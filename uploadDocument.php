<!DOCTYPE html>
<!--
Copyright (C) 2019 misty

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Document Upload</title>
    </head>
    <body>
        <h1>Upload Documents</h1>
        <br>
        <?php
        
            require_once 'Document.php';
            require_once 'Collection.php';
            require_once 'sanitize.php';
            require_once 'Message.php';
            
            // declare and initialize variables
            
            $dbConnection = new DBConnection('localhost', 'Regis', 'regis', 'collection');
            $collection = new Collection($dbConnection);
            $documentCount = $collection->getDocumentCount();
            $docTypes = $collection->getDocumentTypes();
            $message = new Message();
            $user = 'Regis';
            
            // display form
        
            echo "<div><form method='post' action='uploadDocument.php' enctype='multipart/form-data'>";    
            echo "<table><tr><th align='left' style='width:100px'>Document Type</th>"
                . "<th align='left'>Select File</th></tr>"
                . "<tr><td><select name='docType'>";
            foreach ($docTypes AS $docType) {
                echo "<option value='$docType'>$docType</option>";
            }
            echo "</select></td>"
                . "<td><input type='file' name='filename' accept='.doc, .docx, .txt, .pdf'></td></tr></table>";
            echo "<br><br><input type='submit' value='Upload'>"
                . "<a href='index.php'>Cancel</a></form></div><br>";
            
            // get docType after form submission
            
            $docType = $_POST['docType'];

            // move files
            
            if ($_FILES) {
                
                $tmpUploadDirectory = '/var/www/html/tmpFiles/';
                $newUploadDirectory = '/var/www/html/uploadedFiles/';
                $newUserDirectory = $newUploadDirectory . $user . '/';
                $newDocTypeDirectory = $newUserDirectory . $docType . '/';
                $sanitizedFileName = sanitizeString($_FILES['filename']['name']);
                $uploadPath = $newDocTypeDirectory . $sanitizedFileName;
                $docTitle = pathinfo($uploadPath)['filename'];
                
                // get file extension
                
                switch($_FILES['filename']['type']) {
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':     $docExtension = 'DOCX';  break;
                    case 'application/pdf':                                                             $docExtension = 'PDF';   break;
                    case 'application/msword':                                                          $docExtension = 'DOC';   break;
                    case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':   $docExtension = 'PPTX';  break;
                    case 'text/plain':                                                                  $docExtension = 'TXT';   break;
                    default:                                                                            $docExtension = '';      break;
                }
                
                // get file size
                
                if ($_FILES['filename']['size'] == null) {
                    $docSize = 'Unknown';
                } else if ($_FILES['filename']['size'] > 1000000) {
                    $docSize = $_FILES['filename']['size'] / 1000000 . 'GB';
                } else if ($_FILES['filename']['size'] > 1000) {
                    $docSize = $_FILES['filename']['size'] / 1000 . 'MB';
                } else {
                    $docSize = $_FILES['filename']['size'] . 'KB';
                }
                
                // get uploadDate
                
                $docUploadDate = (string)date("Y/m/d");
                
                // create database connection
        
                $dbConnection = new DBConnection('localhost', 'Regis', 'regis', 'collection');

                // create Collection

                $collection = new Collection($dbConnection);
                
                // create Document
                
                $document = new Document($docType, $docTitle, $docExtension, $docSize, $docUploadDate);

                // add Document to Collection
                
                $collection->addDocument($document);
                
                // check if document successfully added to database
                
                if ($collection->getDocumentCount() == $documentCount + 1) {
                    
                    // create upload directory if it doesn't already exist
                
                    if (!file_exists($newUploadDirectory)) {
                        $old_umask = umask(0);
                        mkdir($newUploadDirectory, 0777);
                        umask($old_umask);
                    }

                    // create user directory if it doesn't already exist

                    if (!file_exists($newUserDirectory)) {
                        $old_umask = umask(0);
                        mkdir($newUserDirectory, 0777);
                        umask($old_umask);
                    }
                    
                    // create document type directory if it doesn't already exist
                    
                    if (!file_exists($newDocTypeDirectory)) {
                        $old_umask = umask(0);
                        mkdir($newDocTypeDirectory, 0777);
                        umask($old_umask);
                    }

                    // upload file to temporary folder and then move to user folder

                    if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadPath)) {
                        $message->fileUploadSuccessful();
                    } else {
                        $message->fileUploadFailed();
                    };
                } else if ($collection->getDocumentID($document) > 0) {
                    $message->fileDuplicate();
                } else if ($docExtension == '') {
                    $message->fileUnsupported();
                } else {
                    $message->fileUploadFailed();
                }
            }
        ?>
    </body>
</html>
