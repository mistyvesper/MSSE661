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

    echo "<span class='documents' id='spnMessageReceived'>
            <h1 class='documents' id='hdrMessageReceived'>Message Received</h1>";
    
    // show error messages
    
    if ($_SESSION['displayMessage']) {
        
        if (is_array($_SESSION['displayMessage']) > 0) {
            foreach($_SESSION['displayMessage'] as $displayMessage) {
                echo "<br>";
                echo $displayMessage;
                echo "<br>";
            } 
        } else {
            echo "<br>";
            echo $_SESSION['displayMessage'];
            echo "<br>";
        }
    }
    
    // get documents by message id
    
    $collection->getReceivedDocumentsByMessage($_SESSION['messageID']);
    $message = $messages->getReceivedMessageByID($_SESSION['messageID'])->getMessage();
    $messageID = $_SESSION['messageID'];
    $subject = $message['subject'];
    $messages->getReceivedMessageByID($_SESSION['messageID'])->showReceivedMessage();
    
    // show form
    
    echo "<form class='documents' id='frmMessageReceived' method='post' action='viewReceivedMessage.php' enctype='multipart/form-data'>";

    // show Message
    
    $collection->showReceivedCollection();
    
    echo "<br><br>
            <table class='documents' id='tblMessageReceived'>
                <tr class='documents' id='trMessageReceived'>";
    
    if (count($_SESSION['receivedCollection']) > 0) {
        echo "<td class='form-submit-button-large' id='tdMessageReceivedAddDoc'>
                <input class='form-submit-button-large' id='subMessageReceivedAddDoc' type='submit' name='addToMyCollection' value='Add to My Collection'>
            </td>
            <td></td>";
    } else if ($subject == "**New Friend Request**") {
        echo "<td class='form-submit-button' id='tdMessageReceivedAddFriend'>
                <input class='form-submit-button' id='subMessageReceivedAddFriend' type='submit' name='acceptFriendRequest' value='Accept'>
            </td>
            <td></td>";
        echo "<td class='form-submit-button' id='tdMessageReceivedIgnoreFriend'>
                <input class='form-submit-button' id='tdMessageReceivedIgnoreFriend' type='submit' name='ignoreFriendRequest' value='Ignore'>
            </td>
            <td></td>";
    }
    
    echo "<td class='form-submit-button-large' id='tdMessageReceivedClose'>
            <input class='form-submit-button-large' id='subMessageReceivedClose' type='submit' name='closeReceivedMessage' value='Close Message'>
        </td>
    </tr>
</table>
</form>";
    
    // end of web page
    
    echo "</span></body></html>";