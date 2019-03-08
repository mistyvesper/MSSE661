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
    require_once($rootDirectory . '/Models/User.php');
    require_once($rootDirectory . '/Models/Users.php');
    require_once($rootDirectory . '/Resources/accountMaintenance.php');
    require_once($rootDirectory . '/Resources/authentication.php');
    require_once($rootDirectory . '/Resources/fileMaintenance.php');
    require_once($rootDirectory . '/Resources/sanitize.php');
    require_once($rootDirectory . '/Resources/databaseMaintenance.php');
    
    // initialize database and display messages
    
    $database = new Database($dbHost, $dbUser, $dbPassword, $dbDBName);
    $infoMessage = new InfoMessage();
    unset($_SESSION['displayMessage']);
    
    // check if get request received
    
    if (isset($_GET)) {
        foreach ($_GET as $get) {
          if ($get == "netbeans-xdebug") {
              continue;
          } else {
              $getRequest = true;
          }
        }  
    }

    // check if user is logged in
    
    if (isset($_SESSION['user'])) {
        $appUser = $_SESSION['user'];
        $loggedIn = TRUE;
        $userDirectory = $rootDirectory . '/uploadedFiles/' . $appUser . '/';
        $userShareDirectory = $userDirectory . '/sharedFiles/';
        createDirectory($userDirectory);
        createDirectory($userShareDirectory);
        
        // instantiate objects common to all pages
        
        $collection = new Collection($appUser, $database);
        $messages = new Messages($appUser, $database);
        $users = new Users($appUser, $database);
        
        // get documents
    
        $collection->getDocuments();
        $collection->getPendingSharedDocuments();
        $collection->getReceivedDocuments();
        
    } else if (strpos($_SERVER['SCRIPT_NAME'], "loginPage.php") > 0) {
        $loggedIn = FALSE;
    } else if (strpos($_SERVER['SCRIPT_NAME'], "registerForAccount.php") > 0 
                || strpos($_SERVER['SCRIPT_NAME'], "accountCreated.php") > 0
                || strpos($_SERVER['SCRIPT_NAME'], "registerForAccount.php") > 0
                || strpos($_SERVER['SCRIPT_NAME'], "logoutPage.php") > 0) {
        $loggedIn = FALSE;
    } else if (!$getRequest) {
        header('Location: loginPage.php');
        $loggedIn = FALSE;
    }      
    
    // check for submitted values to redirect pages as needed
    
/**********************************************************************************************************
 * LOGIN PAGE
 **********************************************************************************************************/
    
    if (isset($_POST['login'])) {       
        validateUser($_POST['userName'], $_POST['password'], $database);
    }

/**********************************************************************************************************
 * ACCOUNT REGISTRATION
 **********************************************************************************************************/
    
    if (isset($_POST['registerForAccount'])) {
        
        if (validateAccountRegistration($_POST['email'], $_POST['userName'], $_POST['password'], $database)) {
            if (createUserDirectory($_POST['userName'], $uploadDirectory)) {
                if (createAccount($_POST['email'], $_POST['userName'], $_POST['password'], $database)) {
                    sendAccountCreationEmail($_POST['email']);
                    header('Location: accountCreated.php');
                }
            }
        }
    }
    
