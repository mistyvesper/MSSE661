<?php

/**
 * Description
 * 
 * Displays shared documents page.  
 * 
 * @author misty
 */

    require_once 'header.php';
    
    // get collections
    
    $collection->getPublicCollections();
    $collection->getPendingPublicCollectionDocs();
    
    // display web page

    echo "<span class='documents' id='spnAddToCollection'>
            <h1>Add To Collection</h1>";
    
    // check for collections
    
    if ($collection->getPublicCollectionCount() == 0) {
        $_SESSION['displayMessage'] = InfoMessage::publicCollectionNoneAvailable();
    } else {
        
        // check for display messages
        
        if (is_array($_SESSION['displayMessage'])) {
            foreach ($_SESSION['displayMessage'] as $displayMessage) {
                echo $displayMessage;
                echo "<br>";
            }
            echo "<br>";
        } else if (isset($_SESSION['displayMessage']) && $_SESSION['displayMessage'] != InfoMessage::dbNoRecords()) {
            echo $_SESSION['displayMessage'];
            echo "<br><br>";
        }
        
        // display form
        
        echo "<form class='documents' id='frmAddToCollection' method='post' action='addToCollection.php' enctype='multipart/form-data'>
                <table class='documents' id='tblAddToCollection'>
                    <tr class='documents' id='trAddToCollection'>
                        <td class='form-label' id='tdAddToCollectionSelectCollection'>Select Collection:</td>
                        <td class='form-text-medium-left' id='tdAddToCollectionTitle'>
                            <select class='form-text-medium-left' id='selAddToCollectionTitle' name='publicCollectionTitle'>";

        foreach ($_SESSION['publicCollection'] as $publicCollection) {
            $title = $publicCollection['title'];
            echo "<option class='form-text-medium-left' id='optAddToCollectionTitle'>$title</option>";
        }

        echo "</select></td></tr></table></div><br>";

        // show Collection

        $collection->showPendingPublicCollectionDocs();

        echo "<table class='documents' id='tblAddToCollectionAddDeleteButtons'>
                <tr class='documents' id='tblAddToCollectionAddDeleteButtons'>";

        if (count($_SESSION['pendingPublicCollectionDocs']) > 0) {
            echo "<td class='form-submit-small-center-gray' id='tdAddToCollectionDelete'>
                    <input class='form-submit-small-center-gray' id='subAddToCollectionDelete' type='submit' name='deletePendingPublicCollectionDocs' value='Delete'>
                </td>";
        }

        echo "<td></td>
                <td class='form-submit-small-center-gray' id='subAddToCollectionAdd'>
                    <input class='form-submit-small-center-gray' id='subAddToCollectionAdd' type='submit' name='addPendingPublicCollectionDocs' value='Add'>
                </td>
            </tr>
        </table>
        <br><br>
        <table class='documents' id='tblAddToCollectionCancelAdd'>
            <tr class='documents' id='trAddToCollectionCancelAdd'>
                <td class='form-submit-button' id='tdAddToCollectionCancel'>
                    <input class='form-submit-button' id='subAddToCollectionCancel' type='submit' name='cancelAddtoPublicCollection' value='Cancel'>
                </td>
                <td></td>
                <td class='form-submit-button-large' id='tdAddToCollectionAddConfirm'>
                    <input class='form-submit-button-large' id='subAddToCollectionAddConfirm' type='submit' name='addToCollection' value='Add to Collection'>
                </td>
            </tr>
        </table>
    </form>
</span>";
    }

// display messages

    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
    }

// end of web page
    
echo "</body>
    </html>";