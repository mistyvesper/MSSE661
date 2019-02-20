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

    echo "<h1>Message Sent</h1>";
    
    // get documents by message id
    $collection->getSentDocumentsByMessage($_SESSION['messageID']);
    $messages->getSentMessageByID($_SESSION['messageID'])->showSentMessage();
    
    echo "<form  method='post' action='viewSentMessage.php' enctype='multipart/form-data'>";

    // show Message
    
    $collection->showSentCollection();
    
    echo "<br><div><table><tr>";
    echo "<td><input type='submit' name='closeSentMessage' value='Close Message'></td></tr></table></div></form>";
    
    // show error messages
    
    if ($_SESSION['displayMessage']) {
        foreach($_SESSION['displayMessage'] as $displayMessage) {
            echo $displayMessage;
            echo "<br>";
        }
    }
    
    // end of web page
    
    echo "</body>
        </html>";