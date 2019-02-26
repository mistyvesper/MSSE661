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

    echo "<span class='documents' id='spnSentMessagesMsg'>
            <h1 class='documents' id='hdrSentMessagesMsg'>Message Sent</h1>";
    
    // get documents by message id
    
    $collection->getSentDocumentsByMessage($_SESSION['messageID']);
    $messages->getSentMessageByID($_SESSION['messageID'])->showSentMessage();
    
    echo "<form class='documents' id='frmSentMessagesMsg' method='post' action='viewSentMessage.php' enctype='multipart/form-data'>";

    // show Message
    
    $collection->showSentCollection();
    
    echo "<br><br>
            <table class='documents' id='tblSentMessagesMsgSubmitButtons'>
                <tr class='documents' id='trSentMessagesMsgSubmitButtons'>
                    <td class='form-submit-button-large' id='tdSentMessagesMsg'>
                        <input class='form-submit-button-large' id='subSentMessagesMsgClose' type='submit' name='closeSentMessage' value='Close Message'>
                    </td>
                </tr>
            </table>
        </form>";
    
    // show error messages
    
    if ($_SESSION['displayMessage']) {
        foreach($_SESSION['displayMessage'] as $displayMessage) {
            echo $displayMessage;
            echo "<br>";
        }
    }
    
    // end of web page
    
    echo "</span></body></html>";