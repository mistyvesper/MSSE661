<?php

/**
 * Description
 * 
 * Displays document upload page. 
 * 
 * @author misty
 */
    
    require_once 'header.php';
    
    if (!isset($_SESSION['fileUploadCount'])) {
        $_SESSION['fileUploadCount'] = 1;
    }
        
    // display form

    echo "<h1>Upload Documents</h1>
            <br>
            <div><form method='post' action='uploadDocument.php' enctype='multipart/form-data'>
            <table><tr>
                <th align='left' style='width:100px'>Document Type</th>
                <th align='left'>Select File</th>
            </tr>";

    for ($i = 0; $i< $_SESSION['fileUploadCount']; $i++) {
        $collection->showUploadForm($i);
    }
                
    echo "</table><input type='submit' name='addFileUpload' value='Add'>
            <br><br><input type='submit' name='uploadDocs' value='Upload'>
                <input type='submit' name='cancelUploadDocs' value='Cancel'>
                </form></div>";
                
    if (isset($_SESSION['displayMessage'])) {
        foreach($_SESSION['displayMessage'] as $displayMessage) {
            echo $displayMessage;
            echo "<br>";
        }  
    }    
    
    echo "</body>
        </html>";
