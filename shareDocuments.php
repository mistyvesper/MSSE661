<?php

/**
 * Description
 * 
 * Displays shared documents page.  
 * 
 * @author misty
 */

    require_once 'header.php';
    
    // get friends
    
    $users->getFriends();
    
    // display web page

    echo "<span class='documents' id='spnShareDocuments'>
            <h1 class='documents' id='hdrShareDocuments'>Send Message</h1>";
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
    }
    
    // display web form
    
    echo "<form class='documents' id='frmShareDocuments' method='post' action='shareDocuments.php' enctype='multipart/form-data'>
            <h4 class='documents' id='hdrShareDocumentsWith'>Share With</h4>
                <select class='form-text-medium-center' id='selShareDocumentsWith' name='shareWithUser[]' style='width:150px'>";
    
    if (isset($_SESSION['sendTo'])) {
        $sendTo = $_SESSION['sendTo'];
        foreach ($_SESSION['friends'] AS $friend) {
            $friendUserName = $friend['userName'];            
            if ($friendUserName == $sendTo) {
                echo "<option class='form-text-medium-center' id='optShareDocumentsWith' value='$friendUserName' selected='selected'>$sendTo</option>";
            } else {
                echo "<option class='form-text-medium-center' id='optShareDocumentsWith' value='$friendUserName'>$friendUserName</option>";
            } 
        }
    } else {
        foreach ($_SESSION['friends'] AS $friend) {
            $friendUserName = $friend['userName'];  
            if ($friendUserName == $sendTo) {
                echo "<option class='form-text-medium-center' id='optShareDocumentsWith' value='$friendUserName' selected='selected'>$sendTo</option>";
            } else {
                echo "<option class='form-text-medium-center' id='optShareDocumentsWith' value='$friendUserName'>$friendUserName</option>";
            } 
        }
    }
    
    echo "</select>";

    echo "<h4 class='documents' id='hdrPendingShareDocumentsSubject'>Subject</h4>
            <input class='form-text-extralarge-left' id='inPendingShareDocumentsSubject' type='text' name='subject'>
        <h4 class='documents' id='hdrPendingShareDocumentsMessage'>Message</h4>
            <textarea class='form-text-extralarge-tall-left' id='inPendingShareDocumentsMessage' name='body'></textarea>
        <h4 class='documents' id='hdrPendingShareDocumentsAttach'>Attachments</h4>
        <table class='documents compact' id='tblPendingShareDocuments'>
            <thead class='documents' id='theadPendingShareDocumentsHeaders'>
                <tr class='documents' id='trPendingShareDocumentsHeaders'";
    
    // show Collection

    $collection->showPendingSharedCollection();
    
    echo "</tbody></table></div>";
    echo "<table class='documents' id='tblPendingShareDocumentsAddDeleteButtons'>
            <tr class='documents' id='trPendingShareDocumentsAddDeleteButtons'>";
    
    if (count($_SESSION['pendingSharedCollection']) > 0) {
        echo "<td class='documents' id='tdDeletePendingShareDocuments'>
                <input class='form-submit-small-center-gray' id='subDeletePendingShareDocuments' type='submit' name='deletePendingSharedDocs' value='Delete'>
            </td>";
    }
    
    echo "<td></td>
            <td class='documents' id='tdAddPendingShareDocuments'>
                <input class='form-submit-small-center-gray' id='subAddPendingShareDocuments' type='submit' name='addPendingSharedDocs' value='Add'>
            </td>
        </tr>
    </table>
    <br><br>
    <table class='documents' id='tblPendingShareDocumentsCancelShareButtons'>
        <tr class='documents' id='trPendingShareDocumentsCancelShareButtons'>
            <td class='documents' id='tdPendingShareDocumentsCancel'>
                <input class='form-submit-button' id='subPendingShareDocumentsCancel' type='submit' name='cancelShare' value='Cancel'>
            </td>
            <td></td>
            <td class='documents' id='tdPendingShareDocumentsSend'>
                <input class='form-submit-button' id='subPendingShareDocumentsSend' type='submit' name='send' value='Send'>
            </td>
        </tr>
    </table>
 </form>
 </body>
 </html>";    