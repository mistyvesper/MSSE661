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
    
    $messages->getSentMessages();
    
    // display web page

    $messages->showMessageNavigationBar();
    echo "<h1>Sent Messages</h1>";
    
    // show Messages

    $messages->showAllSentMessages();
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
    }
    
    // end of web page
    
    echo "</body>
        </html>";

