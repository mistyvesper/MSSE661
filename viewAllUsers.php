<?php

/**
 * Description
 * 
 * Displays users page.  
 * 
 * @author misty
 */

    require_once 'header.php'; 
    
    // get users
    
    $users->getUsers();
    
    // display web page

    $users->showUserNavigationBar();
    echo "<h1 class='documents' id='hdrViewAllUsers'>View All Users</h1>";
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
        echo "<br><br>";
    }
    
    // show users

    $users->showAllUsers();
    
    // end of web page
    
    echo "</body>
        </html>";

