<?php

/**
 * Description
 * 
 * Provides functionality common to all web app pages.
 * 
 * @author misty
 */

    // start session

    session_start();
    
    // get root directory 
    
    $rootDirectory = dirname(__FILE__);
    
    // get dependencies for all pages
    
    require_once($rootDirectory . '/App/login.php');
    require_once($rootDirectory . '/App/config.php');
    require_once($rootDirectory . '/Models/Collection.php');
    require_once($rootDirectory . '/Models/Database.php');
    require_once($rootDirectory . '/Models/Document.php');
    require_once($rootDirectory . '/Models/InfoMessage.php');
    require_once($rootDirectory . '/Models/Message.php');
    require_once($rootDirectory . '/Models/Messages.php');
    require_once($rootDirectory . '/Resources/accountMaintenance.php');
    require_once($rootDirectory . '/Resources/authentication.php');
    require_once($rootDirectory . '/Resources/fileMaintenance.php');
    require_once($rootDirectory . '/Resources/sanitize.php');
    require_once($rootDirectory . '/Resources/databaseMaintenance.php');

    // check if user is logged in
    
    if (isset($_SESSION['user'])) {
        $appUser = $_SESSION['user'];
        $loggedIn = TRUE;
        $userDirectory = $rootDirectory . '/uploadedFiles/' . $appUser . '/';
        $userShareDirectory = $userDirectory . '/sharedFiles/';
        createDirectory($userDirectory);
        createDirectory($userShareDirectory);
    } else if (strpos($_SERVER['SCRIPT_NAME'], "loginPage.php") > 0) {
        $loggedIn = FALSE;
    } else {
        header('Location: loginPage.php');
        $loggedIn = FALSE;
    }
    
    // instantiate objects common to all pages

    $database = new Database($dbHost, $dbUser, $dbPassword, $dbDBName);
    $collection = new Collection($appUser, $database);
    $messages = new Messages($appUser, $database);
    $infoMessage = new InfoMessage();
    unset($_SESSION['displayMessage']);
    
    
    
    // check for submitted values to redirect pages as needed
    
    // shared documents
    
    if (isset($_POST['share']) && isset($_POST['document'])) { 
        foreach ($_POST['document'] as $document) {
            $sharedDocument = $collection->getDocumentByID($document);
            $collection->addSharedDocument($sharedDocument);
        }
        header('Location: shareDocuments.php');        
    } 
    
    // send shared documents
    
    if (isset($_POST['send'])) {
        
        $shareWith = sanitizeString($_POST['shareWithUser']);
        $subject = sanitizeString($_POST['subject']);
        $body = sanitizeString($_POST['body']);
        $sharedDocuments = $_SESSION['pendingSharedCollection'];
        $_SESSION['sharedDate'] = date('Y-m-d h:i:s');
        $sharedDate = $_SESSION['sharedDate'];
        
        if (strlen($shareWith) == 0 || !checkForExistingAccount($shareWith, $database)) {
            $_SESSION['displayMessage'] = InfoMessage::missingTo();
        } else if ($collection->shareDocuments($subject, $body, $shareWith, $sharedDocuments, $sharedDate)) {
            unset($_SESSION['pendingSharedCollection']);
            $_SESSION['pendingSharedCollection'] = $collection->getPendingSharedDocuments();
            $_SESSION['displayMessage'] = InfoMessage::messageSent($shareWith);
            header('Refresh: 0; URL=index.php');
        } else {
            $_SESSION['displayMessage'] = InfoMessage::messageNotSent();
        }
    }
    
    // cancel sharing
    
    else if (isset($_POST['cancelShare']) && isset($_SESSION['pendingSharedCollection'])) {
        foreach ($_SESSION['pendingSharedCollection'] as $sharedDocument) {
            $collection->deletePendingSharedDocument($sharedDocument);
        }
        header('Location: index.php');
    }
    
    // add pending shared documents
    
    else if (isset($_POST['addPendingSharedDocs'])) {
        header('Location: index.php');
    }
    
    // delete pending shared documents
    
    else if (isset($_POST['deletePendingSharedDocs']) && isset($_POST['document'])) {
        foreach ($_POST['document'] as $docID) {
            $sharedDocument = $collection->getSharedDocumentByID($docID);
            $collection->deletePendingSharedDocument($sharedDocument);
        }
        header('Location: shareDocuments.php');
    }
    
    // add received documents to collection
    
    else if (isset($_POST['addToMyCollection']) && isset($_POST['document'])) {
        foreach ($_POST['document'] as $receivedDocID) {
            $receivedDocument = $collection->getReceivedDocumentByID($receivedDocID);
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
            } else if ($collection->addReceivedDocumentToCollection($receivedDocument)) {  
                $_SESSION['displayMessage'][] = InfoMessage::documentsAdded();
            } else {
                $_SESSION['displayMessage'][] = InfoMessage::documentsNotAdded();
            }
        }
    }
    
    // close received message
    
    else if (isset($_POST['closeReceivedMessage'])) {
        header('Location: viewAllReceivedMessages.php');
    }
    
    // close sent message
    
    else if (isset($_POST['closeSentMessage'])) {
        header('Location: viewAllSentMessages.php');
    }
    
    // delete documents
    
    else if (isset($_POST['delete']) && isset($_POST['document'])) {

        $error = false;
        
        foreach ($_POST['document'] as $docID) {
            $document = $collection->getDocumentByID($docID);
            if (!$collection->deleteDocument($document)) {
                $error = true;
            }
        }
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::documentsNotDeleted();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::documentsDeleted();
        }
        
    }
    
    // update documents
    
    else if (isset($_POST['updateDoc'])) {
        
        $error = false;
        $updateCount = 0;
        
        foreach ($_SESSION['collection'] as $key => $document) {
            $newType = $_POST['docType'][$key];
            $newTitle = $_POST['docTitle'][$key];
            
            if ($document['type'] != $newType || $document['title'] != $newTitle) {
                if (!$collection->updateDocument($document, $newType, $newTitle)) {
                    $error = true;
                } else {
                    $updateCount++;
                }
            }
        }
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::documentsNotUpdated();
        } else if ($updateCount > 0) {
            $_SESSION['displayMessage'] = InfoMessage::documentsUpdated();
        } 
    }
    
    // download document 
    
    else if (isset($_POST['downloadDocument'])) {
        
        $documentID = key($_POST['downloadDocument']);
        $document = $collection->getDocumentByID($documentID);
        $type = $document['type'];
        $extension = strtolower($document['extension']);
        $title = $document['title'];
        $file = "$rootDirectory/uploadedFiles/$appUser/$type/$title.$extension";
        
        downloadFile($file);
    }
    
    // download received document
    
    else if (isset($_POST['downloadReceivedDocument'])) {
        
        $receivedDocumentID = key($_POST['downloadReceivedDocument']);
        $receivedDocument = $collection->getReceivedDocumentByID($receivedDocumentID);
        $type = $receivedDocument['type'];
        $extension = strtolower($receivedDocument['extension']);
        $title = $receivedDocument['title'];
        $receivedFrom = $receivedDocument['receivedFrom'];
        $receivedDate = str_replace('-', '', str_replace(' ', '', str_replace(':', '', $receivedDocument['receivedDate'])));
        $file = "$rootDirectory/uploadedFiles/$receivedFrom/sharedFiles/$receivedDate/$title.$extension";
        
        downloadFile($file);
    }
    
    // download shared document
    
    else if (isset($_POST['downloadSharedDocument'])) {
        
        $sharedDocumentID = key($_POST['downloadSharedDocument']);
        $sharedDocument = $collection->getSharedDocumentByID($sharedDocumentID);
        $type = $sharedDocument['type'];
        $extension = strtolower($sharedDocument['extension']);
        $title = $sharedDocument['title'];
        $sharedWith = $sharedDocument['sharedWith'];
        $sharedDate = str_replace('-', '', str_replace(' ', '', str_replace(':', '', $sharedDocument['sharedDate'])));
        $file = "$rootDirectory/uploadedFiles/$appUser/sharedFiles/$sharedDate/$title.$extension";
        
        downloadFile($file);
    }
    
    // search results
    
    else if (isset($_POST['searchValue']) && $_POST['searchValue'] != '') {
        $searchValue = strtolower(sanitizeString($_POST['searchValue']));
        $collection->searchCollection($searchValue);
        $_SESSION['searchValue'][] = InfoMessage::searchValue($_POST['searchValue']);
    } 
    
    // clear search results
    
    else if (isset($_POST['clearSearch'])) {
        unset($_SESSION['collection']);
        unset($_SESSION['searchValue']);
        $collection->getDocuments();
    }
    
    // sort documents
    
    else if (isset($_POST['sortDoc'])) {
        $_SESSION['ascending'] = ($_SESSION['ascending']) ? false : true;
        switch ($_POST['sortDoc']) {
            case 'Type':
                $collection->sortCollection('type', $_SESSION['ascending']);
                break;
            case 'Title':
                $collection->sortCollection('title', $_SESSION['ascending']);
                break;
            case 'Extension':
                $collection->sortCollection('extension', $_SESSION['ascending']);
                break;
            case 'File Size':
                $collection->sortCollection('size', $_SESSION['ascending']);
                break;
            case 'Upload Date':
                $collection->sortCollection('uploadDate', $_SESSION['ascending']);
                break;
        }       
    }
    
    // updated profile
    
    else if (isset($_POST['updateProfile'])) {
        
        unset($_SESSION['displayMessage']);
        $profileUserName = sanitizeString($_POST['profileUserName']);
        $profileEmail = sanitizeString($_POST['profileEmail']);
        $profileFirstName = sanitizeString($_POST['profileFirstName']);
        $profileLastName = sanitizeString($_POST['profileLastName']);
        
        if (strlen($profileUserName) == 0 && strlen($profileEmail) == 0) {
            $_SESSION['displayMessage'] = InfoMessage::invalidEntries();
        } else if (strlen($profileUserName) == 0) {
            $_SESSION['displayMessage'] = InfoMessage::missingUser();
        } else if (strlen($profileEmail) == 0) {
            $_SESSION['displayMessage'] = InfoMessage::invalidEmail();
        } else if (updateAccount($appUser, $profileUserName, $profileEmail, $profileFirstName, $profileLastName, $database)) {
            $appUser = $profileUserName;   
            $_SESSION['user'] = $profileUserName;
            $_SESSION['displayMessage'] = InfoMessage::profileUpdated();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::profileUpdateFailed();
        }      
    }
    
    // change password
    
    else if (isset($_POST['updatePassword'])) {
        
        unset($_SESSION['displayMessage']);
        $oldPassword = sanitizeString($_POST['oldPassword']);
        $newPassword1 = sanitizeString($_POST['newPassword1']);
        $newPassword2 = sanitizeString($_POST['newPassword2']);
        
        if (strlen($oldPassword) == 0 || strlen($newPassword1) == 0 || strlen($newPassword2) == 0) {
            $_SESSION['displayMessage'] = InfoMessage::invalidEntries();
        } else if (!validateUser($appUser, $oldPassword, $dbConnection)) {
            $_SESSION['displayMessage'] = InfoMessage::invalidPassword();
        } else if ($newPassword1 != $newPassword2) {
            $_SESSION['displayMessage'] = InfoMessage::passwordsDontMatch();
        } else if (updatePassword($appUser, $newPassword1, $database)) {
            $_SESSION['displayMessage'] = InfoMessage::passwordUpdated();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::passwordUpdateFailed();
        }
    }
    
    // add file upload
    
    else if ($_POST['addFileUpload']) {
        if ($_SESSION['fileUploadCount'] <= 10) {
            $_SESSION['fileUploadCount']++;
        }
    }
    
    // upload documents 
    
    else if ($_POST['uploadDocs']) {
        
        $i = 0;
        $documentCount = $collection->getDocumentCount();
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
            } else if ($file['error'] == 0 || $docType != '') {

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

                $collection->addDocument($document);

                // check if document successfully added to database

                if ($collection->getDocumentCount() == $documentCount + 1) {

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
                        $_SESSION['displayMessage'][] = InfoMessage::fileUploadFailed($file['name']);
                    }
                } else if ($collection->getDocumentID($document) > 0) {
                    $error = true;
                    $_SESSION['displayMessage'][] = InfoMessage::fileDuplicate($file['name']);
                } else if ($docExtension == '') {
                    $error = true;
                    $_SESSION['displayMessage'] = InfoMessage::fileUnsupported($file['name']);
                } else {
                    $error = true;
                    $_SESSION['displayMessage'] = InfoMessage::fileUploadFailed($file['name']);
                }
            } else if ($file['error'] == 1 || $file['error'] == 2) {
                $error = true;
                $_SESSION['displayMessage'] = InfoMmessage::fileExceedsMaxSize($file['name']);
            } else if ($file['error'] > 3) {
                $error = true;
                $_SESSION['displayMessage'] = InfoMessage::fileUploadFailed($file['name']);
            } 

            $i++;
        }
        
        if ($documentCount == $collection->getDocumentCount()) {
            $_SESSION['displayMessage'][] = InfoMessage::fileNoFilesSelected();
        } else if (!$error) {
            $_SESSION['displayMessage'][] = InfoMessage::fileUploadSuccessful();
            header('Refresh: 0; URL=index.php');
        } 
    } 
    
    // cancel upload documents
    
    else if ($_POST['cancelUploadDocs']) {
        $_SESSION['fileUploadCount'] = 1;
        header('Location: index.php');
    }
    
    // view received message
    
    else if (isset($_POST['viewReceivedMessage'])) {
        $_SESSION['messageID'] = key($_POST['viewReceivedMessage']);
        $messages->readMessage($_SESSION['messageID']);
        header("Location: viewReceivedMessage.php");
    }
    
    // view sent message
    
    else if (isset($_POST['viewSentMessage'])) {
        $_SESSION['messageID'] = key($_POST['viewSentMessage']);
        header("Location: viewSentMessage.php");
    }
    
    // delete read messages
    
    else if (isset($_POST['deleteReceivedMessages']) && isset($_POST['messages'])) {
        
        $error = false;
        
        foreach ($_POST['messages'] as $item) {
            if (!$messages->deleteReceivedMessage($item)) {
                $error = true;
            }
        }
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::messagesNotDeleted();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::messagesDeleted();
        }
    }
    
    // delete sent messages
    
    else if (isset($_POST['deleteSentMessages']) && isset($_POST['messages'])) {
        
        $error = false;
        
        foreach ($_POST['messages'] as $item) {
            if (!$messages->deleteSentMessage($item)) {
                $error = true;
            }
        }
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::messagesNotDeleted();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::messagesDeleted();
        }
    }
    
    // get documents
    
    $collection->getDocuments();
    $collection->getPendingSharedDocuments();
    $collection->getReceivedDocuments();
    
    // display page header
    
    if ((!strpos($_SERVER['SCRIPT_NAME'], "loginPage.php") 
            && !strpos($_SERVER['SCRIPT_NAME'], "logoutPage.php")
            && !strpos($_SERVER['SCRIPT_NAME'], "registerForAccount.php")
            && !strpos($_SERVER['SCRIPT_NAME'], "accountCreated.php")) 
            && $loggedIn) {
        
        // capitalize appUser
    
        $upperAppUser = strtoupper($appUser);
        
        echo "<html>
                    <head>
                        <meta charset='UTF-8'>
                        <title>Nano-site</title>
                    </head>
                    <body>
                        <div>
                            <table style='width:100%'>
                                <tr>
                                    <td style='width:3%'><img src='Media/person_icon.png' style='width:35px;height:35px;'></td>
                                    <td style='width:18%'>Signed in as: $upperAppUser</td>
                                    <td style='width:80%' align='right'><a href='logoutPage.php'>Logout</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td align='right'><a href='index.php'><img src='Media/home.png' style='width:35px;height:35px;'></a>
                                                        <a href='viewAllReceivedMessages.php'><img src='Media/mail.png' style='width:35px;height:35px;'></a>
                                                        <a href='manageProfile.php'><img src='Media/settings.png' style='width:35px;height:35px;'></a>
                                    </td>
                                </tr>
                            </table>
                        </div>";  
                                                
    }