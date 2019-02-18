<?php

/**
 * Description
 * 
 * Displays messages page.  
 * 
 * @author misty
 */

    require_once 'header.php'; 
    
    // get messages
    
    $messages->getMessages();
    
    // display web page

    echo "<h1>View All Messages</h1>";
    
    // show Messages

    $messages->showAllMessages();
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
    }
    
    // end of web page
    
    echo "</body>
        </html>";

