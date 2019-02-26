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
    
    $messages->getSentMessages($appUser);
    
    // display web page

    $messages->showMessageNavigationBar();
    echo "<h1 class='documents' id='hdrSentMessages'>Sent Messages</h1>";
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
        echo "<br><br>";
    }
    
    // show Messages

    $messages->showAllSentMessages();
    
    // end of web page
    
    echo "</body>
        </html>";

