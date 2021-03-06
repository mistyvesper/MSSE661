<?php

/**
 * Description
 * 
 * Displays document upload page. 
 * 
 * @author misty
 */
    
    require_once 'header.php';
    
    // check file upload count - only allow up to 10 files at a time
    
    if (!isset($_SESSION['fileUploadCount'])) {
        $_SESSION['fileUploadCount'] = 1;
    }
        
    // display form

    echo "<span class='documents' id='spnUploadDocuments'>
            <h1 class='documents' id='hrUploadDocuments'>Upload Documents</h1>
            <label class='documents' id='lblUploadDocuments'>**Only DOC, DOCX, PDF, and TXT file types < 25 MB in size may be uploaded**</label>
            <br>
                <br>
                <form class='documents' id='frmUploadDocuments' method='post' action='uploadDocument.php' enctype='multipart/form-data'>
                    <table class='documents' id='tblUploadDocuments'>
                        <thead class='documents' id='theadUploadDocuments'>
                            <tr class='documents' id='trUploadDocumentsTypeHeaders'>
                                <th class='form-submit-medium-header-center' id='thUploadDocumentsTypeHeader'>Document Type</th>
                                <th class='form-submit-extralarge-header-left' id='tdUploadDocumentsSelectFileHeader'>Select File</th>
                            </tr>
                        </thead>
                    <tbody class='documents' id='tbodyUploadDocuments'>";

    for ($i = 0; $i< $_SESSION['fileUploadCount']; $i++) {
        $collection->showUploadForm($i);
    }
                
    echo "</tbody></table>
            <input class='form-submit-small-center-gray' id='subUploadDocumentsAddFileUpload' type='submit' name='addFileUpload' value='Add'>
            <br><br>
            <input class='form-submit-button' id='subUploadDocumentsUpload' type='submit' name='uploadDocs' value='Upload'>
            <input class='form-submit-button' id='subUploadDocumentsCancel' type='submit' name='cancelUploadDocs' value='Cancel'>
        </form>";
    
    // display error messages
                
    if (isset($_SESSION['displayMessages'])) {
        foreach($_SESSION['displayMessages'] as $displayMessage) {
            echo "<br>";
            echo $displayMessage;
            echo "<br>";
        }  
    }    
    
    echo "</span></body></html>";
