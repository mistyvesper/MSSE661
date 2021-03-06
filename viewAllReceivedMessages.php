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
    
    $messages->getReceivedMessages($appUser);
    
    // display web page

    $messages->showMessageNavigationBar();
    echo "<h1 class='documents' id='hdrReceivedMessages'>Received Messages</h1>";
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
        echo "<br><br>";
    }
    
    // show Messages

    $messages->showAllReceivedMessages();
    
    // end of web page
    
    echo "</span></body></html>";

