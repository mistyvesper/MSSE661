<?php

/**
 * Description
 * 
 * Displays shared documents page.  
 * 
 * @author misty
 */

    require_once 'header.php';    
    
    // display web page

    echo "<h1>Share Documents</h1>";
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
    }
    
    echo "<form  method='post' action='shareDocuments.php' enctype='multipart/form-data'>"
                    . "<div><h4>Share With</h4>"
                    . "<input type='text' name='shareWithUser'>"
                    . "<h4>Subject</h4>"
                    . "<input type='text' name='subject' style='width:745px'>"
                    . "<h4>Message</h4>"
                    . "<input type='text' name='body' style='width:745px;height:75px'></div>"
                    . "<div><h4>Attachments</h4>"
                    . "<table>";
    
    // show Collection

    $collection->showPendingSharedCollection();
    
    echo "</table></div>";
    echo "<div><table><tr>";
    
    if (count($_SESSION['pendingSharedCollection']) > 0) {
        echo "<td style='width:75px'><input type='submit' name='deletePendingSharedDocs' value='Delete' style='width:75px'></td>";
    }
    
    echo "<td></td><td style='width:75px'><input type='submit' name='addPendingSharedDocs' value='Add' style='width:75px'></td></tr></table></div>"
        . "<br><br><div><table><tr>"
        . "<td style='width:75px'><input type='submit' name='cancelShare' value='Cancel' style='width:75px'></td>"
        . "<td></td><td style='width:75px'><input type='submit' name='send' value='Send' style='width:75px'></td></tr></table></div></form>";    

// end of web page
    
    echo "</body>
        </html>";