/**********************************************************************************************************
 * MY DOCUMENTS
 **********************************************************************************************************/

    // share documents
    
    if (isset($_POST['share'])) { 
        if (isset($_POST['document'])) {
            $collection->addSharedDocuments($_POST['document']);
        }
        $_SESSION['sendingLocation'] = 'index.php';
        header('Location: shareDocuments.php');        
    } 
    
    // delete documents
    
    if (isset($_POST['delete']) && isset($_POST['document'])) {
        $collection->deleteDocuments($_POST['document']);
    }
    
    // update documents
    
    if (isset($_POST['updateDoc'])) {
        $collection->updateDocuments();
    }
    
    // download document 
    
    if (isset($_POST['downloadDocument'])) {
        $collection->downloadDocument(key($_POST['downloadDocument']));
    }
    
    // search for documents
    
    if (isset($_POST['searchValue']) && $_POST['searchValue'] != '') {
        $searchValue = strtolower(sanitizeString($_POST['searchValue']));
        $_SESSION['postedSearchValue'][] = $searchValue;
        $collection->searchCollection($searchValue);
    } 
    
    // clear search results
    
    if (isset($_POST['clearSearch'])) {
        unset($_SESSION['collection']);
        unset($_SESSION['searchCollection']);
        unset($_SESSION['searchValue']);
        unset($_SESSION['postedSearchValue']);
        $collection->getDocuments();
    }
    
    // sort documents
    
    if (isset($_POST['sortDoc'])) {
        $_SESSION['ascending'] = ($_SESSION['ascending']) ? false : true;
        $collection->sortCollection($_POST['sortDoc'], $_SESSION['ascending']);       
    }
    
    // upload documents
    
    if (isset($_POST['upload'])) {
        $_SESSION['fileUploadCount'] = 1;
        header('Location: uploadDocument.php');
    }
    
/**********************************************************************************************************
 * PENDING SHARED DOCUMENTS
 **********************************************************************************************************/
    
    // add pending shared documents
    
    if (isset($_POST['addPendingSharedDocs'])) {
        header('Location: index.php');
    }

    // send shared documents
    
    if (isset($_POST['send'])) {
        $collection->sendSharedDocuments();
        header('Refresh: 0; URL=index.php');
    }
    
    // delete pending shared documents
    
    if (isset($_POST['deletePendingSharedDocs']) && isset($_POST['document'])) {
        $collection->deletePendingSharedDocuments();
        header('Location: shareDocuments.php');
    }
    
    // cancel sharing
    
    if (isset($_POST['cancelShare']) && isset($_SESSION['pendingSharedCollection'])) {
        $collection->deletePendingSharedDocuments();
        if (isset($_SESSION['sendingLocation']) && $_SESSION['sendingLocation'] == 'viewAllFriends.php') {
            header('Location: viewAllFriends.php');
        } else {
            header('Location: index.php');
        } 
    }
    
/**********************************************************************************************************
 * PENDING COLLECTION DOCUMENTS
 **********************************************************************************************************/  
    
    // add documents to pending public collection docs
    
    if (isset($_POST['addToPendingPublicCollection'])) {

        if (isset($_POST['document'])) {
            $collection->addDocumentsToPendingPublicCollection();
        }
        
        // redirect to add to collection page as needed
        
        if (!isset($_SESSION['displayMessage'])) {
            header('Location: addToCollection.php');
        } 
    }
    
    // add more documents to pending public collection docs
    
    if (isset($_POST['addPendingPublicCollectionDocs'])) {
        header('Location: index.php');
    }
    
    // delete pending collection documents
    
    if (isset($_POST['deletePendingPublicCollectionDocs'])) {
        $collection->deletePendingPublicCollectionDocuments();
    }
    
    // cancel add documents to collection 
    
    if (isset($_POST['cancelAddtoPublicCollection'])) {
        $collection->cancelAddDocumentToPublicCollection();
        header('Location: index.php');
    }
    
    // add documents to collection
    
    if (isset($_POST['addToCollection'])) {
        $collection->addDocumentsToPublicCollection();
    }    
    
/**********************************************************************************************************
 * UPLOAD DOCUMENTS
 **********************************************************************************************************/
    
    // add file upload
    
    if ($_POST['addFileUpload']) {
        if ($_SESSION['fileUploadCount'] <= 10) {
            $_SESSION['fileUploadCount']++;
        }
    }
    
    // upload documents 
    
    if ($_POST['uploadDocs']) {
        $collection->uploadDocuments($userDirectory); 
    } 
    
    // cancel upload documents
    
    if ($_POST['cancelUploadDocs']) {
        $_SESSION['fileUploadCount'] = 1;
        header('Location: index.php');
    }

