<?php

/**
 * Description
 * 
 * Displays document upload page. 
 * 
 * @author misty
 */
    
    require_once 'header.php';
    
    // check if user logged in
    
    if (!$loggedIn) {
        header('Location: loginPage.php');
    }

    // declare and initialize variables

    $database = new Database($dbHost, $dbUser, $dbPassword, $dbDBName);
    $collection = new Collection($appUser, $database);
    $documentCount = $collection->getDocumentCount();
    $docTypes = $collection->getDocumentTypes();
    $docUploadDate = (string)date("Y/m/d");
    $infoMessage = new InfoMessage();
    
    // get docType after form submission
            
    $docType = $_POST['docType'];

    // move files

    if ($_FILES && $_FILES['filename']['error'] == 0) {

        $newDocTypeDirectory = $userDirectory . $docType . '/';
        $sanitizedFileName = sanitizeString($_FILES['filename']['name']);
        $uploadPath = $newDocTypeDirectory . $sanitizedFileName;
        $docTitle = pathinfo($uploadPath)['filename'];

        // get file extension

        $docExtension = getFileExtensionByType($_FILES['filename']['type']);

        // get file size
                
        $docSize = getFriendlyFileSize($_FILES['filename']['size']);

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

            if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadPath)) {
                $infoMessage->fileUploadSuccessful();
            } else {
                $infoMessage->fileUploadFailed();
            };
        } else if ($collection->getDocumentID($document) > 0) {
            $infoMessage->fileDuplicate();
        } else if ($docExtension == '') {
            $infoMessage->fileUnsupported();
        } else {
            $infoMessage->fileUploadFailed();
        }
    } else if ($_FILES && ($_FILES['filename']['error'] == 1 || $_FILES['filename']['error'] == 2)) {
        $infoMmessage->fileExceedsMaxSize();
    } else if ($_FILES && $_FILES['filename']['error'] > 3) {
        $infoMessage->fileUploadFailed();
    }
        
    // display form

    echo "<h1>Upload Documents</h1>
            <br>
            <div><form method='post' action='uploadDocument.php' enctype='multipart/form-data'>
            <table>
                <tr>
                    <th align='left' style='width:100px'>Document Type</th>
                    <th align='left'>Select File</th>
                </tr>
                <tr>
                    <td><select name='docType'>";
    
    foreach ($docTypes AS $docType) {
        echo "<option value='$docType'>$docType</option>";
    }
    
    echo "</select>
                        </td>
                        <td><input type='file' name='filename' accept='.doc, .docx, .txt, .pdf'>
                        </td>
                    </tr>
                </table>
                <br><br><input type='submit' value='Upload'><a href='index.php'>Cancel</a>
                </form></div><br>      
            </body>
        </html>";
