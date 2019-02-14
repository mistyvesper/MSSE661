<?php

/**
 * Description
 * 
 * Provides functions for web app account maintenance.
 * 
 * @author misty
 */

require_once 'fileMaintenance.php';

// function to check for existing account

function checkForExistingAccount($user, $dbConnection) {
    
    // get user from database
    
    $dbUser = mysqli_fetch_array(mysqli_query($dbConnection, "SELECT userName FROM user WHERE userName = '$user'"), MYSQLI_NUM)[0];
    
    // check if user exists
    
    if ($user != '' && $dbUser != '') {
        return true;
    } else {
        return false;
    }
}

// function to check for existing email

function checkForExistingEmail($userEmail, $dbConnection) {
    
    // get user email from database
    
    $dbUserEmail = mysqli_fetch_array(mysqli_query($dbConnection, "SELECT userEmail FROM user WHERE userEmail = '$userEmail'"), MYSQLI_NUM)[0];
    
    // check if user exists
    
    if ($userEmail != '' && $dbUserEmail != '') {
        return true;
    } else {
        return false;
    }
}

// function to create account

function createAccount($userEmail, $user, $password, $dbConnection) {
    
    // declare and initialize variables
    
    $salt1 = '1234';
    $salt2 = '4321';
    if ($password != '') {
        $token = hash("ripemd128", "$salt1$password$salt2");
    }
    
    // try inserting new user
    
    $result = mysqli_query($dbConnection, "INSERT INTO user (userEmail, userName, userPassword) VALUES ('$userEmail', '$user', '$token');");
    
    // return result
    
    return $result;
}

// function to create user directory

function createUserDirectory($user, $uploadDirectory) {
    
    if (file_exists($uploadDirectory)) {
        
        if (substr($uploadDirectory, len($uploadDirectory)-1, 1) == '/') {
            $userDirectory = $uploadDirectory . $user . '/';
        } else {
            $userDirectory = $uploadDirectory . '/' . $user . '/';
        }
        createDirectory($userDirectory);
    }
}