/**********************************************************************************************************
 * RECEIVED DOCUMENTS
 **********************************************************************************************************/

    // add received documents to collection
    
    if (isset($_POST['addToMyCollection']) && isset($_POST['document'])) {
        $collection->addReceivedDocumentsToCollection();
    }
    
    // close received message
    
    if (isset($_POST['closeReceivedMessage'])) {
        header('Location: viewAllReceivedMessages.php');
    }
    
    // download received document
    
    if (isset($_POST['downloadReceivedDocument'])) {
        $collection->downloadReceivedDocument();
    }
    
    // view received message
    
    if (isset($_POST['viewReceivedMessage'])) {
        $_SESSION['messageID'] = key($_POST['viewReceivedMessage']);
        $messages->readMessage($_SESSION['messageID']);
        header("Location: viewReceivedMessage.php");
    }

    // delete received messages
    
    if (isset($_POST['deleteReceivedMessages']) && isset($_POST['messages'])) {
        $messages->deleteReceivedMessages();
    }
    
/**********************************************************************************************************
 * SHARED DOCUMENTS
 **********************************************************************************************************/
    
    // download shared document
    
    if (isset($_POST['downloadSharedDocument'])) {
        $collection->downloadSharedDocument();
    }
    
    // view sent message
    
    if (isset($_POST['viewSentMessage'])) {
        $_SESSION['messageID'] = key($_POST['viewSentMessage']);
        header("Location: viewSentMessage.php");
    }
    
    // delete sent messages
    
    if (isset($_POST['deleteSentMessages']) && isset($_POST['messages'])) {
        $messages->deleteSentMessages();
    } 
    
    // close sent message
    
    if (isset($_POST['closeSentMessage'])) {
        header('Location: viewAllSentMessages.php');
    }

