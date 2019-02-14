<?php

/**
 * Description
 * 
 * Provides functionality common to all web app pages.
 * 
 * @author misty
 */

    // start session

    session_start();
    
    // get root directory 
    
    $rootDirectory = dirname(__FILE__);
    
    // get dependencies
    
    require_once($rootDirectory . '/App/login.php');
    require_once($rootDirectory . '/Models/Collection.php');
    require_once($rootDirectory . '/Models/Database.php');
    require_once($rootDirectory . '/Models/Document.php');
    require_once($rootDirectory . '/Models/Message.php');
    require_once($rootDirectory . '/Resources/accountMaintenance.php');
    require_once($rootDirectory . '/Resources/authentication.php');
    require_once($rootDirectory . '/Resources/fileMaintenance.php');
    require_once($rootDirectory . '/Resources/sanitize.php');

    // check if user is logged in
    
    if (isset($_SESSION['user'])) {
        $appUser = $_SESSION['user'];
        $loggedIn = TRUE;
        $userDirectory = $rootDirectory . '/uploadedFiles/' . $appUser . '/';
    } else {
        $loggedIn = FALSE;
    }