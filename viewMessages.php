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

    echo "<h1>View Messages</h1>";
    
    // show Messages

    $messages->showAllMessages();
    
    // end of web page
    
    echo "</body>
        </html>";

