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

    echo "<h1>Documents</h1>
            <div>

                <table>
                    <td><form  method='post' action='index.php' enctype='multipart/form-data'><input type='text' name='searchValue'><input type='submit' name='search' value='Search'></form></td>
                    <td><form  method='post' action='index.php' enctype='multipart/form-data'><input type='submit' name='clearSearch' value='Clear Search'></form></td>
                    <td><form  method='post' action='uploadDocument.php' enctype='multipart/form-data'><input type='submit' name='upload' value='Upload Documents'></form></td>
                </table> 
            </div>
            <br>";
    
    // show messages
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
        echo "<br><br>";
    } else if (isset($_SESSION['searchValue'])) {
        echo $_SESSION['searchValue'];
        echo "<br><br>";
    }
    
    // show Collection

    $collection->showCollection();
    
    // end of web page
    
    echo "</body>
        </html>";