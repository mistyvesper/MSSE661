<?php

/**
 * Description of Collection
 * 
 * Stores uploaded documents as collections with each collection being represented
 * by a single database in MySQL. 
 * 
 * @author misty
 */

require_once 'InfoMessage.php';

class Collection
{
    // encapsulate Collection properties by declaring private

    private $collection = []; // array of documents
    private $pendingSharedCollection = []; // array of documents to be shared
    private $sharedCollection = []; // array of documents that have been shared
    private $receivedCollection = []; // array of documents that have been received
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
        
        if (!isset($_SESSION['collection']) || count($_SESSION['collection']) == 0) {
            
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
                
                // close result and database connection

                $result->close();
                $this->db->closeDBConnection();
                
                $_SESSION['collection'] = $this->collection;
                return $this->collection;
            } else {
                return false;
            }
        } else {
            $this->collection = $_SESSION['collection'];
        }  
    }
    
    // function to get received collection
    
    public function getReceivedDocuments() {
        
        if (!isset($_SESSION['receivedCollection'])) {
            
            // reset received collection array
        
            $this->receivedCollection = [];

            // open database connection

            $this->con = $this->db->getDBConnection();

            // check for errors

            if (!$this->con->connect_error) {

                // get documents

                $query = "CALL usp_getReceivedDocumentsByUser('$this->collectionUser');";
                $result = mysqli_query($this->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->receivedCollection[] = $row;
                }
                
                // close result and database connection

                $result->close();
                $this->db->closeDBConnection();
                
                $_SESSION['receivedCollection'] = $this->receivedCollection;
                return $this->receivedCollection;
            } else {
                return false;
            }

            
        } else {
            $this->receivedCollection = $_SESSION['receivedCollection'];
        } 
    }
    
    // function to get received documents by message
    
    public function getReceivedDocumentsByMessage($msgID) {
            
        // reset received collection array

        $this->receivedCollection = [];

        // open database connection

        $this->con = $this->db->getDBConnection();

        // check for errors

        if (!$this->con->connect_error) {

            // get documents

            $query = "CALL usp_getReceivedDocumentsByMsgID('$msgID');";
            $result = mysqli_query($this->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->receivedCollection[] = $row;
            }

            // close result and database connection

            $result->close();
            $this->db->closeDBConnection();

            $_SESSION['receivedCollection'] = $this->receivedCollection;
        } else {
            return false;
        } 
    }
    
    // function to get shared documents
    
    public function getPendingSharedDocuments() {
        
        if (!isset($_SESSION['pendingSharedCollection'])) {
            
            // reset received collection array
        
            $this->pendingSharedCollection = [];

            // open database connection

            $this->con = $this->db->getDBConnection();

            // check for errors

            if (!$this->con->connect_error ) {

                // get documents

                $query = "CALL usp_getPendingSharedDocumentsByUser('$this->collectionUser');";
                $result = mysqli_query($this->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->pendingSharedCollection[] = $row;
                }
                
                // close result and database connection

                $result->close();
                $this->db->closeDBConnection();
                
                $_SESSION['receivedCollection'] = $this->pendingSharedCollection;
                return $this->pendingSharedCollection;
            } else {
                return false;
            }

            
        } else {
            $this->pendingSharedCollection = $_SESSION['pendingSharedCollection'];
        } 
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
            
            // close result and database connection
        
            $result->close();
            $this->db->closeDBConnection();
            
            return $this->docTypes;
        } else {
            return false;
        }
        
        
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
            
            // close result and close database connection
        
            $result->close();
            $this->db->closeDBConnection();
            
            if ($documentID) {
                return $documentID;
            } else {
                return 0;
            }
        } else {
            return false;
        }
    }
    
    // function to get document by index
    
    public function getDocumentByIndex($index) {
        
        // reload documents
        
        $this->getDocuments();
        
        // iterate through collection to find document
        
        foreach ($this->collection as $key => $document) {
            if ($key == $index) {
                return $document;
                break;
            } 
        }
        
        return false;
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
            
            if ($statement->error == '') {
                unset($_SESSION['collection']);
                $_SESSION['collection'] = $this->getDocuments(); // update collection array
            } else {
                return false;
            }
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // fucntion to add document to received collection
    
    public function addReceivedDocument($sharedDocument, $sharedWith, $msgID) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_insertReceivedDocument(?, ?, ?, ?, ?, ?, ?);");
            $statement->bind_param('ssssssi', $title, $type, $extension, $size, $receivedBy, $receivedFrom, $messageID);
            
            // get document properties
        
            $title = $sharedDocument['title'];
            $type = $sharedDocument['type'];
            $extension = strtoupper($sharedDocument['extension']);
            $size = $sharedDocument['size'];
            $receivedBy = $sharedWith;
            $receivedFrom = $this->collectionUser;
            $messageID = $msgID;
        
            // add document
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                return false;
            } 
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to add document to shared documents
    // note that the document is "sent" to another user until it has been updated with a sharedWith value (see shareDocuments function)
    
    public function addSharedDocument($sharedDocument) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_insertSharedDocument(?, ?, ?, ?, ?);");
            $statement->bind_param('sssss', $title, $type, $extension, $size, $sharedBy);
            
            // get document properties
        
            $title = $sharedDocument['title'];
            $type = $sharedDocument['type'];
            $extension = $sharedDocument['extension'];
            $size = $sharedDocument['size'];
            $sharedBy = $this->collectionUser;
        
            // add document
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error == '') {
                unset($_SESSION['pendingSharedCollection']);
                $_SESSION['pendingSharedCollection'] = $this->getPendingSharedDocuments();
            } else {
                return false;
            }
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // function to share documents
    
    public function shareDocuments($subject, $body, $shareWith, $sharedDocuments, $sharedDate) {
        
        $msg = new Message($subject, $body, $shareWith, $this->collectionUser, $sharedDate, $this->db);
        
        if ($msg->sendMessage() && $msg->getMessageID()) {
            $msgID = $msg->getMessageID();
            foreach($sharedDocuments as $sharedDocument) {
                if (!$this->addReceivedDocument($sharedDocument, $shareWith, $msgID)) {
                    return false;
                }
            }
            
            if (!$this->updateSharedDocuments($sharedDocuments, $shareWith, $sharedDate, $msgID)) {
                return false;
            } else {
                return true;
            }
        }
    }
    
    // function to update document properties (using optional parameters)

    public function updateDocument($document, $newDocType = '', $newDocTitle = '') {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            foreach ($this->collection as $item) {
            
                if (($item === $document) && (($newDocType != '' && $newDocType != $item['type']) 
                        || ($newDocTitle != '' && $newDocTitle != $item['title']))) {

                    // prepare insert statement

                    $statement = $this->con->prepare("CALL usp_updateDocument(?, ?, ?, ?, ?, ?);");
                    $statement->bind_param('ssssss', $oldTitle, $newTitle, $oldType, $newType, $extension, $user);

                    // get document properties

                    $oldType = $item['type'];
                    $newType = $newDocType;
                    $oldTitle = $item['title'];
                    $newTitle = $newDocTitle;
                    $extension = $item['extension'];
                    $lowerExtension = strtolower($extension);
                    $user = $this->collectionUser;
                    $oldFilename = dirname(dirname(__FILE__)) . "/uploadedFiles/$user/$oldType/$oldTitle.$lowerExtension";
                    $newFilename = dirname(dirname(__FILE__)) . "/uploadedFiles/$user/$newType/$newTitle.$lowerExtension";
                    
                    createDirectory(dirname($newFilename));

                    if (rename($oldFilename, $newFilename)) {
                        
                        // update document

                        $statement->execute();

                        // check for errors

                        if ($statement->error != '') {
                            rename($newFilename, $oldFilename);
                            return false;
                            
                        } else {
                            unset($_SESSION['collection']);
                            $_SESSION['collection'] = $this->getDocuments();
                        } 
                    }  
                }
            }  
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to update shared documents
    
    public function updateSharedDocuments($sharedDocuments, $sharedWith, $sharedDate, $msgID) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (count($sharedDocuments) > 0 && checkForExistingAccount($sharedWith, $this->db) && !$this->con->connect_error) {
            
            foreach($sharedDocuments as $sharedDocument) {
                
                // prepare insert statement

                $statement = $this->con->prepare("CALL usp_updateSharedDocument(?, ?, ?, ?, ?, ?, ?);");
                $statement->bind_param('ssssssi', $docTitle, $docType, $docExtension, $docSharedWithUser, $docSharedByUser, $docSharedDate, $docMessageID);

                // get document properties

                $docType = $sharedDocument['type'];
                $docTitle = $sharedDocument['title'];
                $docExtension = strtoupper($sharedDocument['extension']);
                $docSharedByUser = $this->collectionUser;
                $docSharedWithUser = $sharedWith;
                $docSharedDate = $sharedDate;
                $docMessageID = $msgID;
                
                mysqli_query($this->con, "CALL usp_updateSharedDocument('$docTitle', '$docType', '$docExtension', '$docSharedWithUser', '$docSharedByUser', '$docSharedDate', '$docMessageID');");

                $statement->execute();

                // check for errors

                if ($statement->error != '') {
                    return false;
                }
            }  
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
        
    }

    // function to delete document from collection

    public function deleteDocument($document) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->con->prepare("CALL usp_deleteDocument(?, ?, ?, ?);");
            $statement->bind_param('ssss', $title, $type, $extension, $user);
            
            // get document properties

            $title = $document['title'];
            $type = $document['type'];
            $extension = $document['extension'];
            $lowerExtension = strtolower($extension);
            $user = $this->collectionUser;
            $file = dirname(dirname(__FILE__)) . "/uploadedFiles/$user/$type/$title.$lowerExtension";

            if (file_exists($file)) {
                
                // delete document from database
            
                $statement->execute();

                // check for errors

                if ($statement->error != '') {
                    return false;
                } else {
                    
                    // delete document from file system
                    
                    unlink($file);

                    // reseed documentID

                    $this->seed = mysqli_fetch_array(mysqli_query($this->con, "SELECT MAX(documentID) FROM document;"), MYSQLI_NUM)[0];
                    mysqli_query($this->con, "ALTER TABLE document AUTO_INCREMENT = '$this->seed';");

                    // get documents

                    unset($_SESSION['collection']);
                    $_SESSION['collection'] = $this->getDocuments();
                }
            } else {
                return false;
            }

        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to delete document from received collection

    public function deleteReceivedDocument($receivedDocument) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->con->prepare("CALL usp_deleteReceivedDocument(?, ?, ?, ?, ?);");
            $statement->bind_param('sssss', $title, $type, $extension, $receivedBy, $receivedFrom);
            
            // get shared document properties

            $title = $receivedDocument['title'];
            $type = $receivedDocument['type'];
            $extension = $receivedDocument['extension'];
            $lowerExtension = strtolower($extension);
            $receivedBy = $this->collectionUser;
            $receivedFrom = $receivedDocument['receivedFrom'];
                
            // delete received document

            $statement->execute();

            // check for errors

            if ($statement->error != '') {
                return false;
            } else {

                // reseed receivedDocumentID

                $this->seed = mysqli_fetch_array(mysqli_query($this->con, "SELECT MAX(receivedDocumentID) FROM receivedDocument;"), MYSQLI_NUM)[0];
                mysqli_query($this->con, "ALTER TABLE receivedDocument AUTO_INCREMENT = '$this->seed';");

                // get received documents

                unset($_SESSION['receivedCollection']);
                $_SESSION['receivedCollection'] = $this->getReceivedDocuments();
            }

        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // function to delete pending shared document
    
    public function deletePendingSharedDocument($sharedDocument) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->con->prepare("CALL usp_deletePendingSharedDocument(?, ?, ?, ?);");
            $statement->bind_param('ssss', $title, $type, $extension, $sharedBy);
            
            // get shared document properties

            $title = $sharedDocument['title'];
            $type = $sharedDocument['type'];
            $extension = $sharedDocument['extension'];
            $lowerExtension = strtolower($extension);
            $sharedBy = $this->collectionUser;
                
            // delete received document

            $statement->execute();

            // check for errors

            if ($statement->error != '') {
                return false;
            } else {

                // reseed receivedDocumentID

                $this->seed = mysqli_fetch_array(mysqli_query($this->con, "SELECT MAX(sharedDocumentID) FROM sharedDocument;"), MYSQLI_NUM)[0];
                mysqli_query($this->con, "ALTER TABLE sharedDocument AUTO_INCREMENT = '$this->seed';");

                // get received documents

                unset($_SESSION['pendingSharedCollection']);
                $_SESSION['pendingSharedCollection'] = $this->getPendingSharedDocuments();
            }

        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        
    }
    
    // function to search collection
    
    public function searchCollection($searchValue) {
        
        $this->getDocuments();
        $i = 0;
        $searchValueLength = strlen($searchValue);

        foreach($this->collection as $document) {

            $type = strtolower($document['type']);
            $title = strtolower($document['title']);
            $extension = strtolower($document['extension']);
            $size = strtolower($document['size']);
            $uploadDate = strtolower($document['uploadDate']);

            if (!strpos($type, $searchValue) && substr($type, 0, $searchValueLength) != $searchValue
                    && !strpos($title, $searchValue) && substr($title, 0, $searchValueLength) != $searchValue
                    && !strpos($extension, $searchValue) && substr($extension, 0, $searchValueLength) != $searchValue
                    && !strpos($size, $searchValue) && substr($size, 0, $searchValueLength) != $searchValue
                    && !strpos($uploadDate, $searchValue) && substr($uploadDate, 0, $searchValueLength) != $searchValue) {
                unset($this->collection[$i]);
            }

            $i++;
        }
        
        $_SESSION['collection'] = $this->collection;
    }
    
    // function to sort collection by type
    // https://secure.php.net/manual/en/function.array-multisort.php
    
    public function sortCollection($arrayKey, $ascending) {
        
        // get documents
        
        $this->getDocuments();
            
        // sort documents

        foreach ($this->collection as $key => $row) {
            $data[$key] = strtolower($row[$arrayKey]);
        }

        if ($ascending) {
            array_multisort($data, SORT_DESC, $this->collection);
        } else {
            array_multisort($data, SORT_ASC, $this->collection);
        }

        $_SESSION['collection'] = $this->collection;
    }
    
    // function to sort shared collection
    // https://secure.php.net/manual/en/function.array-multisort.php
    
    public function sortSharedCollection($arrayKey, $ascending) {
        
        // get shared documents
        
        $this->getSharedDocuments();
            
        // sort documents

        foreach ($this->pendingSharedCollection as $key => $row) {
            $data[$key] = strtolower($row[$arrayKey]);
        }

        if ($ascending) {
            array_multisort($arrayKey, SORT_DESC, $this->pendingSharedCollection);
        } else {
            array_multisort($arrayKey, SORT_ASC, $this->pendingSharedCollection);
        }

        $_SESSION['pendingSharedCollection'] = $this->pendingSharedCollection;
    }
  
    // function to display collection in table
            
    public function showCollection() {
        
        // check for existing documents

        if (count($this->collection) == 0) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
            
            $docTypes = $this->getDocumentTypes();
        
            // iterate through Collection array to display Documents

            echo "<form  method='post' action='index.php' enctype='multipart/form-data'><div id='nojavascript'><table><tr>"
                    . "<th align='center' style='width:75px'><input type='submit' name='select' value='Select' style='width:75px'></th>"
                    . "<th align='left' style='width:150px'><input type='submit' name='sortDoc' value='Type' style='width:150px'></th>"
                    . "<th align='left' style='width:350px'><input type='submit' name='sortDoc' value='Title' style='width:350px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='sortDoc' value='Extension' style='width:75px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='sortDoc' value='File Size' style='width:75px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sortDoc' value='Upload Date' style='width:100px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='download' value='Download' style='width:75px'></th></tr>";
            
            foreach ($this->collection as $key => $document) {
                
                $type = $document['type'];
                $title = $document['title'];
                $extension = strtolower($document['extension']);

                echo "<tr><td align='center'><input type='checkbox' name='document[]' value='$key'></td>"
                        . "<td><select name='docType[]' style='width:150px'>";
                
                
                foreach ($docTypes AS $docType) {
                    if ($docType == $document['type']) {
                        echo "<option value='$docType' selected='selected'>$docType</option>";
                    } else {
                        echo "<option value='$docType'>$docType</option>";
                    } 
                }
                
                 echo "</select></td><td><input type='text' style='width:350px' name='docTitle[]' value='"
                        . $document['title'] . "'></td><td align='center'>"
                        . $document['extension'] . "</td><td align='center'>"
                        . $document['size'] . "</td><td align='center'>"
                        . $document['uploadDate'] . "</td>"
                        . "<td align='center'><a href='/uploadedFiles/$this->collectionUser/$type/$title.$extension'>Download</a></td></tr>";
            }

            echo "</table></div>";
            echo "<br><div><table><tr><td><input type='submit' name='share' value='Share'></td><td></td>"
                . "<td><input type='submit' name='updateDoc' value='Update'></td><td></td>"
                . "<td><input type='submit' name='delete' value='Delete'></td></tr></table></div></form>";
        }
    }
    
    // function to display received collection in table
            
    public function showReceivedCollection() {
        
        // check for existing documents

        if ($this->getReceivedDocumentCount() == 0) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
        
            // iterate through shared Collection array to display Documents

            echo "<form  method='post' action='index.php' enctype='multipart/form-data'><div id='nojavascript'><table><tr>"
                    . "<th align='center' style='width:75px'><input type='submit' name='select' value='Select' style='width:75px'></th>"
                    . "<th align='left' style='width:150px'><input type='submit' name='type' value='Type' style='width:150px'></th>"
                    . "<th align='left' style='width:350px'><input type='submit' name='title' value='Title' style='width:350px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='extension' value='Extension' style='width:75px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='size' value='File Size' style='width:75px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='sharedBy' value='From' style='width:75px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sharedDate' value='Shared Date' style='width:100px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='download' value='Download' style='width:75px'></th></tr>";
            
            foreach ($this->receivedCollection as $key => $receivedDocument) {
                
                $type = $receivedDocument['type'];
                $title = $receivedDocument['title'];
                $extension = $receivedDocument['extension'];
                $size = $receivedDocument['size'];
                $sharedBy = $receivedDocument['receivedFrom'];
                $sharedDate = $receivedDocument['dateReceived'];
                $url = "/uploadedFiles/$sharedBy/$type/$title.$extension";

                echo "<tr><td align='center'><input type='checkbox' name='document[]' value='$key'></td>"
                        . "<td>$type</td>"
                        . "<td>$title</td>"
                        . "<td align='center'>$extension</td>"
                        . "<td align='center'>$size</td>"
                        . "<td align='center'>$sharedBy</td>"
                        . "<td align='center'>$sharedDate</td>"
                        . "<td align='center'><a href='$url'>Download</a></td></tr>";
            }

            echo "</table></div>";
            echo "<br><div><table><tr><td><input type='submit' name='add' value='Add to My Collection'></td><td></td>"
                . "<td><input type='submit' name='delete' value='Delete'></td></tr></table></div></form>";
        }
    }
    
    // function to show shared Collection
            
    public function showSharedCollection() {
        
        // check for existing documents

        if ($this->getPendingSharedDocumentCount() == 0) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
        
            // iterate through shared Collection array to display Documents

            echo "<form  method='post' action='index.php' enctype='multipart/form-data'>"
                    . "<div><h4>Share With</h4>"
                    . "<input type='text' name='shareWithUser'>"
                    . "<h4>Subject</h4>"
                    . "<input type='text' name='subject' style='width:745px'>"
                    . "<h4>Message</h4>"
                    . "<input type='text' name='body' style='width:745px;height:75px'></div>"
                    . "<div><h4>Attachments</h4>"
                    . "<table><tr>"
                    . "<th align='center' style='width:75px'><input type='submit' name='select' value='Select' style='width:75px'></th>"
                    . "<th align='left' style='width:150px'><input type='submit' name='type' value='Type' style='width:150px'></th>"
                    . "<th align='left' style='width:350px'><input type='submit' name='title' value='Title' style='width:350px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='extension' value='Extension' style='width:75px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='size' value='File Size' style='width:75px'></th></tr>";
            
            foreach ($this->pendingSharedCollection as $key => $sharedDocument) {
                
                $type = $sharedDocument['type'];
                $title = $sharedDocument['title'];
                $extension = strtolower($sharedDocument['extension']);
                $size = $sharedDocument['size'];

                echo "<tr><td align='center'><input type='checkbox' name='document[]' value='$key'></td>"
                        . "<td>$type</td>"
                        . "<td>$title</td>"
                        . "<td align='center'>$extension</td>"
                        . "<td align='center'>$size</td>"
                        . "<td align='center'>$sharedBy</td></tr>";
            }

            echo "</table></div>";
            echo "<div><table><tr>"
                . "<td style='width:75px'><input type='submit' name='deletePendingSharedDocs' value='Delete' style='width:75px'></td>"
                . "<td></td><td style='width:75px'><input type='submit' name='addPendingSharedDocs' value='Add' style='width:75px'></td></tr></table></div>"
                . "<br><br><div><table><tr>"
                . "<td style='width:75px'><input type='submit' name='cancelShare' value='Cancel' style='width:75px'></td>"
                . "<td></td><td style='width:75px'><input type='submit' name='send' value='Send' style='width:75px'></td></tr></table></div></form>";
        }
    }
    
    // function to show upload form
    
    public function showUploadForm($index) {     
        
        $docTypes = $this->getDocumentTypes();

        echo "<tr>
                    <td><select name='docType[]'>";
    
        foreach ($docTypes AS $docType) {
            echo "<option value='$docType'>$docType</option>";
        }

        echo "</select>
                    </td>
                    <td><input type='file' name='$index'[]' accept='.doc, .docx, .txt, .pdf'></td>
                </tr>";
    }
    
    // function to get document count
    
    public function getDocumentCount() {
        $this->getDocuments();
        return count($this->collection);
    }
    
    // function to get received document count
    
    public function getReceivedDocumentCount() {
        $this->getReceivedDocuments();
        return count($this->receivedCollection);
    }
    
    // function to get shared document count
    
    public function getPendingSharedDocumentCount() {
        $this->getPendingSharedDocuments();
        return count($this->pendingSharedCollection);
    }
}