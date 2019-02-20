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

    echo "<h1>Message Received</h1>";
    
    // get documents by message id
    $collection->getReceivedDocumentsByMessage($_SESSION['messageID']);
    $messages->getReceivedMessageByID($_SESSION['messageID'])->showReceivedMessage();
    
    echo "<form  method='post' action='viewReceivedMessage.php' enctype='multipart/form-data'>";

    // show Message
    
    $collection->showReceivedCollection();
    
    echo "<br><div><table><tr>";
    
    if (count($_SESSION['receivedCollection']) > 0) {
        echo "<td><input type='submit' name='addToMyCollection' value='Add to My Collection'></td><td></td>";
    }
    
    echo "<td><input type='submit' name='closeReceivedMessage' value='Close Message'></td></tr></table></div></form>";
    
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