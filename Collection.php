<?php

/*
 * Copyright (C) 2019 misty
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'DBConnection.php';
require_once 'Message.php';

/**
 * Description of Collection
 * 
 * Stores uploaded documents as collections with each collection being represented
 * by a single database in MySQL. 
 * 
 * @author misty
 */
class Collection
{
    // encapsulate Collection properties by declaring private

    private $collection = []; // array of documents
    private $docTypes = []; // array of document types
    private $db; // DBConnection instance
    private $con; // DBConnection connection
    private $seed; // for reseeding AUTO_INCREMENT
    
    // constructor requires database connection to instantiate
    
    public function __construct($dbConnection) {
        $this->db = $dbConnection;    
    }

    // function to get collection array

    public function getDocuments() { 
        
        // reset collection array
        
        $this->collection = [];
        
        // open database connection
        
        $this->db->openDBConnection();
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if ($this->con->connect_error ) {
            
            // create new error message
            
            $error = new Message();
            $error->dbConnectError();
            unset($error);
        } else {
            
            // get documents
        
            $query = "CALL usp_getDocumentsByUser('Regis');";
            $result = mysqli_query($this->con,$query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->collection[] = $row;
            }
            return $this->collection;
        }
        
        // close result and database connection 
        
        $result->close();
        $this->db->closeDBConnection();
    }
    
    // function to get document types
    
    public function getDocumentTypes() {
        
        // open database connection
        
        $this->db->openDBConnection();
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if ($this->con->connect_error ) {
            
            // create new error message
            
            $error = new Message();
            $error->dbConnectError();
            unset($error);
        } else {
            
            // get document types
        
            $query = "CALL usp_getDocumentTypes;";
            $result = mysqli_query($this->con,$query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->docTypes[] = $row['typeDescription'];
            }
            return $this->docTypes;
        }
        
        // close result and database connection 
        
        $result->close();
        $this->db->closeDBConnection();
    }
    
    // function to get document ID
    
    public function getDocumentID($document) {
        
        $title = $document->getDocumentTitle();
        $type = $document->getDocumentType();
        $extension = $document->getDocumentExtension();
        $user = 'Regis';
        
        // open database connection
        
        $this->db->openDBConnection();
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if ($this->con->connect_error ) {
            
            // create new error message
            
            $error = new Message();
            $error->dbConnectError();
            unset($error);
        } else {
            
            // get document ID
        
            $query = "CALL usp_getDocumentID('$title', '$type', '$extension', '$user');";
            $documentID = mysqli_fetch_array(mysqli_query($this->con, $query), MYSQLI_NUM)[0];
            if ($documentID) {
                return $documentID;
            } else {
                return 0;
            }
        }
        
        // close database connection 
        
        $this->db->closeDBConnection();
    }

    // function to add document to collection

    public function addDocument($document) {
        
        // open database connection
        
        $this->db->openDBConnection();
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if ($this->con->connect_error ) {
            
            // create new error message 
            
            $error = new Message();
            $error->dbConnectError();
            unset($error);
        } else {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_insertDocument(?, ?, ?, ?, ?);");
            $statement->bind_param('sssss', $title, $type, $extension, $size, $user);
            
            // get document properties
        
            $title = $document->getDocument()['title'];
            $type = $document->getDocument()['type'];
            $extension = $document->getDocument()['extension'];
            $size = $document->getDocument()['size'];
            $user = 'Regis';
        
            // add document
        
            $statement->execute();
            
            // check for errors
            
            if (!$statement->affected_rows <= 0) {
                $this->getDocuments(); // update collection array
            }
          
            // close database connection

            $this->db->closeDBConnection();
        } 
    }

    // function to delete document from collection

    public function deleteDocument($document) {
        
        // open database connection
        
        $this->db->openDBConnection();
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if ($this->con->connect_error ) {
            
            // create new error message
            
            $error = new Message();
            $error->dbConnectError();
            unset($error);
        } else {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_deleteDocument(?, ?, ?, ?);");
            $statement->bind_param('ssss', $title, $type, $extension, $user);
            
            // get document properties

            $title = $document->getDocument()['title'];
            $type = $document->getDocument()['type'];
            $extension = $document->getDocument()['extension'];
            $user = 'Regis';

            // delete document
            
            $statement->execute();
            
            // check for errors
            
            if ($statement->affected_rows <= 0) {
                
                // create new error message
                
                $error = new Message();
                $error->dbDeleteError();
                unset($error);
            } else {
                
                // reseed documentID
                
                $this->seed = mysqli_fetch_array(mysqli_query($this->con, "SELECT MAX(documentID) FROM document;"), MYSQLI_NUM)[0];
                mysqli_query($this->con, "ALTER TABLE document AUTO_INCREMENT = '$this->seed';");
                
                // get documents

                $this->getDocuments();
            }
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // function to display collection in table
            
    public function showCollection() {
        
        // get documents
        
        $this->getDocuments();
        
        // check for existing documents

        if (count($this->collection) == 0) {
            
            // create new error message
            
            $error = new Message();
            $error->dbNoRecords();
            unset($error);
        } else {
            
            // iterate through Collection array to display Documents

            echo "<div><table><tr>"
                    . "<th align=\"left\" style=\"width:50px\">Select</th>"
                    . "<th align=\"left\" style=\"width:150px\">Type</th>"
                    . "<th align=\"left\" style=\"width:350px\">Title</th>"
                    . "<th align=\"left\" style=\"width:75px\">Extension</th>"
                    . "<th align=\"left\" style=\"width:75px\">File Size</th>"
                    . "<th align=\"left\" style=\"width:100px\">Upload Date</th>"
                    . "<th align=\"left\" style=\"width:75px\">Update</th></tr>";

            foreach ($this->collection as $document) {

                echo '<tr><td><input type="checkbox"></td><td><input type="text" value="' 
                        . $document['type'] . '"></td><td><input type="text" style="width:350px" value="'
                        . $document['title'] . '"></td><td>'
                        . $document['extension'] . '</td><td>'
                        . $document['size'] . '</td><td>'
                        . $document['uploadDate'] . '</td><td>'
                        . '<button>Update</button></td><td></tr>';
            }

            echo "</table></div>";
        }
    }
    
    // function to get document count
    
    public function getDocumentCount() {
        $this->getDocuments();
        return count($this->collection);
    }
}