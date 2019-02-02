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

    private $collection = [];
    private $db;
    private $seed;
    
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
        
        // get documents
        
        $query = "CALL usp_getDocumentsByUser('Regis');";
        $result=mysqli_query($this->db->getDBConnection(),$query);
        while ($row = mysqli_fetch_assoc($result)) {
            $this->collection[] = $row;
        }
        return $this->collection;
        
        // close database connection 
        
        $this->db->closeDBConnection();
    }

    // function to add document to collection

    public function addDocument($document) {
        
        // open database connection
        
        $this->db->openDBConnection();

        // add document
            
        $title = $document->getDocument()['title'];
        $type = $document->getDocument()['type'];
        $extension = $document->getDocument()['extension'];
        $size = $document->getDocument()['size'];
        mysqli_query($this->db->getDBConnection(), "CALL usp_insertDocument('$title', '$type', '$extension', '$size', 'Regis');");
        
        // update collection array
        
        $this->getDocuments();
        
        // close database connection
        
        $this->db->closeDBConnection();
    }

    // function to delete document from collection

    public function deleteDocument($document) {
        
        // open database connection
        
        $this->db->openDBConnection();
        
        // get document title

        $title = $document->getDocument()['title'];
        $type = $document->getDocument()['type'];
        $extension = $document->getDocument()['extension'];
        
        // delete document and reseed document table
        
        mysqli_query($this->db->getDBConnection(), "CALL usp_deleteDocument('$title', '$type', '$extension', 'Regis');");
        $this->seed = mysqli_fetch_array(mysqli_query($this->db->getDBConnection(), "SELECT MAX(documentID) FROM document;"), MYSQLI_NUM)[0];
        mysqli_query($this->db->getDBConnection(), "ALTER TABLE document AUTO_INCREMENT = '$this->seed';");
        
        // get documents
        
        $this->getDocuments();
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // function to display collection in table
            
    public function showCollection() {

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