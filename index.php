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
        echo "<div>Search Value(s): ";
        $i = 1; 
        foreach ($_SESSION['searchValue'] as $searchValue) {
            if (count($_SESSION['searchValue']) > 1 && $i != count($_SESSION['searchValue'])) {
                echo $searchValue . ", ";
            } else {
                echo $searchValue;
            }
            
            $i++;
        }
        echo "</div><br><br>";
    }
    
    // show Collection

    $collection->showCollection();
    
    // end of web page
    
    echo "</body>
        </html>";