<?php

/**
 * Description
 * 
 * Displays received documents page.  
 * 
 * @author misty
 */

    require_once 'header.php';    
    
    // display web page

    echo "<h1>Received Documents</h1>";
    
    // get documents by message id
    $collection->getReceivedDocumentsByMessage($_SESSION['messageID']);
    $messages->getMessageByID($_SESSION['messageID'])->showMessage();

    // show Message
    
    $collection->showReceivedCollection();
    
    // end of web page
    
    echo "</body>
        </html>";