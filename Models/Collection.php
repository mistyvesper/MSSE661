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
    private $seed; // for reseeding AUTO_INCREMENT
    private $collectionUser; // for displaying user-specific documents
    private $publicCollection = [];
    private $publicCollectionDocs = [];
    private $pendingPublicCollectionDocs = [];
    
    // constructor requires user and database connection to instantiate
    
    public function __construct($user, $database) {
        $this->collectionUser = $user;
        $this->db = $database;
    }
    
    
    /************************ MY DOCUMENTS METHODS ************************/

    // function to get collection array

    public function getDocuments() { 
        
        // check if session variable or search values already set
        
        if ((!isset($_SESSION['collection']) && !isset($_POST['search']) && !isset($_SESSION['searchValue'])) 
                || (count($_SESSION['collection']) == 0 && !isset($_SESSION['searchValue']) && !isset($_POST['search']))) {
            
            // reset collection array
        
            $this->collection = [];

            // open database connection

            $this->db->getDBConnection();

            // check for errors

            if (!$this->db->con->connect_error ) {

                // get documents

                $query = "CALL usp_getDocumentsByUser('$this->collectionUser');";
                $result = mysqli_query($this->db->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->collection[] = $row;
                }
                
                // close result and database connection

                if ($result && isset($this->collection)) {
                    $result->close();
                }
                $this->db->closeDBConnection();
                
                // return collection
                
                if (isset($this->collection)) {
                    $_SESSION['collection'] = $this->collection;
                    return $this->collection;
                } else {
                    $_SESION['displayMessage'] = InfoMessage::dbNoRecords();
                    return false;
                }
            } else {
                
                // close database connection and display error
                
                $this->db->closeDBConnection();
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            }
        } else {
            $this->collection = $_SESSION['collection'];
            return $this->collection;
        }  
    }
    
    // function to get document by document ID
    
    public function getDocumentByID($docID) {
        
        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get document

            $query = "CALL usp_getDocumentByDocID('$docID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $document = $row;
            }
        } else {
            
            // return database error 
            
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        }

        // close result and database connection

        if ($result && isset($document)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return document
        
        if (isset($document)){
            return $document;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return false;
        }
    }
    
    // function to get document ID
    
    public function getDocumentID($document) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // get document details
        
        $title = $document->getDocumentTitle();
        $type = $document->getDocumentType();
        $extension = $document->getDocumentExtension();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // get document ID
        
            $query = "CALL usp_getDocumentID('$title', '$type', '$extension', '$this->collectionUser');";
            $result = mysqli_query($this->db->con, $query);
            $documentID = mysqli_fetch_array($result, MYSQLI_NUM)[0];
            
            // close result and close database connection
        
            if ($result && isset($documentID)) {
                $result->close();
            }
            $this->db->closeDBConnection();
            
            // return document ID
            
            if ($documentID) {
                return $documentID;
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return 0;
            }
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }
    }
    
    // function to get document types
    
    public function getDocumentTypes() {
        
        if (isset($this->docTypes)) {
            unset($this->docTypes);
        }

        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // get document types
        
            $query = "CALL usp_getDocumentTypes;";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->docTypes[] = $row['typeDescription'];
            }
            
            // close result and database connection
        
            if ($result && isset($this->docTypes)) {
                $result->close();
            }
            $this->db->closeDBConnection();
            
            // return document types
            
            if (isset($this->docTypes)) {
                return $this->docTypes;
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return false;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        } 
    }

    // function to add document to collection

    public function addDocument($document) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_insertDocument(?, ?, ?, ?, ?);");
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
                $_SESSION['displayMessage'] = InfoMessage::documentsNotAdded();
                $this->db->closeDBConnection();
                return false;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // function to upload documents 
    
    public function uploadDocuments($userDirectory) {
        
        $i = 0;
        unset($_SESSION['collection']);
        unset($_SESSION['displayMessages']);
        $this->getDocuments();
        $documentCount = $this->getDocumentCount();
        $docUploadDate = (string)date("Y/m/d h:i:s");
        $error = false;
        $_SESSION['fileUploadCount'] = 1;

        // move files

        foreach ($_FILES as $file) {

            // get docType after form submission

            $docType = $_POST['docType'][$i];

            // check for errors 

            if ($file['name'] == '') {
                continue;
            } else if ($file['error'] == 1 || $file['error'] == 2 || $file['size'] > 26214400) {
                $_SESSION['displayMessages'][] = InfoMessage::fileExceedsMaxSize($file['name']);
                $_SESSION['fileUploadCount'] = 1;
            } else if ($file['error'] == 0 || $docType != '' && ($file['error'] != 1 || $file['error'] != 2 || $file['size'] < 26214400)) {

                $newDocTypeDirectory = $userDirectory . $docType . '/';
                $sanitizedFileName = sanitizeString($file['name']);
                $uploadPath = $newDocTypeDirectory . $sanitizedFileName;
                $docTitle = pathinfo($uploadPath)['filename'];

                // get file extension

                $docExtension = getFileExtensionByType($file['type']);

                // get file size

                $docSize = getFriendlyFileSize($file['size']);

                // create Document

                $document = new Document($docType, $docTitle, $docExtension, $docSize, $docUploadDate);

                // add Document to Collection

                $this->addDocument($document);

                // check if document successfully added to database

                if ($this->getDocumentCount() == $documentCount + 1) {

                    // create document type directory if it doesn't already exist

                    if (!file_exists($newDocTypeDirectory)) {
                        $old_umask = umask(0);
                        mkdir($newDocTypeDirectory, 0777);
                        umask($old_umask);
                    }

                    // upload file to temporary folder and then move to user folder

                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $error = false;
                    } else {
                        $error = true;
                        $_SESSION['displayMessages'][] = InfoMessage::fileUploadFailed($file['name']);
                        $_SESSION['fileUploadCount'] = 1;
                    }
                } else if ($this->getDocumentID($document) > 0) {
                    $error = true;
                    $_SESSION['displayMessages'][] = InfoMessage::fileDuplicate($file['name']);
                    $_SESSION['fileUploadCount'] = 1;
                } else if ($docExtension == '') {
                    $error = true;
                    $_SESSION['displayMessages'][] = InfoMessage::fileUnsupported($file['name']);
                    $_SESSION['fileUploadCount'] = 1;
                } else {
                    $error = true;
                    $_SESSION['displayMessages'][] = InfoMessage::fileUploadFailed($file['name']);
                    $_SESSION['fileUploadCount'] = 1;
                }
            } else if ($file['error'] == 1 || $file['error'] == 2 || $file['size'] > 26214400) {
                $error = true;
                $_SESSION['displayMessages'][] = InfoMmessage::fileExceedsMaxSize($file['name']);
                $_SESSION['fileUploadCount'] = 1;
            } else if ($file['error'] > 3) {
                $error = true;
                $_SESSION['displayMessages'][] = InfoMessage::fileUploadFailed($file['name']);
                $_SESSION['fileUploadCount'] = 1;
            } 

            $i++;
        }
        
        if (!$error && $documentCount == $this->getDocumentCount()) {
            $_SESSION['displayMessage'][] = InfoMessage::fileNoFilesSelected();
        } else if (!$error) {
            $_SESSION['displayMessage'][] = InfoMessage::fileUploadSuccessful();
            header('Refresh: 0; URL=index.php');
        }
    }
    
    // function to update documents 
    
    public function updateDocuments() {
        
        // update documents and initialize error/updateCount
        
        $this->getDocuments();
        $error = false;
        $updateCount = 0;
        
        // iterate through documents and check for updates
        
        foreach ($_SESSION['collection'] as $key => $document) {
            $newType = $_POST['docType'][$key];
            $newTitle = $_POST['docTitle'][$key];
            
            if (($document['type'] != '' && $document['type'] != $newType) || ($document['title'] != '' && $document['title'] != $newTitle)) {
                if (!$this->updateDocument($document, $newType, $newTitle)) {
                    $error = true;
                } else {
                    $updateCount++;
                }
            }
        }
        
        // check for errors
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::documentsNotUpdated();
        } else if ($updateCount > 0) {
            $_SESSION['displayMessage'] = InfoMessage::documentsUpdated();
        } 
    }
    
    // function to update document properties (using optional parameters)

    public function updateDocument($document, $newDocType = '', $newDocTitle = '') {
        
        // get documents
        
        $this->getDocuments();

        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            foreach ($this->collection as $item) {
                
                // check for changes
            
                if (($item === $document) && (($newDocType != '' && $newDocType != $item['type']) 
                        || ($newDocTitle != '' && $newDocTitle != $item['title']))) {

                    // prepare insert statement

                    $statement = $this->db->con->prepare("CALL usp_updateDocument(?, ?, ?);");
                    $statement->bind_param('iss', $docID, $newTitle, $newType);

                    // get document properties

                    $docID = $item['documentID'];
                    $oldType = $item['type'];
                    $newType = $newDocType;
                    $oldTitle = $item['title'];
                    $newTitle = $newDocTitle;
                    $extension = $item['extension'];
                    $lowerExtension = strtolower($extension);
                    $user = $this->collectionUser;
                    $oldFilename = dirname(dirname(__FILE__)) . "/uploadedFiles/$user/$oldType/$oldTitle.$lowerExtension";
                    $newFilename = dirname(dirname(__FILE__)) . "/uploadedFiles/$user/$newType/$newTitle.$lowerExtension";
                    
                    // create new type directory as needed
                    
                    createDirectory(dirname($newFilename));
                    
                    // rename document

                    if (rename($oldFilename, $newFilename)) {
                        
                        // update document

                        $statement->execute();

                        // check for errors

                        if ($statement->error != '') {
                            $_SESSION['displayMessage'] = InfoMessage::documentsNotUpdated(); // todo
                            rename($newFilename, $oldFilename);
                            $this->db->closeDBConnection();
                            return false;
                            
                        } else {
                            unset($_SESSION['collection']);
                            unset($_SESSION['searchValue']);
                            $_SESSION['collection'] = $this->getDocuments();
                            break;
                        } 
                    }  
                }
            }  
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
         $_SESSION['displayMessage'] = InfoMessage::documentsUpdated();
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to delete documents from collection
    
    public function deleteDocuments($documentIDs) {
        
        // initialize error variable
        
        $error = false;
        
        // delete documents
        
        foreach ($documentIDs as $documentID) {
            $document = $this->getDocumentByID($documentID);
            if (!$this->deleteDocument($document)) {
                $error = true;
            }
        }
        
        // check for errors
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::documentsNotDeleted();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::documentsDeleted();
        }
    }
    
    // function to delete document from collection

    public function deleteDocument($document) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->db->con->prepare("CALL usp_deleteDocument(?);");
            $statement->bind_param('i', $docID);
            
            // get document properties

            $docID = $document['documentID'];
            $title = $document['title'];
            $type = $document['type'];
            $extension = $document['extension'];
            $lowerExtension = strtolower($extension);
            $user = $this->collectionUser;
            $file = dirname(dirname(__FILE__)) . "/uploadedFiles/$user/$type/$title.$lowerExtension";
            
            // delete document

            if (file_exists($file)) {
                
                // delete document from database
            
                $statement->execute();

                // check for errors

                if ($statement->error != '') {
                    $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                    $this->db->closeDBConnection();
                    return false;
                } else {
                    
                    // delete document from file system
                    
                    unlink($file);

                    // reseed documentID

                    $this->seed = mysqli_fetch_array(mysqli_query($this->db->con, "SELECT MAX(documentID) FROM document;"), MYSQLI_NUM)[0];
                    mysqli_query($this->db->con, "ALTER TABLE document AUTO_INCREMENT = '$this->seed';");

                    // get documents

                    unset($_SESSION['collection']);
                    unset($_SESSION['searchValue']);
                    $_SESSION['collection'] = $this->getDocuments();
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::documentsNotDeleted();
                $this->db->closeDBConnection();
                return false;
            }

        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::documentsDeleted();
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to search collection
    
    public function searchCollection($searchValue) {
        
        // check if search value "posted" and set collection variables accordingly
        
        if (isset($_POST['searchValue']) && $_POST['searchValue'] != '') {
            
            // set session variables
            
            $this->collection = $_SESSION['searchCollection'];
            $_SESSION['collection'] = $_SESSION['searchCollection'];
            
            // set search values session variables

            $_SESSION['searchValue'][] = $searchValue;
            
        } else {
            
            // get documents and search value length
        
            $searchCollection = $_SESSION['searchCollection'];
            $searchValueLength = strlen($searchValue);

            // search document

            foreach($searchCollection as $key => $document) {

                $type = strtolower($document['type']);
                $title = strtolower($document['title']);
                $extension = strtolower($document['extension']);
                $size = strtolower($document['size']);
                $uploadDate = strtolower($document['uploadDate']);

                // check for matches

                if (!strpos($type, $searchValue) && substr($type, 0, $searchValueLength) != $searchValue
                        && !strpos($title, $searchValue) && substr($title, 0, $searchValueLength) != $searchValue
                        && !strpos($extension, $searchValue) && substr($extension, 0, $searchValueLength) != $searchValue
                        && !strpos($size, $searchValue) && substr($size, 0, $searchValueLength) != $searchValue
                        && !strpos($uploadDate, $searchValue) && substr($uploadDate, 0, $searchValueLength) != $searchValue) {
                    unset($searchCollection[$key]);
                }
            }

            // return filtered result set

            $_SESSION['searchCollection'] = $searchCollection;
            
        }
    }
    
    // function to sort collection by type
    // https://secure.php.net/manual/en/function.array-multisort.php
    
    public function sortCollection($arrayKey, $ascending) {
        
        if (substr($arrayKey, 0, strpos($arrayKey, ' ')) == 'Upload') {
            $arrayKey = 'Upload Date';
        } else {
            $arrayKey = substr($arrayKey, 0, strpos($arrayKey, ' '));
        }
        
        // get sort category 
        
        switch ($arrayKey) {
            case 'Type':
                $sortCategory = 'type';
                break;
            case 'Title':
                $sortCategory = 'title';
                break;
            case 'Ext':
                $sortCategory = 'extension';
                break;
            case 'Size':
                $sortCategory = 'fileSizeInBytes';
                break;
            case 'Upload Date':
                $sortCategory = 'uploadDate';
                break;
        }
        
        // get documents
        
        $this->getDocuments();
            
        // sort documents

        foreach ($this->collection as $key => $row) {
            $data[$key] = strtolower($row[$sortCategory]);
        }
        
        // determine sorting order

        if ($ascending) {
            array_multisort($data, SORT_DESC, $this->collection);
        } else {
            array_multisort($data, SORT_ASC, $this->collection);
        }
        
        // return sorted collection

        $_SESSION['collection'] = $this->collection;
    }
    
    // function to display collection in table
            
    public function showCollection() {
        
        // check for existing documents

        if (count($this->collection) == 0 && !isset($_SESSION['searchCollection'])) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
            
            // check if active search in place
            
            if (isset($_SESSION['searchCollection'])) {
                $collection = $_SESSION['searchCollection'];
            } else {
                $collection = $this->collection;
            }

            // get document types
            
            $docTypes = $this->getDocumentTypes();
        
            // iterate through Collection array to display Documents

            echo "<form class='documents' id='frmMyDocuments' method='post' action='index.php' enctype='multipart/form-data'>
                    <table class='documents compact' id='tblMyDocuments' cellspacing=0>
                        <thead class='documents' id='theadMyDocumentsHeaders'>
                            <tr class='documents' id='trMyDocumentsHeaders'>
                                <th class='form-label-small-center' id='thMyDocumentsSelectHeader'>
                                    <input class='form-submit-small-header-center' id='subMyDocumentsSelectHeader' type='submit' name='select' value='Select' disabled>
                                </th>
                                <th class='form-label-medium-center' id='thMyDocumentsTypeHeader'>
                                    <input class='form-submit-medium-header-center' id='subMyDocumentsTypeHeader' type='submit' name='sortDoc' value='Type &uarr;&darr;'>
                                </th>
                                <th class='form-label-large-left' id='thMyDocumentsTitleHeader'>
                                    <input class='form-submit-large-header-left' id='subMyDocumentsTitleHeader' type='submit' name='sortDoc' value='Title &uarr;&darr;'>
                                </th>
                                <th class='form-label-small-center' id='thMyDocumentsExtensionHeader' align='center'>
                                    <input class='form-submit-small-header-center' id='subMyDocumentsExtensionHeader' type='submit' name='sortDoc' value='Ext &uarr;&darr;'>
                                </th>
                                <th class='form-label-small-center' id='thMyDocumentsFileSizeHeader' align='center'>
                                    <input class='form-submit-small-header-center' id='subMyDocumentsFileSizeHeader' type='submit' name='sortDoc' value='Size &uarr;&darr;'>
                                </th>
                                <th class='form-label-medium-center' id='thMyDocumentsUploadDateHeader' align='center'>
                                    <input class='form-submit-medium-header-center' id='subMyDocumentsUploadDateHeader' type='submit' name='sortDoc' value='Upload Date &uarr;&darr;'>
                                </th>
                                <th class='form-label-small-center' id='thMyDocumentsDownloadHeader' align='center'>
                                    <input class='form-submit-small-header-center' type='submit' name='download' value='Download' disabled>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='documents' id='tbodyMyDocumentsContent'>";
            
            foreach ($collection as $key => $document) {
                
                $documentID = $document['documentID'];
                $type = $document['type'];
                $title = $document['title'];
                $extension = strtolower($document['extension']);

                echo "<tr class='documents' id='trMyDocumentsContent'>
                        <td class='form-text-small-center' id='tdMyDocumentsCheckbox'>
                            <input class='checkbox' id='chkMyDocumentsCheckbox" . $key . "' type='checkbox' name='document[]' value='$documentID'>
                        </td>
                        <td class='form-text-medium-center' id='tdMyDocumentsType'>
                            <select class='form-text-medium-center' id='selMyDocumentsType' name='docType[]'>";
                
                
                foreach ($docTypes AS $docType) {
                    if ($docType == $document['type']) {
                        echo "<option class='form-text-medium-center' id='optMyDocumentsType' value='$docType' selected='selected'>$docType</option>";
                    } else {
                        echo "<option class='form-text-medium-center' id='optMyDocumentsType' value='$docType'>$docType</option>";
                    } 
                }
                
                 echo "</select></td>
                            <td class='form-text-large-left' id='tdMyDocumentsTitle'>
                                <input class='form-text-large-left inMyDocumentsTitle' id='inMyDocumentsTitle' type='text' name='docTitle[]' value='" . $document['title'] . "'>
                            </td>
                            <td class='form-text-small-center' id='tdMyDocumentsExtension'>"
                                . $document['extension'] . 
                            "</td>
                            <td class='form-text-small-center' id='tdMyDocumentsSize'>"
                                . $document['size'] . 
                            "</td>
                            <td class='form-text-medium-center' id='tdMyDocumentsUploadDate'>"
                                . $document['uploadDate'] . 
                            "</td>
                            <td class='download' id='dwnMyDocumentsDownload'>
                                <input class='form-submit-small-center-gray' id='subMyDocumentsDownload' type='submit' name='downloadDocument[$documentID]' value='Download'>
                            </td>
                        </tr>";
            }

            echo "</tbody></table>
                    <br>
                    <table class='documents' id='tblMyDocumentsSubmitButtons'>
                        <tr class='documents' id='trMyDocumentsSubmitButtons'>
                            <td class='documents' id='tdMyDocumentsShare'>
                                <input class='form-submit-button' id='subMyDocumentsShare' type='submit' name='share' value='Share'>
                            </td>
                            <td></td>
                            <td class='documents' id='tdMyDocumentsAddToCollection'>
                                <input class='form-submit-button-large' id='subMyDocumentsAddToCollection' type='submit' name='addToPendingPublicCollection' value='Add To Public Collection'>
                            </td>
                            <td></td>
                            <td class='documents' id='tdMyDocumentsUpdate'>
                                <input class='form-submit-button' id='subMyDocumentsUpdate' type='submit' name='updateDoc[$key]' value='Update'>
                            </td>
                            <td></td>
                            <td class='documents' id='tdMyDocumentsDelete'>
                                <input class='form-submit-button' id='subMyDocumentsDelete' type='submit' name='delete' value='Delete'>
                            </td>
                        </tr>
                    </table>
                </form>";
        }
    }
    
    // function to show upload form
    
    public function showUploadForm($index) {    
        
        // get documents types
        
        $docTypes = $this->getDocumentTypes();
        
        // show form

        echo "<tr class='documents' id='trUploadDocumentsType'>
                <td class='form-text-medium-center' id='tdUploadDocumentsType'>
                    <select class='form-text-medium-center' id='selUploadDocumentsType' name='docType[]'>";
    
        foreach ($docTypes AS $docType) {
            echo "<option class='form-text-medium-center' id='optUploadDocumentsType' value='$docType'>$docType</option>";
        }

        echo "</select>
                    </td>
                    <td>
                        <input class='form-text-extralarge-left' id='inUploadDocumentsSelectFile' type='file' name='$index'[]' accept='.doc, .docx, .txt, .pdf'></td>
                </tr>";
    }
    
    // function to get document count
    
    public function getDocumentCount() {
        $this->getDocuments();
        return count($this->collection);
    }
    
    // function to download document
    
    public function downloadDocument($documentID) {
        
        // get document 
        
        $document = $this->getDocumentByID($documentID);
        
        // get document properties
        
        $type = $document['type'];
        $extension = strtolower($document['extension']);
        $title = $document['title'];
        $rootDirectory = dirname(dirname(__FILE__));
        
        // create file path
        
        $file = "$rootDirectory/uploadedFiles/$this->collectionUser/$type/$title.$extension";
        
        // download file
        
        downloadFile($file);
    }
    
    // function to download received docuemnts
    
    public function downloadReceivedDocument() {
        
        // get document
        
        $receivedDocumentID = key($_POST['downloadReceivedDocument']);
        $receivedDocument = $this->getReceivedDocumentByID($receivedDocumentID);
        
        // get document properties
        
        $extension = strtolower($receivedDocument['extension']);
        $title = $receivedDocument['title'];
        $receivedFrom = $receivedDocument['receivedFrom'];
        $receivedDate = str_replace('-', '', str_replace(' ', '', str_replace(':', '', $receivedDocument['receivedDate'])));
        $rootDirectory = dirname(dirname(__FILE__));
        
        // create file path
        
        $file = "$rootDirectory/uploadedFiles/$receivedFrom/sharedFiles/$receivedDate/$title.$extension";
        
        // download file
        
        downloadFile($file);
    }
    
    // function to download shared document
    
    public function downloadSharedDocument() {
        
        // get document
        
        $sharedDocumentID = key($_POST['downloadSharedDocument']);
        $sharedDocument = $this->getSharedDocumentByID($sharedDocumentID);
        
        // get document properties
        
        $extension = strtolower($sharedDocument['extension']);
        $title = $sharedDocument['title'];
        $sharedDate = str_replace('-', '', str_replace(' ', '', str_replace(':', '', $sharedDocument['sharedDate'])));
        $rootDirectory = dirname(dirname(__FILE__));
        
        // create file path
        
        $file = "$rootDirectory/uploadedFiles/$this->collectionUser/sharedFiles/$sharedDate/$title.$extension";
        
        // download file
        
        downloadFile($file);
    }
    
    // function to download public collection document 
    
    public function downloadPublicCollectionDocument() {
        
        // get document
        
        $collectDocID = key($_POST['downloadCollectDocument']);
        $collectDocument = $this->getPublicCollectionDocByID($collectDocID);
        
        // get document properties
        
        $collectTitle = $collectDocument['collectionTitle'];
        $type = $collectDocument['type'];
        $extension = strtolower($collectDocument['extension']);
        $title = $collectDocument['title'];
        $rootDirectory = dirname(dirname(__FILE__));
        
        // create file path
        
        $file = "$rootDirectory/uploadedFiles/$collectTitle/$type/$title.$extension";
        
        // download file
        
        downloadFile($file);
    }
    
    /************************ RECEIVED DOCUMENTS METHODS ************************/
    
    // function to get received collection
    
    public function getReceivedDocuments() {
        
        // check if session variable already set
        
        if (!isset($_SESSION['receivedCollection'])) {
            
            // reset received collection array
        
            $this->receivedCollection = [];

            // open database connection

            $this->db->getDBConnection();

            // check for errors

            if (!$this->db->con->connect_error) {

                // get documents

                $query = "CALL usp_getReceivedDocumentsByUser('$this->collectionUser');";
                $result = mysqli_query($this->db->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->receivedCollection[] = $row;
                }
                
                // close result and database connection

                if ($result && isset($this->receivedCollection)) {
                    $result->close();
                }
                $this->db->closeDBConnection();
                
                // check if results returned
                
                if (isset($this->receivedCollection)) {
                    $_SESSION['receivedCollection'] = $this->receivedCollection;
                    return $this->receivedCollection;
                } else {
                    $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                    return false;
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
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

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error) {

            // get documents

            $query = "CALL usp_getReceivedDocumentsByMsgID('$msgID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->receivedCollection[] = $row;
            }

            // close result and database connection

            if ($result && isset($this->receivedCollection)) {
                $result->close();
            }
            $this->db->closeDBConnection();
            
            // return received collection
            
            if (isset($this->receivedCollection)) {
                $_SESSION['receivedCollection'] = $this->receivedCollection;
                return $this->receivedCollection;
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return false;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        } 
    }
    
    // function to get received document by id
    
    public function getReceivedDocumentByID($receivedDocID) {
        
        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get document

            $query = "CALL usp_getReceivedDocumentByDocID('$receivedDocID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $receivedDocument = $row;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        }

        // close result and database connection

        if ($result && isset($receivedDocument)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return document
        
        if (isset($receivedDocument)) {
            return $receivedDocument; 
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbDeleteError();
            return false;
        }
    }
    
    // function to add received documents to collection
    
    public function addReceivedDocumentsToCollection() {
        foreach ($_POST['document'] as $receivedDocID) {
            $receivedDocument = $this->getReceivedDocumentByID($receivedDocID);
            $fileName = $receivedDocument['title'] . '.' . strtolower($receivedDocument['extension']);
            $duplicate = false;
            
            foreach ($_SESSION['collection'] as $document) {
                if ($document['title'] == $receivedDocument['title']
                        && $document['type'] == $receivedDocument['type']
                        && $document['extension'] == $receivedDocument['extension']) {
                    $duplicate = true;
                    break;
                }
            }
            
            if ($duplicate) {
                $_SESSION['displayMessage'][] = InfoMessage::documentsDuplicate($fileName);
            } else if ($this->addReceivedDocumentToCollection($receivedDocument)) {  
                $_SESSION['displayMessage'][] = InfoMessage::documentsAdded();
            } else {
                $_SESSION['displayMessage'][] = InfoMessage::documentsNotAdded();
            }
        }
    }
    
    // function to add received document to collection
    
    public function addReceivedDocumentToCollection($receivedDoc) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_insertDocument(?, ?, ?, ?, ?);");
            $statement->bind_param('sssss', $title, $type, $extension, $size, $user);
            
            // get document properties
        
            $title = $receivedDoc['title'];
            $type = $receivedDoc['type'];
            $extension = $receivedDoc['extension'];
            $lowerExtension = strtolower($extension);
            $size = $receivedDoc['size'];
            $user = $this->collectionUser;
            $receivedFrom = $receivedDoc['receivedFrom'];
            $receivedDate = str_replace('-', '', str_replace(' ', '', str_replace(':', '', $receivedDoc['receivedDate'])));
            $source = dirname(dirname(__FILE__)) . "/uploadedFiles/$receivedFrom/sharedFiles/$receivedDate/$title.$lowerExtension";
            $destination = dirname(dirname(__FILE__)) . "/uploadedFiles/$user/$type/$title.$lowerExtension";

            // create new type directory as needed

            if (!createDirectory(dirname($destination))) {
                $_SESSION['displayMessage'] = InfoMessage::fileDirectoryNotCreated();
                $this->db->closeDBConnection();
                return false;
            } else if (file_exists($destination)) {
                $_SESSION['displayMessage'] = InfoMessage::fileDuplicate();
                $this->db->closeDBConnection();
                return false;
            } else if (file_exists($source) && copy($source, $destination)) {

                // update document

                $statement->execute();

                // check for errors

                if ($statement->error != '') {
                    $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                    unlink($destination);
                    $this->db->closeDBConnection();
                    return false;

                } else {
                    unset($_SESSION['collection']);
                    $_SESSION['collection'] = $this->getDocuments();
                } 
            }  
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to display received collection in table
            
    public function showReceivedCollection() {
        
        // check for existing documents

        if ($this->getReceivedDocumentCount() == 0) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
        
            // iterate through shared Collection array to display Documents

            echo "<table class='documents' id='tblReceivedDocumentsMsg'>
                    <thead class='documents' id='theadReceivedDocumentsMsg'>
                        <tr class='documents' id='trReceivedDocumentsMsgHeaders'>
                            <th class='form-submit-small-header-center' id='thReceivedDocumentsMsgSelectHeader'>
                                <input class='form-submit-small-header-center' id='subReceivedDocumentsMsgSelectHeader' type='submit' name='select' value='Select' disabled>
                            </th>
                            <th class='form-submit-medium-header-center' id='thReceivedDocumentsMsgTypeHeader'>
                                <input class='form-submit-medium-header-center' id='subReceivedDocumentsMsgTypeHeader' type='submit' name='type' value='Type' disabled>
                            </th>
                            <th class='form-submit-large-header-left' id='thReceivedDocumentsMsgTitleHeader'>
                                <input class='form-submit-large-header-left' id='subReceivedDocumentsMsgTitleHeader' type='submit' name='title' value='Title' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thReceivedDocumentsMsgExtensionHeader'>
                                <input class='form-submit-small-header-center' id='subReceivedDocumentsMsgExtensionHeader' type='submit' name='extension' value='Extension' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thReceivedDocumentsMsgSizeHeader'>
                                <input class='form-submit-small-header-center' id='subReceivedDocumentsMsgSizeHeader' type='submit' name='size' value='File Size' disabled>
                            </th>
                            <th class='form-submit-medium-header-center' id='thReceivedDocumentsMsgShareDateHeader'>
                                <input class='form-submit-small-header-center' id='thReceivedDocumentsMsgShareDateHeader' type='submit' name='sharedDate' value='Shared Date' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thReceivedDocumentsMsgDownloadHeader'>
                                <input class='form-submit-small-header-center' id=subReceivedDocumentsMsgDownloadHeader' type='submit' name='download' value='Download' disabled>
                            </th>
                        </tr>
                    </thead>
                    <tbody class='documents' id='tbodyReceivedDocumentsMsg'>";
            
            foreach ($this->receivedCollection as $key => $receivedDocument) {
                
                $receivedDocID = $receivedDocument['receivedDocumentID'];
                $type = $receivedDocument['type'];
                $title = $receivedDocument['title'];
                $extension = $receivedDocument['extension'];
                $size = $receivedDocument['size'];
                $sharedBy = $receivedDocument['receivedFrom'];
                $sharedDate = $receivedDocument['receivedDate'];

                echo "<tr class='documents' id='trReceivedDocumentsMsg'>
                        <td class='form-text-small-center' id='tdReceivedDocumentsMsgSelect'>
                            <input class='checkbox' id='chkReceivedDocumentsMsgSelect' type='checkbox' name='document[]' value='$receivedDocID'>
                        </td>
                        <td class='form-text-medium-center' id='tdReceivedDocumentsMsgType'>$type</td>
                        <td class='form-text-large-left' id='tdReceivedDocumentsMsgTitle'>$title</td>
                        <td class='form-text-small-center' id='tdReceivedDocumentsMsgExtension'>$extension</td>
                        <td class='form-text-small-center' id='tdReceivedDocumentsMsgSize'>$size</td>
                        <td class='form-text-medium-center' id='tdReceivedDocumentsMsgShareDate'>$sharedDate</td>
                        <td class='form-submit-small-center-gray' id='tdReceivedDocumentsMsgDownload'>
                            <input class='form-submit-small-center-gray' id='subReceivedDocumentsMsgDownload' type='submit' name='downloadReceivedDocument[$receivedDocID]' value='Download'>
                        </td>
                    </tr>";
            }

            echo "</tbody></table></div>";
        }
    }
    
    // function to get received document count
    
    public function getReceivedDocumentCount() {
        $this->getReceivedDocuments();
        return count($this->receivedCollection);
    }
    
    /************************ PENDING SHARED DOCUMENTS METHODS ************************/
    
    // function to get shared documents
    
    public function getPendingSharedDocuments() {
        
        // check if session variable already set
        
        if (!isset($_SESSION['pendingSharedCollection'])) {
            
            // reset received collection array
        
            $this->pendingSharedCollection = [];

            // open database connection

            $this->db->getDBConnection();

            // check for errors

            if (!$this->db->con->connect_error ) {

                // get documents

                $query = "CALL usp_getPendingSharedDocumentsByUser('$this->collectionUser');";
                $result = mysqli_query($this->db->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->pendingSharedCollection[] = $row;
                }
                
                // close result and database connection

                if ($result && isset($this->pendingSharedCollection)) {
                    $result->close();
                }
                $this->db->closeDBConnection();
                
                // return pending shared documents
                
                if (isset($this->pendingSharedCollection)) {
                    $_SESSION['receivedCollection'] = $this->pendingSharedCollection;
                    return $this->pendingSharedCollection;
                } else {
                    $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                    return false;
                } 
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
                return false;
            }
        } else {
            $this->pendingSharedCollection = $_SESSION['pendingSharedCollection'];
        } 
    }
    
    // function to add shared documents
    
    public function addSharedDocuments($documentIDs) {
        foreach ($documentIDs as $documentID) {
            $sharedDocument = $this->getDocumentByID($documentID);
            $this->addSharedDocument($sharedDocument);
        }
    }
    
    // function to add document to shared documents
    // note that the document is "sent" to another user until it has been updated with a sharedWith value (see shareDocuments function)
    
    public function addSharedDocument($sharedDocument) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_insertSharedDocument(?, ?);");
            $statement->bind_param('is', $docID, $sharedBy);
            
            // get document properties
        
            $docID = $sharedDocument['documentID'];
            $sharedBy = $this->collectionUser;
        
            // add document
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error == '') {
                unset($_SESSION['pendingSharedCollection']);
                $_SESSION['pendingSharedCollection'] = $this->getPendingSharedDocuments();
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError(); // todo
                $this->db->closeDBConnection();
                return false;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
     // function to update shared documents
    
    public function updateSharedDocument($sharedDocument, $sharedWith, $sharedDate, $msgID) {
        
        // check for errors
        
        if (checkForExistingAccount($sharedWith, $this->db)) {
            
            // open database connection
        
            $this->db->getDBConnection();
            
            // check for errors
            
            if (!$this->db->con->connect_error) {
                
                // prepare insert statement

                $statement = $this->db->con->prepare("CALL usp_updateSharedDocument(?, ?, ?, ?);");
                $statement->bind_param('issi', $sharedDocID, $docSharedWithUser, $docSharedDate, $docMessageID);

                // get document properties

                $sharedDocID = $sharedDocument['sharedDocumentID'];
                $docSharedWithUser = $sharedWith;
                $docSharedDate = $sharedDate;
                $docMessageID = $msgID;

                // update shared documents

                $statement->execute();

                // check for errors

                if ($statement->error != '') {
                    $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                    $this->db->closeDBConnection();
                    return false;
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                return false;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::documentsNotUpdated(); // todo
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to delete pending shared document
    
    public function deletePendingSharedDocument($sharedDocument) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->db->con->prepare("CALL usp_deletePendingSharedDocument(?);");
            $statement->bind_param('i', $sharedDocID);
            
            // get shared document properties

            $sharedDocID = $sharedDocument['sharedDocumentID'];
            $title = $sharedDocument['title'];
            $type = $sharedDocument['type'];
            $extension = $sharedDocument['extension'];
            $lowerExtension = strtolower($extension);
            $sharedBy = $this->collectionUser;
                
            // delete received document

            $statement->execute();

            // check for errors

            if ($statement->error != '') {
                $_SESSION['displayMessage'] = InfoMessage::documentsNotDeleted();
                $this->db->closeDBConnection();
                return false;
            } else {

                // reseed receivedDocumentID

                $this->seed = mysqli_fetch_array(mysqli_query($this->db->con, "SELECT MAX(shareDocumentID) FROM shareDocument;"), MYSQLI_NUM)[0];
                mysqli_query($this->db->con, "ALTER TABLE shareDocument AUTO_INCREMENT = '$this->seed';");

                // get received documents

                unset($_SESSION['pendingSharedCollection']);
                $_SESSION['pendingSharedCollection'] = $this->getPendingSharedDocuments();
            }

        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::documentsDeleted();
        $this->db->closeDBConnection();
        
    }
    
    // function to show pending shared Collection
            
    public function showPendingSharedCollection() {
        
        // check for existing documents

        if ($this->getPendingSharedDocumentCount() == 0) {
            
            // create new error message
  
            echo infoMessage::attachmentsNoneSelected();
            
        } else {
        
            // iterate through shared Collection array to display Documents

            echo "<tr>
                    <th class='form-label-small-center' id='thPendingShareDocsSelectHeader'>
                        <input class='form-submit-small-header-center' id='subPendingShareDocsSelectHeader' type='submit' name='select' value='Select' disabled>
                    </th>
                    <th class='form-label-medium-center' id='thPendingShareDocsTypeHeader'>
                        <input class='form-submit-medium-header-center' id='subPendingShareDocsTypeHeader' type='submit' name='type' value='Type' disabled>
                    </th>
                    <th class='form-label-large-center' id='thPendingShareDocsTitleHeader'>
                        <input class='form-submit-large-header-left' id='subPendingShareDocsTitleHeader' type='submit' name='title' value='Title' disabled>
                    </th>
                    <th class='form-label-small-center' id='thPendingShareDocsExtensionHeader'>
                        <input class='form-submit-small-header-center' id='subPendingShareDocsExtensionHeader' type='submit' name='extension' value='Extension' disabled>
                    </th>
                    <th class='form-label-small-center' id='thPendingShareDocsSizeHeader'>
                        <input class='form-submit-small-header-center' id='subPendingShareDocsSizeHeader' type='submit' name='size' value='File Size' disabled>
                    </th>
                </tr>
            </thead>
            <tbody class='documents' id='tbodyPendingShareDocuments'>";
            
            foreach ($this->pendingSharedCollection as $key => $sharedDocument) {
                
                $sharedDocID = $sharedDocument['sharedDocumentID'];
                $type = $sharedDocument['type'];
                $title = $sharedDocument['title'];
                $extension = $sharedDocument['extension'];
                $size = $sharedDocument['size'];

                echo "<tr class='documents' id='trPendingShareDocs'>
                        <td class='form-text-small-center' id='tdPendingShareDocsCheckbox'>
                            <input class='checkbox' id='chkPendingShareDocsCheckbox' type='checkbox' name='document[]' value='$sharedDocID'>
                        </td>
                        <td class='form-text-medium-center' id='tdPendingShareDocsType'>$type</td>
                        <td class='form-text-large-left' id='tdPendingShareDocsTitle'>$title</td>
                        <td class='form-text-small-center' id='tdPendingShareDocsExtension'>$extension</td>
                        <td class='form-text-small-center' id='tdPendingShareDocsSize'>$size</td>
                    </tr>";
            }
        }
    }
    
    // function to get shared document count
    
    public function getPendingSharedDocumentCount() {
        $this->getPendingSharedDocuments();
        return count($this->pendingSharedCollection);
    }
    
    /************************ SHARED DOCUMENTS METHODS ************************/
    
    // function to get shared collection
    
    public function getSharedDocuments() {
        
        // check if session variable already set
        
        if (!isset($_SESSION['sharedCollection'])) {
            
            // reset shared collection array
        
            $this->sharedCollection = [];

            // open database connection

            $this->db->getDBConnection();

            // check for errors

            if (!$this->db->con->connect_error) {

                // get documents

                $query = "CALL usp_getSharedDocumentsByUser('$this->collectionUser');";
                $result = mysqli_query($this->db->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->sharedCollection[] = $row;
                }
                
                // close result and database connection

                if ($result && isset($this->sharedCollection)) {
                    $result->close();
                }
                $this->db->closeDBConnection();
                
                // return shared collection
                
                if (isset($this->sharedCollection)) {
                    $_SESSION['sharedCollection'] = $this->sharedCollection;
                    return $this->sharedCollection;
                } else {
                    $SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                    return false;
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
                return false;
            }            
        } else {
            $this->sharedCollection = $_SESSION['sharedCollection'];
        } 
    }
    
    // function to get sent documents by message
    
    public function getSentDocumentsByMessage($msgID) {
            
        // reset received collection array

        $this->sharedCollection = [];

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error) {

            // get documents

            $query = "CALL usp_getSentDocumentsByMsgID('$msgID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->sharedCollection[] = $row;
            }

            // close result and database connection

            if ($result && isset($this->sharedCollection)) {
                $result->close();
            }
            $this->db->closeDBConnection();
            
            // return shared collection
            
            if (isset($this->sharedCollection)) {
                $_SESSION['sharedCollection'] = $this->sharedCollection;
                return $this->sharedCollection;
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return false;
            }
        } else {
            $this->db->closeDBConnection();
            return false;
        } 
    }
    
    // function to get shared document by id
    
    public function getSharedDocumentByID($sharedDocID) {
        
        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get document

            $query = "CALL usp_getSharedDocumentByDocID('$sharedDocID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $sharedDocument = $row;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        }

        // close result and database connection

        if ($result && isset($sharedDocument)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return document
        
        if (isset($sharedDocument)) {
            return $sharedDocument;  
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return $false;
        }
    }
    
    // function to send shared documents
    
    public function sendSharedDocuments() {
        
        // get message properties
        
        $shareWith = sanitizeString($_POST['shareWithUser'][0]);
        $subject = sanitizeString($_POST['subject']);
        $body = sanitizeString($_POST['body']);
        $sharedDocuments = $_SESSION['pendingSharedCollection'];
        $_SESSION['sharedDate'] = date('Y-m-d h:i:s');
        $sharedDate = $_SESSION['sharedDate'];
        
        // check for errors and send message
        
        if (strlen($shareWith) == 0 || !checkForExistingAccount($shareWith, $this->db)) {
            $_SESSION['displayMessage'] = InfoMessage::missingTo();
        } else if ($this->shareDocuments($subject, $body, $shareWith, $sharedDocuments, $sharedDate)) {
            unset($_SESSION['pendingSharedCollection']);
            $_SESSION['pendingSharedCollection'] = $this->getPendingSharedDocuments();
        }
    }

    // function to share documents
    
    public function shareDocuments($subject, $body, $shareWith, $sharedDocuments, $sharedDate) {
        
        // declare and initialize variables
        
        $msg = new Message($subject, $body, $shareWith, $this->collectionUser, $sharedDate, $this->db);
        $msgs = new Messages($this->collectionUser, $this->db);
        $shareDate = str_replace('-', '', str_replace(' ', '', str_replace(':', '', $sharedDate)));
        $destinationFolder = dirname(dirname(__FILE__)) . '/uploadedFiles/' . $this->collectionUser . '/sharedFiles/' . sanitizeString($shareDate) . '/';
        
        // check for errors
        
        if (!createDirectory($destinationFolder)) {
            $_SESSION['displayMessage'] = InfoMessage::fileDirectoryNotCreated();
            return false;
        } else if ($msg->sendMessage() && $msg->getMessageID()) {
            
            $msgID = $msg->getMessageID();
            
            // send shared documents
            
            foreach($sharedDocuments as $sharedDocument) {
                
                $type = $sharedDocument['type'];
                $title = $sharedDocument['title'];
                $extension = strtolower($sharedDocument['extension']);
                $destination = $destinationFolder . '/' . $title . '.' . $extension;
                $source = dirname(dirname(__FILE__)) . '/uploadedFiles/' . $this->collectionUser . '/' . $type . '/' . $title . '.' . $extension;
                
                // check for errors
                
                if (copy($source, $destination)) {
                    if (!$this->updateSharedDocument($sharedDocument, $shareWith, $sharedDate, $msgID)) {
                        $_SESSION['displayMessage'] = InfoMessage::messageNotSent();
                        $msgs->deleteMessage($msgID);
                        unset($msg);
                        unset($msgs);
                        return false;
                    } 
                } else {
                    $_SESSION['displayMessage'] = InfoMessage::messageNotSent();
                    $msgs->deleteMessage($msgID);
                    unset($msg);
                    unset($msgs);
                    return false;
                }      
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::messageNotSent();
            return false;
        }
        
        return true;
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
        
        // determine sorting order

        if ($ascending) {
            array_multisort($arrayKey, SORT_DESC, $this->pendingSharedCollection);
        } else {
            array_multisort($arrayKey, SORT_ASC, $this->pendingSharedCollection);
        }
        
        // return sorted collection

        $_SESSION['pendingSharedCollection'] = $this->pendingSharedCollection;
    }
    
    // function to display sent collection in table
            
    public function showSentCollection() {
        
        // check for existing documents

        if ($this->getSharedDocumentCount() == 0) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
        
            // iterate through shared Collection array to display Documents

            echo "<table class='documents' id='tblSentMessagesMsg'>
                    <thead class='documents' id='theadSentMessagesMsg'>
                        <tr class='documents' id='trSentMessagesMsgHeaders'>
                            <th class='form-submit-medium-header-center' id='thSentMessagesMsgTypeHeader'>
                                <input class='form-submit-small-header-center' id='subSentMessagesMsgTypeHeader' type='submit' name='type' value='Type' disabled>
                            </th>
                            <th class='form-submit-large-header-left' id='thSentMessagesMsgTitleHeader'>
                                <input class='form-submit-large-header-left' id='subSentMessagesMsgTitleHeader' type='submit' name='title' value='Title' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thSentMessagesMsgExtensionHeader'>
                                <input class='form-submit-small-header-center' id='subSentMessagesMsgExtensionHeader' type='submit' name='extension' value='Extension' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thSentMessagesMsgSizeHeader'>
                                <input class='form-submit-small-header-center' id='subSentMessagesMsgSizeHeader' type='submit' name='size' value='File Size' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thSentMessagesMsgShareWith'>
                                <input class='form-submit-small-header-center' id='subSentMessagesMsgShareWith' type='submit' name='sharedWith' value='To' disabled>
                            </th>
                            <th class='form-submit-medium-header-center' id='thSentMessagesMsgShareDate'>
                                <input class='form-submit-small-header-center' id='subSentMessagesMsgShareDate' type='submit' name='sharedDate' value='Shared Date' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thSentMessagesMsgDownload'>
                                <input class='form-submit-small-header-center' id='subSentMessagesMsgDownload' type='submit' name='download' value='Download' disabled>
                            </th>
                        </tr>
                    </thead>
                    <tbody class='documents' id='tbodySentMessagesMsg'>";
            
            foreach ($this->sharedCollection as $key => $sharedDocument) {
                
                $sharedDocID = $sharedDocument['sharedDocumentID'];
                $type = $sharedDocument['type'];
                $title = $sharedDocument['title'];
                $extension = $sharedDocument['extension'];
                $size = $sharedDocument['size'];
                $sharedWith = $sharedDocument['sharedWith'];
                $sharedDate = $sharedDocument['sharedDate'];

                echo "<tr class='documents' id='trSentMessagesMsg'>
                        <td class='form-text-medium-center' id='tdSentMessagesMsgType'>$type</td>
                        <td class='form-text-large-left' id='tdSentMessagesMsgTitle'>$title</td>
                        <td class='form-text-small-center' id='tdSentMessagesMsgExtension'>$extension</td>
                        <td class='form-text-small-center' id='tdSentMessagesMsgSize'>$size</td>
                        <td class='form-text-small-center' id='tdSentMessagesMsgShareWith'>$sharedWith</td>
                        <td class='form-text-medium-center' id='tdSentMessagesMsgShareDate'>$sharedDate</td>
                        <td class='form-text-small-center' id='tdSentMessagesMsgDownload'>
                            <input class='form-submit-small-center-gray' id='subSentMessagesMsgDownload' type='submit' name='downloadSharedDocument[$sharedDocID]' value='Download'>
                        </td>
                    </tr>";
            }

            echo "</tbody></table>";
        }
    }
    
    // function to get shared document count
    
    public function getSharedDocumentCount() {
        $this->getSharedDocuments();
        return count($this->sharedCollection);
    }
    
    /************************ PENDING PUBLIC COLLECTION METHODS ************************/
    
    // function to get pending collection documents
    
    public function getPendingPublicCollectionDocs() {
        
        // check if session variable is set
        
        if (!isset($_SESSION['pendingPublicCollectionDocs']))
        {
            // reset public collection array
            
            unset($this->pendingPublicCollectionDocs);
        
            // open database connection

            $this->db->getDBConnection();

            // check for errors

            if (!$this->db->con->connect_error ) {

                // get document

                $query = "CALL usp_getPendingCollectionDocumentsByUser('$this->collectionUser');";
                $result = mysqli_query($this->db->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->pendingPublicCollectionDocs[] = $row;
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            }

            // close result and database connection

            if ($result && isset($this->pendingPublicCollectionDocs)) {
                $result->close();
            }
            $this->db->closeDBConnection();

            // return document

            if (isset($this->pendingPublicCollectionDocs)) {
                $_SESSION['pendingPublicCollectionDocs'] = $this->pendingPublicCollectionDocs;
                return $this->pendingPublicCollectionDocs;  
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return false;
            }
        } else {
            $this->pendingPublicCollectionDocs = $_SESSION['pendingPublicCollectionDocs'];
            return $this->pendingPublicCollectionDocs;
        }
    }
    
    // function to check for existing pending public collection documents
    
    public function checkForExistingPendingPublicCollectionDoc($document) {
        
        foreach ($this->pendingPublicCollectionDocs as $collectDoc) {
            
            if ($collectDocument['title'] != $document['title'] 
                        && $collectDocument['type'] != $document['type'] 
                        && $collectDocument['extension'] != $document['extension']) {
                continue;
            } else {
                return true;
            }
        }
        
        return false;
    }
    
    // function to add pending documents to public collection
    
    public function addDocumentsToPendingPublicCollection() {
        
        // get pending documents
        
        $this->getPendingPublicCollectionDocs();
        
        // clear display messages
        
        unset($_SESSION['displayMessage']);
        
        // for each document selected, added to pending documents
        
        foreach ($_POST['document'] as $documentID) {
            
            // get document
            
            $document = $this->getDocumentByID($documentID);
            
            // check if document already added
            
            if (!$this->checkForExistingPendingPublicCollectionDoc($document)) {
                
                if (!$this->addDocumentToPublicCollection($document)) {
                    $_SESSION['displayMessage'] = InfoMessage::publicCollectionDocumentsNotAdded();
                }
            }
        }
    }
    
    // function to cancel adding a document to a public collection
    
    public function cancelAddDocumentToPublicCollection() {
        foreach ($_SESSION['pendingPublicCollectionDocs'] as $collectDocument) {
            $this->deletePendingPublicCollectionDocument($collectDocument);
        }
    }
    
    // function to add documents to public collection 
    
    public function addDocumentsToPublicCollection() {
        
        $error = false;
        unset($_SESSION['displayMessage']);
        
        if (!isset($_SESSION['pendingPublicCollectionDocs']) || !isset($_POST['publicCollectionTitle'])) {
            $_SESSION['displayMessage'] = InfoMessage::publicCollectionNoDocumentsSelected();
        } else {
            $this->getPublicCollections();
            $collectTitle = $_POST['publicCollectionTitle'];
            $collectID = $this->getPublicCollectionID($collectTitle);
            $_SESSION['collectionID'] = $collectID;
            $this->getPublicCollectionDocsByID($collectID);
            foreach ($_SESSION['pendingPublicCollectionDocs'] as $collectDocument) {
                
                if (!$this->checkForExistingPublicCollectionDoc($collectDocument, $collectTitle)) {
                    $this->updatePublicCollectionDoc($collectDocument, $collectTitle);
                } else {
                    $error = true;
                    $documentTitle = $collectDocument['title'] . '.' . strtolower($collectDocument['extension']);
                    $_SESSION['displayMessage'][] = InfoMessage::publicCollectionDuplicateDocument($documentTitle);
                }
            }
            
            if (!$error) {
                header('Location: viewPublicCollection.php');
            } 
        } 
    }
    
     // function to add document to public collection

    public function addDocumentToPublicCollection($collectDocument) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_insertCollectionDocument(?, ?, ?, ?, ?);");
            $statement->bind_param('sssss', $collectDocTitle, $collectDocType, $collectDocExtension, $collectDocSize, $collectUser);
            
            // get document properties
        
            $collectDocTitle = $collectDocument['title'];
            $collectDocType = $collectDocument['type'];
            $collectDocExtension = $collectDocument['extension'];
            $collectDocSize = $collectDocument['size'];
            $collectUser = $this->collectionUser;
                    
            // add document

            $statement->execute();

            // check for errors

            if ($statement->error == '') {
                unset($_SESSION['pendingPublicCollectionDocs']);
                $_SESSION['pendingPublicCollectionDocs'] = $this->getPendingPublicCollectionDocs($this->collectionUser);
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbDocumentsNotAdded();
                $this->db->closeDBConnection();
                return false;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to update document properties (using optional parameters)

    public function updatePublicCollectionDoc($collectDocument, $collectTitle) {
        
        // get documents
        
        $this->getDocuments();

        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement

            $statement = $this->db->con->prepare("CALL usp_updateCollectionDocument(?, ?);");
            $statement->bind_param('is', $collectDocID, $collectionTitle);
            
            // get collection properties
            
            $collectDocID = $collectDocument['collectionDocumentID'];
            $collectDocTitle = $collectDocument['title'];
            $collectType = $collectDocument['type'];
            $collectExtension = strtolower($collectDocument['extension']);
            $collectUser = $this->collectionUser;
            $collectionTitle = $collectTitle;
            $source = dirname(dirname(__FILE__)) . "/uploadedFiles/$collectUser/$collectType/$collectDocTitle.$collectExtension";
            $destination = dirname(dirname(__FILE__)) . "/uploadedFiles/$collectTitle/$collectType/$collectDocTitle.$collectExtension";
                    
            // create new type directory as needed

            if (!createDirectory(dirname(dirname($destination))) || !createDirectory(dirname($destination))) {
                $_SESSION['displayMessage'] = InfoMessage::fileDirectoryNotCreated();
                return false;
            } else if (!copy($source, $destination)) {
                $_SESSION['displayMessage'] = InfoMessage::documentsNotAdded();
                return false;
            } else {
        
                // add document

                $statement->execute();

                // check for errors

                if ($statement->error == '') {
                    unset($_SESSION['publicCollectionDocs']);
                    $_SESSION['publicCollectionDocs'] = $this->getPublicCollectionDocsByID($collectID); // update collection array
                } else {
                    unlink($destination);
                    $_SESSION['displayMessage'] = InfoMessage::dbDocumentsNotAdded();
                    $this->db->closeDBConnection();
                    return false;
                }
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to delete pending shared documents
    
    public function deletePendingSharedDocuments() {
        foreach ($_SESSION['pendingSharedCollection'] as $sharedDocument) {
            $this->deletePendingSharedDocument($sharedDocument);
        }
    }
    
    // function to delete pending public collection documents
    
    public function deletePendingPublicCollectionDocuments() {
        foreach ($_POST['pendingCollectDocument'] as $collectDocID) {
            $collectDocument = $this->getPublicCollectionDocByID($collectDocID);
            $this->deletePendingPublicCollectionDocument($collectDocument);
        }
    }
    
    // function to delete pending public collection document

    public function deletePendingPublicCollectionDocument($collectDocument) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->db->con->prepare("CALL usp_deletePendingCollectionDocument(?, ?);");
            $statement->bind_param('is', $docID, $user);
            
            // get document properties

            $docID = $collectDocument['collectionDocumentID'];
            $user = $this->collectionUser;
                
            // delete document from database

            $statement->execute();

            // check for errors

            if ($statement->error != '') {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
                return false;
            } else {

                // reseed documentID

                $this->seed = mysqli_fetch_array(mysqli_query($this->db->con, "SELECT MAX(collectionDocumentID) FROM collectionDocument;"), MYSQLI_NUM)[0];
                mysqli_query($this->db->con, "ALTER TABLE collectionDocument AUTO_INCREMENT = '$this->seed';");

                // get documents

                unset($_SESSION['pendingPublicCollectionDocs']);
                $_SESSION['pendingPublicCollectionDocs'] = $this->getPendingPublicCollectionDocs();
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to display pending public collection documents
            
    public function showPendingPublicCollectionDocs() {
        
        // check for existing documents

        if (count($this->pendingPublicCollectionDocs) == 0 || $this->pendingPublicCollectionDocs == false) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
        
            // iterate through Collection array to display Documents

            echo "<table class='documents' id='tblPendingPublicCollectDocs'>
                    <thead class='documents' id=theadPendingPublicCollectDocs'>
                        <tr class='documents' id='trPendingPublicCollectDocsHeaders'>
                            <th class='form-submit-small-header-center' id='thPendingPublicCollectDocsSelectHeader'>
                                <input class='form-submit-small-header-center' id='inPendingPublicCollectDocsSelectHeader' type='submit' name='select' value='Select' style='width:75px' disabled>
                            </th>
                            <th class='form-submit-medium-header-center' id='thPendingPubicCollectDocsTypeHeader'>
                                <input class='form-submit-medium-header-center' id='inPendingPublicCollectDocsTypeHeader' type='submit' name='sortPendingPublicCollectDoc' value='Type' style='width:150px' disabled>
                            </th>
                            <th class='form-submit-large-header-left' id='thPendingPublicCollectDocsTitleHeader'>
                                <input class='form-submit-large-header-left' id='inPendingPublicCollectDocsTitleHeader' type='submit' name='sortPendingPublicCollectDoc' value='Title' style='width:350px' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thPendingPublicCollectDocsExtensionHeader'>
                                <input class='form-submit-small-header-center' id='inPendingPublicCollectDocsExtensionHeader' type='submit' name='sortPendingPublicCollectDoc' value='Extension' style='width:75px' disabled>
                            </th>
                            <th class='form-submit-small-header-center' id='thPendingPublicCollectDocsSize'>
                                <input class='form-submit-small-header-center' id='inPendingPublicCollectDocsSizeHeader' type='submit' name='sortPendingPublicCollectDoc' value='File Size' style='width:75px' disabled>
                            </th>
                        </tr>
                    </thead>
                    <tbody class='documents' id='tbodyPendingPublicCollectDocs'>";
            
            foreach ($this->pendingPublicCollectionDocs as $collectDocument) {
                
                $collectDocID = $collectDocument['collectionDocumentID'];
                $collectType = $collectDocument['type'];
                $collectTitle = $collectDocument['title'];
                $collectExtension = $collectDocument['extension'];
                $collectSize = $collectDocument['size'];

                echo "<tr class='documents' id='trPendingPublicDocs'>
                        <td class='form-text-small-center' id='tdPendingPublicDocsSelect'>
                            <input class='checkbox' id='chkPendingPublicDocsSelect' type='checkbox' name='pendingCollectDocument[]' value='$collectDocID'>
                        </td>
                        <td class='form-text-medium-center' id='tdPendingPublicDocsType' style='width:150px'>$collectType</td>
                        <td class='form-text-large-left' id='tdPendingPublicDocsTitle'>$collectTitle</td>
                        <td class='form-text-small-center' id='tdPendingPublicDocsExtension'>$collectExtension</td>
                        <td class='form-text-small-center' id='tdPendingPublicDocsSize'>$collectSize</td>
                    </tr>";
            }

            echo "</tbody></table></div>";
        }
    }
    
    /************************ PUBLIC COLLECTION METHODS ************************/
    
    // function to get collections
    
    public function getPublicCollections() {
        
        // check if session variable is set
        
        if (!isset($_SESSION['publicCollection']))
        {
            // reset public collection array
            
            unset($this->publicCollection);
            
            // open database connection
        
            $this->db->getDBConnection();

            // check for errors

            if (!$this->db->con->connect_error ) {

                // get document types

                $query = "CALL usp_getCollections;";
                $result = mysqli_query($this->db->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->publicCollection[] = $row;
                }

                // close result and database connection

                if ($result && isset($this->publicCollection)) {
                    $result->close();
                }
                $this->db->closeDBConnection();

                // return document types

                if (isset($this->publicCollection)) {
                    $_SESSION['publicCollection'] = $this->publicCollection;
                    return $this->publicCollection;
                } else {
                    $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                    return false;
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
                return false;
            } 
        } else {
            $this->publicCollection = $_SESSION['publicCollection'];
            return $this->publicCollection;
        }
    }
    
    // function to get collection title
    
    public function getPublicCollectionTitle($collectID) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // get document ID
        
            $query = "CALL usp_getCollectionTitleByID('$collectID');";
            $result = mysqli_query($this->db->con, $query);
            $collectTitle = mysqli_fetch_array($result, MYSQLI_NUM)[0];
            
            // close result and close database connection
        
            if ($result && isset($collectTitle)) {
                $result->close();
            }
            $this->db->closeDBConnection();
            
            // return document ID
            
            if (isset($collectTitle)) {
                return $collectTitle;
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return 0;
            }
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }
    }
    
    // function to get collection title
    
    public function getPublicCollectionID($collectTitle) {
        
        foreach($this->publicCollection as $publicCollection) {
            if ($publicCollection['title'] == $collectTitle) {
                return $publicCollection['collectionID'];
            } else {
                return false;
            }
        }
    }
    
    // function to get collection documents by id
    
    public function getPublicCollectionDocByID($collectDocID) {

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get document

            $query = "CALL usp_getCollectionDocumentByID('$collectDocID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $collectDocument = $row;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        }

        // close result and database connection

        if ($result && isset($collectDocument)) {
            $result->close();
        }
        $this->db->closeDBConnection();

        // return document

        if (isset($collectDocument)) {
            return $collectDocument;  
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return false;
        }
    }
    
    // function to get collection documents by id
    
    public function getPublicCollectionDocsByID($collectID) {
        
        // check if session variable is set
        
        if (!isset($_SESSION['publicCollectionDocs']) || $_SESSION['publicCollectionDocs'] == false)
        {
            // reset public collection array
            
            unset($this->publicCollectionDocs);
        
            // open database connection

            $this->db->getDBConnection();

            // check for errors

            if (!$this->db->con->connect_error ) {

                // get document

                $query = "CALL usp_getCollectionDocumentsByID('$collectID');";
                $result = mysqli_query($this->db->con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->publicCollectionDocs[] = $row;
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            }

            // close result and database connection

            if ($result && isset($this->publicCollectionDocs)) {
                $result->close();
            }
            $this->db->closeDBConnection();

            // return document

            if (isset($this->publicCollectionDocs)) {
                $_SESSION['publicCollectionDocs'] = $this->publicCollectionDocs;
                return $this->publicCollectionDocs;  
            } else {
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return false;
            }
        } else {
            $this->publicCollectionDocs = $_SESSION['publicCollectionDocs'];
            return $this->publicCollectionDocs;
        }
    }
    
    // function to check for existing public collection documents
    
    public function checkForExistingPublicCollectionDoc($document, $collectTitle) {
        
        foreach ($this->publicCollectionDocs as $collectDocument) {
            
            if ($collectDocument['title'] == $document['title'] 
                        && $collectDocument['type'] == $document['type'] 
                        && $collectDocument['extension'] == $document['extension']
                        && $collectDocument['collectionTitle'] == $collectTitle) {
                return true;
            } else {
                continue;
            }
        }
        
        return false;
    }
    
    // function to add public collection

    public function addPublicCollection($collectTitle, $collectDescription) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_addCollection(?, ?, ?);");
            $statement->bind_param('sss', $title, $description, $user);
            
            // get collection properties
        
            $title = $collectTitle;
            $description = $collectDescription;
            $user = $this->collectionUser;
            $newFolder = dirname(dirname(__FILE__)) . "/uploadedFiles/$collectionTitle/";
            
            if (!createDirectory($newFolder)) {
                $_SESSION['displayMessage'] = InfoMessage::fileDirectoryNotCreated();
                return false;
            } else {
                
                // add document
        
                $statement->execute();

                // check for errors

                if ($statement->error == '') {
                    unset($_SESSION['publicCollection']);
                    $_SESSION['publicCollection'] = $this->getPublicCollections(); // update collection array
                } else {
                    $_SESSION['displayMessage'] = InfoMessage::publicCollectionNotAdded($title);
                    $this->db->closeDBConnection();
                    return false;
                }   
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::publicCollectionAdded($title);
        $this->db->closeDBConnection();
        return true;
    }

    // function to delete document from collection

    public function deletePublicCollection($collectID) {
              
        // get collection title
        
        $collectTitle = $this->getPublicCollectionTitle($collectID);
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->db->con->prepare("CALL usp_deleteCollection(?, ?);");
            $statement->bind_param('is', $colID, $user);
            
            // get document properties

            $colID = $collectID;
            $user = $this->collectionUser;
            
            // delete collection
            
            $statement->execute();

            // check for errors

            if ($statement->error != '') {
                $_SESSION['displayMessage'] = InfoMessage::publicCollectionNotDeleted($collectTitle);
                $this->db->closeDBConnection();
                return false;
            } else {

                // reseed collectionID

                $this->seed = mysqli_fetch_array(mysqli_query($this->db->con, "SELECT MAX(collectionID) FROM collection;"), MYSQLI_NUM)[0];
                mysqli_query($this->db->con, "ALTER TABLE collection AUTO_INCREMENT = '$this->seed';");

                // get documents

                unset($_SESSION['publicCollection']);
                $this->getPublicCollections();
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::publicCollectionDeleted($collectTitle);
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to delete document from collection

    public function deletePublicCollectionDocument($collectDocument) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare delete statement
            
            $statement = $this->db->con->prepare("CALL usp_deleteCollectionDocument(?, ?);");
            $statement->bind_param('is', $docID, $user);
            
            // get document properties

            $docID = $collectDocument['collectionDocumentID'];
            $user = $this->collectionUser;
            $collectID = $collectDocument['collectionID'];
            $collectionTitle = $collectDocument['collectionTitle'];
            $title = $collectDocument['title'];
            $type = $collectDocument['type'];
            $extension = strtolower($collectDocument['extension']);
            $file = dirname(dirname(__FILE__)) . "/uploadedFiles/$collectionTitle/$type/$title.$extension";
            
            // delete document

            if (file_exists($file)) {
                
                // delete document from database
            
                $statement->execute();

                // check for errors

                if ($statement->error != '') {
                    $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                    $this->db->closeDBConnection();
                    return false;
                } else {
                    
                    // delete document from file system
                    
                    unlink($file);

                    // reseed documentID

                    $this->seed = mysqli_fetch_array(mysqli_query($this->db->con, "SELECT MAX(collectionDocumentID) FROM collectionDocument;"), MYSQLI_NUM)[0];
                    mysqli_query($this->db->con, "ALTER TABLE collectionDocument AUTO_INCREMENT = '$this->seed';");

                    // get documents

                    unset($_SESSION['publicCollectionDocs']);
                    $_SESSION['publicCollectionDocs'] = $this->getPublicCollectionDocsByID($collectID);
                }
            } else {
                $_SESSION['displayMessage'] = InfoMessage::documentsNotDeleted();
                $this->db->closeDBConnection();
                return false;
            }

        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::documentsDeleted();
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to show public collections
    
    public function showPublicCollections() {
        
        // check for existing documents

        if ($this->getPublicCollectionCount() == 0) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
        
            // iterate through shared Collection array to display Documents

            echo "<form class='documents' id='frmShowPublicCollections' method='post' action='managePublicCollections.php' enctype='multipart/form-data'>
                    <table class='documents compact' id='tblShowPublicCollections'>
                        <thead class='documents' id='theadShowPublicCollections'>
                            <tr class='documents' id='trShowPublicCollectionsHeaders'>
                                <th class='form-submit-medium-header-left' id='thShowPublicCollectionsTitleHeader'>
                                    <input class='form-submit-medium-header-left' id='subShowPublicCollectionsTitleHeader' type='submit' name='sortPublicCollectionTitle' value='Title' disabled>
                                </th>
                                <th class='form-submit-large-header-left' id='thShowPublicCollectionsDescriptionHeader'>
                                    <input class='form-submit-large-header-left' id='subShowPublicCollectionsDescriptionHeader' type='submit' name='sortPublicCollectionDescription' value='Description' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thShowPublicCollectionsOwnerHeader'>
                                    <input class='form-submit-small-header-center' id='subShowPublicCollectionsOwnerHeader' type='submit' name='sortPublicCollectionOwner' value='Owner' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thShowPublicCollectionsViewHeader'>
                                    <input class='form-submit-small-header-center' id='subShowPublicCollectionsViewHeader' type='submit' name='viewPublicCollection' value='View' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thShowPublicCollectionsDownloadHeader'>
                                    <input class='form-submit-small-header-center' id='subShowPublicCollectionsDownloadHeader' type='submit' name='deletePublicCollection' value='Delete' disabled>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='documents' id='tbodyShowPublicCollections'>";
            
            foreach ($this->publicCollection as $collection) {
                
                $collectID = $collection['collectionID'];
                $collectTitle = $collection['title'];
                $collectDescription = $collection['description'];
                $collectOwner = $collection['owner'];

                echo "<tr class='documents' id='trShowPublicCollections'>
                        <td class='form-text-medium-left' id='tdShowPublicCollectionsTitle'>$collectTitle</td>
                        <td class='form-text-large-left' id='tdShowPublicCollectionsDescription'>$collectDescription</td>
                        <td class='form-text-small-center' id='tdShowPublicCollectionsOwner'>$collectOwner</td>
                        <td class='form-text-small-center' id='tdShowPublicCollectionsView'>
                            <input class='form-submit-small-center-gray' id='subShowPublicCollectionsView' type='submit' name='viewPublicCollection[$collectID]' value='View'>
                        </td>";
                
                if ($collectOwner == $this->collectionUser) {
                    echo "<td class='form-text-small-center' id='tdShowPublicCollectionsDelete'>
                            <input class='form-submit-small-center-gray' id='subShowPublicCollectionsDelete' type='submit' name='deletePublicCollection[$collectID]' value='Delete'>
                        </td></tr>";
                } else {
                    echo "<td class='form-text-small-center' id='tdShowPublicCollectionsDelete'>
                            <input class='form-submit-small-center-gray' id='subShowPublicCollectionsDelete' type='submit' name='deletePublicCollection[$collectID]' value='Delete' disabled>
                        </td></tr>";
                }
            }
            
            echo "</tbody></table></form></div>";
        }
    }
    
    // function to display public collection documents
            
    public function showPublicCollection() {
        
        // check for existing documents

        if (count($this->publicCollectionDocs) == 0) {
            
            // create new error message
  
            echo infoMessage::dbNoRecords();
            
        } else {
        
            // iterate through Collection array to display Documents

            echo "<form class='documents' id='frmViewPublicCollection' method='post' action='viewPublicCollection.php' enctype='multipart/form-data'>
                    <table class='documents' id='tblViewPublicCollection'>
                        <thead class='documents' id='theadViewPublicCollection'>
                            <tr class='documents' id='trViewPublicCollectionHeaders'>
                                <th class='form-submit-medium-header-center' id='thViewPublicCollectionTypeHeader'>
                                    <input class='form-submit-medium-header-center' id='subViewPublicCollectionTypeHeader' type='submit' name='sortCollectDoc' value='Type' disabled>
                                </th>
                                <th class='form-submit-large-header-left' id='thViewPublicCollectionTitleHeader'>
                                    <input class='form-submit-large-header-left' id='subViewPublicCollectionTitleHeader' type='submit' name='sortCollectDoc' value='Title' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thViewPublicCollectionExtensionHeader'>
                                    <input class='form-submit-small-header-center' id='subViewPublicCollectionExtensionHeader' type='submit' name='sortCollectDoc' value='Extension' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thViewPublicCollectionSizeHeader'>
                                    <input class='form-submit-small-header-center' id='subViewPublicCollectionSizeHeader' type='submit' name='sortCollectDoc' value='File Size' disabled>
                                </th>
                                <th class='form-submit-medium-header-center' id='thViewPublicCollectionShareDateHeader'>
                                    <input class='form-submit-medium-header-center' id='subViewPublicCollectionShareDateHeader' type='submit' name='sortCollectDoc' value='Share Date' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thViewPublicCollectionDownloadHeader'>
                                    <input class='form-submit-small-header-center' id='subViewPublicCollectionDownloadHeader' type='submit' name='downloadCollectDoc' value='Download' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thViewPublicCollectionDeleteHeader'>
                                    <input class='form-submit-small-header-center' id='subViewPublicCollectionDeleteHeader' type='submit' name='deleteCollectDoc' value='Delete' disabled>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='documents' id='tbodyViewPublicCollection'>";
            
            foreach ($this->publicCollectionDocs as $collectDocument) {
                
                $collectionDocumentID = $collectDocument['collectionDocumentID'];
                $type = $collectDocument['type'];
                $title = $collectDocument['title'];
                $extension = $collectDocument['extension'];
                $size = $collectDocument['size'];
                $shareDate = $collectDocument['shareDate'];
                $owner = $collectDocument['collectionOwner'];

                echo "<tr class='documents' id='trViewPublicCollection'>
                        <td class='form-text-medium-center' id='tdViewPublicCollectionType'>$type</td>
                        <td class='form-text-large-left' id='tdViewPublicCollectionTitle'>$title</td>
                        <td class='form-text-small-center' id='tdViewPublicCollectionExtension'>$extension</td>
                        <td class='form-text-small-center' id='tdViewPublicCollectionSize'>$size</td>
                        <td class='form-text-medium-center' id='tdViewPublicCollectionShareDate'>$shareDate</td>
                        <td class='form-text-small-center' id='tdViewPublicCollectionDownload'>
                            <input class='form-submit-small-center-gray' id='subViewPublicCollectionDownload' type='submit' name='downloadCollectDocument[$collectionDocumentID]' value='Download'>
                        </td>";
                
                if ($owner == $this->collectionUser) {
                    echo "<td class='form-text-small-center' id='tdViewPublicCollectionDelete'>
                            <input class='form-submit-small-center-gray' id='subViewPublicCollectionDelete' type='submit' name='deleteCollectDocument[$collectionDocumentID]' value='Delete'>
                        </td>";
                } else {
                    echo "<td class='form-text-small-center' id='tdViewPublicCollectionDelete'>
                            <input class='form-submit-small-center-gray' id='subViewPublicCollectionDelete' type='submit' name='deleteCollectDocument[$collectionDocumentID]' value='Delete' disabled>
                        </td>";
                }
                
                echo "</tr>";
            }

            echo "</tbody></table></div>";
        }
    }
    
    // function to get public collection count
    
    public function getPublicCollectionCount() {
        $this->getPublicCollections();
        return count($this->publicCollection);
    }
}