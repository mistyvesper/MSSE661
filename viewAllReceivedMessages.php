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
    
    $messages->getReceivedMessages();
    
    // display web page

    $messages->showMessageNavigationBar();
    echo "<h1>Received Messages</h1>";
    
    // show Messages

    $messages->showAllReceivedMessages();
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
    }
    
    // end of web page
    
    echo "</body>
        </html>";