/**********************************************************************************************************
 * PUBLIC COLLECTIONS
 **********************************************************************************************************/

    // add public collection
    
    if (isset($_POST['addPublicCollection'])) {
        
        // check for errors and add collection
        
        if (!isset($_POST['publicCollectionTitle']) || strlen($_POST['publicCollectionTitle']) == 0 ) {
            $_SESSION['displayMessage'] = InfoMessage::publicCollectionBlankTitle();
        } else {
            $title = sanitizeString($_POST['publicCollectionTitle']);
            $description = sanitizeString($_POST['publicCollectionDescription']);
            $collection->addPublicCollection($title, $description);
        }
    }
    
    // delete public collection
    
    if (isset($_POST['deletePublicCollection'])) {
        
        // get collection ID
        
        $collectID = key($_POST['deletePublicCollection']);
        $collectTitle = $collection->getPublicCollectionTitle($collectID);
        
        // check for errors and delete collection
        
        if ($collection->deletePublicCollection($collectID)) {
            $_SESSION['displayMessage'] = InfoMessage::publicCollectionDeleted($collectTitle);
        } else {
            $_SESSION['displayMessage'] = InfoMessage::publicCollectionNotDeleted($collectTitle);
        }
    }
    
    // view public collection
    
    if (isset($_POST['viewPublicCollection'])) {
        
        unset($_SESSION['collectionID']);
        $_SESSION['collectionID'] = key($_POST['viewPublicCollection']);
        header('Location: viewPublicCollection.php');        
    }
    
    // add documents to public collection
    
    if (isset($_POST['addPublicCollectionDocs'])) {
        header('Location: index.php');
    }
    
    // close public collection
    
    if (isset($_POST['closePublicCollectionDocs'])) {
        header('Location: managePublicCollections.php');
    }
    
    // download collection document
    
    if (isset($_POST['downloadCollectDocument'])) {
        $collection->downloadPublicCollectionDocument();
    }
    
    // delete collection document
    
    if (isset($_POST['deleteCollectDocument'])) {
        
        $collectDocID = key($_POST['deleteCollectDocument']);
        $collectDocument = $collection->getPublicCollectionDocByID($collectDocID);
        $collection->deletePublicCollectionDocument($collectDocument);
    }
    
 /**********************************************************************************************************
 * MANAGE PROFILE
 **********************************************************************************************************/   
    
    // updated profile
    
    if (isset($_POST['updateProfile'])) {
        
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
    
    if (isset($_POST['updatePassword'])) {
        
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

 /**********************************************************************************************************
 * MANAGE FRIENDS
 **********************************************************************************************************/    
    
    // accept friend request
    
    if (isset($_POST['acceptFriendRequest'])) {
        
        $messageID = $_SESSION['messageID'];
        $users->acceptFriendRequest($messageID);
    }
    
    // ignore friend request
    
    if (isset($_POST['ignoreFriendRequest'])) {
        
        $messageID = $_SESSION['messageID'];
        
        if ($users->ignoreFriendRequest($messageID)) {
            header('Location: viewAllReceivedMessages.php');
        } else {
            $_SESSION['displayMessage'] = InfoMessage::friendsRequestNotIgnored();
        }
    }
    
    // send message
    
    if (isset($_POST['sendMessage'])) {
        
        $user = $users->getUserByID(key($_POST['sendMessage']));
        $_SESSION['sendTo'] = $user['userName'];
        $collection->deletePendingSharedDocuments();
        $_SESSION['sendingLocation'] = 'viewAllFriends.php';
        header('Location: shareDocuments.php');
        
    }
    
    // delete friend
    
    if (isset($_POST['deleteFriends'])) {
        
        foreach ($_POST['deleteFriends'] as $key => $friend) {
            $friendUserName = $users->getUserByID($key)['userName'];
            $users->deleteFriend($friendUserName);
        }
    }
    
    // cancel sending message
    
    if (isset($_POST['cancelShare']) && !isset($_SESSION['pendingSharedCollection'])) {
        header('Location: viewAllFriends.php');
    }
    
 /**********************************************************************************************************
 * VIEW ALL USERS
 **********************************************************************************************************/
    
    // send friend request
    
    if (isset($_POST['requestFriend'])) {
        
        $userID = key($_POST['requestFriend']);
        $user = $users->getUserByID($userID);
        
        if ($users->sendFriendRequest($appUser, $user['userName'], date('Y-m-d h:i:s'))) {
            $_SESSION['displayMessage'] = InfoMessage::friendsRequestSent($user['userName']);
        } else {
            $_SESSION['displayMessage'] = InfoMessage::friendsRequestNotSent($user['userName']);
        }
    }
    
    // display page header
    
    if ((!strpos($_SERVER['SCRIPT_NAME'], "loginPage.php") 
            && !strpos($_SERVER['SCRIPT_NAME'], "logoutPage.php")
            && !strpos($_SERVER['SCRIPT_NAME'], "registerForAccount.php")
            && !strpos($_SERVER['SCRIPT_NAME'], "accountCreated.php")) 
            && $loggedIn) {
        
        // capitalize appUser
    
        $upperAppUser = strtoupper($appUser);
        
        echo "<!DOCTYPE html>
                <html>
                    <head>
                        <meta charset='UTF-8'>
                        <title>Nano-docs</title>
                        <style>
                            @import url('/Stylesheets/main.css');
                        </style>
                        <style>
                            td {
                                overflow: hidden;
                            }
                            #subMyDocumentsTitleHeader {
                                width: 354px;
                            }
                            #trMyDocumentsContent:hover {
                                background-color: whitesmoke;
                            }
                            #trPendingShareDocs:hover {
                                background-color: whitesmoke;
                            }
                            #trPendingPublicDocs:hover {
                                background-color: whitesmoke;
                            }
                            #trViewAllFriends:hover {
                                background-color: whitesmoke;
                            }
                            #trViewAllUsers:hover {
                                background-color: whitesmoke;
                            }
                            #trViewPublicCollection:hover {
                                background-color: whitesmoke;
                            }
                            #trShowPublicCollections:hover {
                                background-color: whitesmoke;
                            }
                            #trReceivedMessages:hover {
                                background-color: whitesmoke;
                            }
                            #trReceivedDocumentsMsg:hover {
                                background-color: whitesmoke;
                            }
                            #trSentMessages:hover {
                                background-color: whitesmoke;
                            }
                            #trSentMessagesMsg:hover {
                                background-color: whitesmoke;
                            }
                            #trUploadDocumentsType {
                                background-color: whitesmoke;
                            }
                            #spnFriendsAndUsers {
                                position: absolute;
                                top: 25%;
                                left: 4%;
                            }
                            #spnManagePublicCollections {
                                position: absolute;
                                top: 25%;
                                left: 5%;
                            }
                            #spnViewPublicCollection {
                                position: absolute;
                                top: 25%;
                                left: 5%;
                            }
                            #spnReceivedMessages {
                                position: absolute;
                                top: 25%;
                                left: 7%;
                            }
                            #spnMessageReceived {
                                position: absolute;
                                top: 25%;
                                left: 5%;
                            }
                            #spnSentMessagesMsg {
                                position: absolute;
                                top: 25%;
                                left: 5%;
                            }
                        </style>
                        <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css'>
                        <script src='http://code.jquery.com/jquery-latest.min.js'></script>
                        <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'
                            integrity='sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30='crossorigin='anonymous'>
                        </script>
                        <script type='text/javascript' charset='utf8' src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js'></script>
                        <script src='/JavaScript/searchCollection.js'></script>
                        <script src='/JavaScript/pagination.js'></script>
                        <script src='/JavaScript/checkForUpdates.js'></script>
                    </head>
                    <body class='body-loggedin' id='bdyLoggedIn'>
                        <span class='loggedIn' id='spnLoggedIn'>
                            <h1 class='loggedInHeader' id='hdrLoggedInHeader'>Nano-docs</h1>
                            <table class='loggedIn' id='tblLoggedIn'>
                                <tr class='loggedInTop' id='trLoginInfo'>
                                    <td class='loggedInTop' id='tdPersonIcon'><img class='img' id='imgPersonIcon' src='Media/person_icon.png' style='width:35px;height:35px;'></td>
                                    <td class='loggedInTop' id='tdSignedInAs' style='width:18%'>Signed in as: <lbl class='lbl' id='lblSignedInAs'>$upperAppUser</lbl></td>
                                    <td class='loggedInTop' id='tdLogOutLink' style='width:80%' align='right'><a class='link' id='lnkLogOutLink' href='logoutPage.php'>Logout</a></td>
                                </tr>
                                <tr class='loggedInBottom' id='trMenuIcons'>
                                    <td class='loggedInBottom' id='tdMenuIcons' colspan='3' align='right'>
                                        <div class='tooltip' div='divHome'>
                                            <span class='tooltiptext'>Home</span>
                                            <a class='link' id='lnkHome' href='index.php'>
                                                <img class='img' id='imgHomeIcon' src='Media/home.png' style='width:35px;height:35px;'>
                                            </a>
                                        </div>
                                        <div class='tooltip' id='divViewAllFriends'>
                                            <span class='tooltiptext'>Manage Friends</span>
                                            <a class='link' id='lnkFriends' href='viewAllFriends.php'>
                                                <img class='img' id='imgFriendsIcon' src='Media/friends.png' style='width:45px;height:45px;'>
                                            </a>
                                        </div>
                                        <div class='tooltip' id='divManageCollections'>
                                            <span class='tooltiptext'>Manage Collections</span>
                                            <a class='link' id='lnkCollections' href='managePublicCollections.php'>
                                                <img class='img' id='imgCollectionsIcon' src='Media/collections.png' style='width:30px;height:30px;'>
                                            </a>
                                        </div>
                                        <div class='tooltip' id='divMessages'>
                                            <span class='tooltiptext'>View Messages</span>
                                            <a class='link' id='lnkMessages' href='viewAllReceivedMessages.php'>
                                                <img class='img' id='imgMailIcon' src='Media/mail.png' style='width:35px;height:35px;'>
                                            </a>
                                        </div>
                                        <div class='tooltip' id='divManageProfile'>
                                            <span class='tooltiptext'>Manage Profile</span>
                                            <a class='link' id='lnkProfile' href='manageProfile.php'>
                                                <img class='img' id='imgSettingsIcon' src='Media/settings.png' style='width:35px;height:35px;'>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </span>";  
                                                
    }