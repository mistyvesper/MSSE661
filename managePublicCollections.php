<?php

/**
 * Description
 * 
 * Displays messages page.  
 * 
 * @author misty
 */
require_once 'header.php';

// display web page

echo "<span class='documents' id='spnManagePublicCollections'>
            <h1 class='documents' id='hdrManagePublicCollections'>View/Manage Public Collections</h1>";

// check for errors

if (isset($_SESSION['displayMessage'])) {
    echo $_SESSION['displayMessage'];
    echo "<br>";
}

// get public collections

$collection->getPublicCollections();

// show add form

echo "<h3 class='documents' id='hdrAddPublicCollection'>Add Public Collection</h3>
            <form class='documents' id='frmAddPublicCollection' method='post' action='managePublicCollections.php' enctype='multipart/form-data'>
                <table class='documents' id='tblAddPublicCollection'>
                    <tr 'class='documents' id='trAddPublicCollectionTitle'>
                        <td class='form-label-medium-left' id='tdAddPublicCollectionTitleLabel'>Title</td>
                        <td class='form-text-extraextralarge-left' id='tdAddPublicCollectionTitleInput'>
                            <input class='form-text-extraextralarge-left' id='txtAddPublicCollectionTitleInput' type='text' name='publicCollectionTitle' maxLength='50'>
                        </td>
                    <tr class='documents' id='trAddPublicCollectionDescription'>
                        <td class='form-label-medium-left' id='tdAddPublicCollectionDescriptionLabel'>Description</td>
                        <td class='form-text-extraextralarge-tall-left' id='tdAddPublicCollectionDescriptionInput'>
                            <textarea class='form-text-extraextralarge-tall-left' id='txtAddPublicCollectionInput' name='publicCollectionDescription' maxLength='4000'></textarea>
                        </td>
                    </tr>
                    <tr class='documents' id='trAddPublicCollectionsSubmit'>
                        <td class='td-rightalign' id='tdAddPublicCollectionsSubmit' colspan='2'>
                            <input class='form-submit-button' id='subAddPublicCollection' type='submit' name='addPublicCollection' value='Add'>
                        </td>
                    </tr>
                </table>
            </form>
        <br>
        <h3 class='documents' id='hdrManageExistingCollections'>View/Manage Existing Public Collections</h3>";

// show public collections

$collection->showPublicCollections();

// end of web page

echo "</span></body></html>";

