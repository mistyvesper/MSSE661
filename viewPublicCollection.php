<?php

/**
 * Description
 * 
 * Displays messages page.  
 * 
 * @author misty
 */

    require_once 'header.php'; 
    
    // check for collection id
    
    if (!isset($_SESSION['collectionID'])) {
        $_SESSION['displayMessage'] = InfoMessage::publicCollectionNotSelected();
    } else {
        
        // show header
        
        $collectTitle = $collection->getPublicCollectionTitle($_SESSION['collectionID']);
        echo "<span class='documents' id='spnViewPublicCollection'>
                <h1 class='documents' id='hdrViewPublicCollection'>$collectTitle</h1>";
        
        // check for errors
    
        if (isset($_SESSION['displayMessage'])) {
            echo $_SESSION['displayMessage'];
            echo "<br><br>";
        }
        
        // show public collections

        $collection->getPublicCollectionDocsByID($_SESSION['collectionID']);
        $collection->showPublicCollection();
        
        // show add and close buttons
        
        echo "<br><br>
                <form class='documents' id='frmViewPublicCollectionAddClose' method='post' action='viewPublicCollection.php' enctype='multipart/form-data'>
                    <table class='documents' id='tblViewPublicCollectionAddClose'>
                        <tr class='documents' id='trViewPublicCollectionAddClose'>
                            <td class='form-submit-button' id='tdViewPublicCollectionAdd'>
                                <input class='form-submit-button' id='subViewPublicCollectionAdd' type='submit' name='addPublicCollectionDocs' value='Add'>
                            </td>
                            <td class='form-submit-button' id='tdViewPublicCollectionClose'>
                                <input class='form-submit-button-large' id='subViewPublicCollectionClose' type='submit' name='closePublicCollectionDocs' value='Close Collection'>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>";
    }
    
    // end of web page
    
    echo "</span></body></html>";

