<?php

/**
 * Description
 * 
 * Provides functions for authenticating web app users.
 * 
 * @author misty
 */

$directory = dirname(dirname(__FILE__));
require_once($directory . '/header.php');

// function to validate user

function validateUser($userName, $password, $db) {
    
    // sanitize input
        
    $userName = sanitizeString($userName);
    $password = sanitizeString($password);
    
    // check for preliminary errors
    
    if ($userName == "" && $password == "") {
        $_SESSION['displayMessage'] = InfoMessage::missingUserAndPW();
        return false;
    } else if ($userName == "") {
        $_SESSION['displayMessage'] = InfoMessage::missingUser();
        return false;
    } else if ($password == "") {
        $_SESSION['displayMessage'] = InfoMessage::missingPW();
        return false;
    }

    // declare and initialize variables
    
    $salt1 = '1234';
    $salt2 = '4321';
    if ($password != '') {
        $token = hash("ripemd128", "$salt1$password$salt2");
    }
    
    // open database connection

    $db->getDBConnection();

    // check for errors

    if (!$db->con->connect_error ) {
    
        // validate user password

        $result = mysqli_fetch_array(mysqli_query($db->con, "CALL usp_validateUser('$userName', '$token');"), MYSQLI_NUM)[0];

        // check if user validated

        if ($result) {
            $db->closeDBConnection();
            $_SESSION['user'] = $userName;
            header('Location: index.php');
            return true;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::loginUnsuccessful();
        }
    } else {
        $db->closeDBConnection();
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        return false;
    }
}