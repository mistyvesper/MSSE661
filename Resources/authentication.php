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

function validateUser($user, $password, $dbConnection) {
    
    // declare and initialize variables
    
    $salt1 = '1234';
    $salt2 = '4321';
    if ($password != '') {
        $token = hash("ripemd128", "$salt1$password$salt2");
    }
    
    // get user password from database
    
    $dbPassword = mysqli_fetch_array(mysqli_query($dbConnection, "SELECT userPassword FROM user WHERE userName = '$user'"), MYSQLI_NUM)[0];
    
    // check if password matches
    
    if ($user != '' && $password != '' && $dbPassword == $token) {
        $_SESSION['user'] = $user;
    }
}