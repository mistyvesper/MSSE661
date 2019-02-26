<?php

/**
 * Description
 * 
 * Displays friends page.  
 * 
 * @author misty
 */

    require_once 'header.php'; 
    
    // get friends
    
    $users->getFriends();
    
    // display web page

    $users->showUserNavigationBar();
    echo "<h1 class='documents' id='hdrViewAllFriends'>View All Friends</h1>";
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
        echo "<br><br>";
    }
    
    // show Friends

    $users->showAllFriends();
    
    // end of web page
    
    echo "</body>
        </html>";

