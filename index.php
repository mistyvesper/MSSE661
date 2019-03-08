<?php

/**
 * Description
 * 
 * Displays main collection page.  
 * 
 * @author misty
 */

    require_once 'header.php';
    
    // display web page

    echo "<span class='documents' id='spnMyDocuments'>
            <h1 class='documents' id='hdrMyDocuments'>My Documents</h1>
                <table class='documents' id='tblMyDocumentsOptions'>
                    <td class='documents' id='tdSearch'>
                        <form class='documents' id='frmSearch' method='post' action='index.php' enctype='multipart/form-data'>
                            <input class='documents' id='inSearchValue' type='text' name='searchValue'>
                            <input class='form-submit-small-center-gray' id='subSearch' type='submit' name='search' value='Search'>
                        </form>
                    </td>
                    <td class='documents' id='tdClearSearch'>
                        <form class='documents' id='frmClearSearch' method='post' action='index.php' enctype='multipart/form-data'>
                            <input class='form-submit-medium-center-gray' id='subClearSearch' type='submit' name='clearSearch' value='Clear Search'>
                        </form>
                    </td>
                    <td class='documents' id='tdUploadDoc'>
                        <form class='documents' id='frmUploadDoc' method='post' action='uploadDocument.php' enctype='multipart/form-data'>
                            <input class='form-submit-large-center-gray' id='subUploadDoc' type='submit' name='upload' value='Upload Documents'>
                        </form>
                    </td>
                </table> 
            <br>";
    
    // show messages
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
        echo "<br><br>";
    } else if (isset($_SESSION['postedSearchValue'])) {
        echo "<span class='searchValues' id='spnSearchValues'>Search Value(s): ";
        $i = 1; 
        foreach ($_SESSION['postedSearchValue'] as $searchValue) {
            if (count($_SESSION['postedSearchValue']) > 1 && $i != count($_SESSION['postedSearchValue'])) {
                echo $searchValue . ", ";
            } else {
                echo $searchValue;
            }
            
            $i++;
        }
        echo "</span><br><br>";
    }
    
    // show Collection

    $collection->showCollection();
    
    // end of web page
    
    echo "</span></body></html>";