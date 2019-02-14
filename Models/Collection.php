<?php

/**
 * Description of Collection
 * 
 * Stores uploaded documents as collections with each collection being represented
 * by a single database in MySQL. 
 * 
 * @author misty
 */

require_once 'Message.php';

class Collection
{
    // encapsulate Collection properties by declaring private

    private $collection = []; // array of documents
    private $docTypes = []; // array of document types
    private $db; // database
    private $con; // Database connection
    private $seed; // for reseeding AUTO_INCREMENT
    private $collectionUser; // for displaying user-specific documents
    
    // constructor requires user and database connection to instantiate
    
    public function __construct($user, $database) {
        $this->collectionUser = $user;
        $this->db = $database;
    }

    // function to get collection array

    public function getDocuments() { 
        
        // reset collection array
        
        $this->collection = [];
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // get documents
        
            $query = "CALL usp_getDocumentsByUser('$this->collectionUser');";
            $result = mysqli_query($this->con, $query);
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
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // get document types
        
            $query = "CALL usp_getDocumentTypes;";
            $result = mysqli_query($this->con, $query);
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
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // get document details
        
        $title = $document->getDocumentTitle();
        $type = $document->getDocumentType();
        $extension = $document->getDocumentExtension();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // get document ID
        
            $query = "CALL usp_getDocumentID('$title', '$type', '$extension', '$this->collectionUser');";
            $result = mysqli_query($this->con, $query);
            $documentID = mysqli_fetch_array($result, MYSQLI_NUM)[0];
            if ($documentID) {
                return $documentID;
            } else {
                return 0;
            }
        }
        
        // close result and close database connection
        
        $result->close();
        $db->closeDBConnection();
    }

    // function to add document to collection

    public function addDocument($document) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_insertDocument(?, ?, ?, ?, ?);");
            $statement->bind_param('sssss', $title, $type, $extension, $size, $user);
            
            // get document properties
        
            $title = $document->getDocument()['title'];
            $type = $document->getDocument()['type'];
            $extension = $document->getDocument()['extension'];
            $size = $document->getDocument()['size'];
            $user = $this->collectionUser;
        
            // add document
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->affected_rows > 0) {
                $this->getDocuments(); // update collection array
            }
        } 
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // function to update document properties (using optional parameters)

    public function updateDocument($docType = '', $docTitle = '') {

        // check if document type is empty string or changed and update

        if ($docType != '' && $docType != $this->document['type']) {
            $this->setDocumentType($docType);
        } 

        // check if document title is empty string or changed and update

        if ($docTitle != '' && $docTitle != $this->document['title']) {
            $this->setDocumentTitle($docTitle);
        }
    }

    // function to delete document from collection

    public function deleteDocument($document) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_deleteDocument(?, ?, ?, ?);");
            $statement->bind_param('ssss', $title, $type, $extension, $user);
            
            // get document properties

            $title = $document->getDocument()['title'];
            $type = $document->getDocument()['type'];
            $extension = $document->getDocument()['extension'];
            $user = $this->collectionUser;

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