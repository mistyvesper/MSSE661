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
    
    // open database connection
        
    $con = $dbConnection->getDBConnection();

    // check for errors

    if (!$con->connect_error) {
    
        // get user from database

        $dbUser = mysqli_fetch_array(mysqli_query($con, "SELECT userName FROM user WHERE userName = '$user'"), MYSQLI_NUM)[0];

        // check if user exists

        if ($user != '' && $dbUser != '') {
            return true;
        } else {
            return false;
        }
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
    
    // check for errors

    if (!$dbConnection->connect_error) {

        // prepare update statement

        $statement = $dbConnection->prepare("CALL usp_addUser(?, ?, ?, ?, ?);");
        $statement->bind_param('sss', $tempUser, $tempEmail, $tempPass);

        // get properties

        $tempUser = $user;
        $tempEmail = $userEmail;
        $tempPass = $token;

        $statement->execute();

        // check for errors

        if ($statement->error != '') {
            return false;
        } else {
            return true;
        } 
    }  
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

// function to update profile

function updateAccount($oldUserName, $newUserName, $newEmail, $newFirstName, $newLastName, $db) {
 
    // open database connection
        
    $con = $db->getDBConnection();

    // check for errors

    if (!$con->connect_error) {

        // prepare update statement

        $statement = $con->prepare("CALL usp_updateUser(?, ?, ?, ?, ?);");
        $statement->bind_param('sssss', $oldUser, $newUser, $email, $firstName, $lastName);

        // get properties

        $oldUser = $oldUserName;
        $newUser = $newUserName;
        $email = $newEmail;
        $firstName = $newFirstName;
        $lastName = $newLastName;

        $statement->execute();

        // check for errors

        if ($statement->error != '') {
            return false;
        } else {
            return true;
        } 
    }  

    // close database connection

    $db->closeDBConnection();
}

// function to update password

function updatePassword($userName, $newPassword, $db) {
 
    // open database connection
        
    $con = $db->getDBConnection();

    // check for errors

    if (!$con->connect_error) {

        // prepare update statement

        $statement = $con->prepare("CALL usp_updatePassword(?, ?);");
        $statement->bind_param('ss', $tempUser, $tempPass);

        // get properties

        $tempUser = $userName;
        $tempPass = $newPassword;

        $statement->execute();

        // check for errors

        if ($statement->error != '') {
            return false;
        } else {
            return true;
        } 
    }  

    // close database connection

    $db->closeDBConnection();
}