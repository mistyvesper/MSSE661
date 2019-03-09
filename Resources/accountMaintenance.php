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
    
    // sanitize input
    
    $user = sanitizeString($user);
    
    // open database connection
        
    $con = $dbConnection->getDBConnection();

    // check for errors

    if (!$con->connect_error) {
    
        // get user from database

        $dbUser = mysqli_fetch_array(mysqli_query($con, "SELECT userName FROM user WHERE userName = '$user' AND active = TRUE"), MYSQLI_NUM)[0];

        // check if user exists

        if ($user != '' && $dbUser != '') {
            $dbConnection->closeDBConnection();
            return true;
        } else {
            $dbConnection->closeDBConnection();
            return false;
        }
    } else {
        $dbConnection->closeDBConnection();
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        return true;
    }
}

// function to check for existing email

function checkForExistingEmail($userEmail, $dbConnection) {
    
    // sanitize input
    
    $userEmail = sanitizeString($userEmail);
    
    // open database connection
        
    $con = $dbConnection->getDBConnection();

    // check for errors

    if (!$con->connect_error) {
    
        // get user email from database

        $dbUserEmail = mysqli_fetch_array(mysqli_query($con, "SELECT userEmail FROM user WHERE userEmail = '$userEmail' AND active = TRUE"), MYSQLI_NUM)[0];

        // check if user exists

        if ($userEmail != '' && $dbUserEmail != '') {
            $dbConnection->closeDBConnection();
            return true;
        } else {
            $dbConnection->closeDBConnection();
            return false;
        }
    } else {
        $dbConnection->closeDBConnection();
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        return true;
    }
    
}

// function to create account

function createAccount($userEmail, $user, $password, $dbConnection) {
    
    // sanitize input
    
    $userEmail = sanitizeString($userEmail);
    $user = sanitizeString($user);
    $password = sanitizeString($password);
    
    // declare and initialize variables
    
    $salt1 = '1234';
    $salt2 = '4321';
    if ($password != '') {
        $token = hash("ripemd128", "$salt1$password$salt2");
    }
    
    // open database connection
        
    $con = $dbConnection->getDBConnection();
    
    // check for errors

    if (!$con->connect_error) {

        // prepare update statement

        $statement = $con->prepare("CALL usp_addUser(?, ?, ?);");
        $statement->bind_param('sss', $tempUser, $tempEmail, $tempPass);

        // get properties

        $tempUser = $user;
        $tempEmail = $userEmail;
        $tempPass = $token;

        $statement->execute();

        // check for errors

        if ($statement->error != '') {
            $dbConnection->closeDBConnection();
            return false;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::accountCreationUnsuccessful($tempUser);
            $dbConnection->closeDBConnection();
            return true;
        } 
    } else {
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        $dbConnection->closeDBConnection();
        return false;
    }  
}

// function to create user directory

function createUserDirectory($user, $uploadDirectory) {
    
    // construct user directory path
    
    if (substr($uploadDirectory, strlen($uploadDirectory)-1, 1) == '/') {
        $userDirectory = $uploadDirectory . $user . '/';
    } else {
        $userDirectory = $uploadDirectory . '/' . $user . '/';
    }
    
    // create directory
    
    if (file_exists($userDirectory)) {
        return true;
    } else if (file_exists($uploadDirectory)) {
        return createDirectory($userDirectory);
    } else if (createDirectory($uploadDirectory)) {
        return createDirectory($userDirectory);
    } else {
        return false;
    }
}

// function to update profile

function updateAccount($oldUserName, $newUserName, $newEmail, $newFirstName, $newLastName, $db) {
    
    // sanitize input
    
    $oldUserName = sanitizeString($oldUserName);
    $newUserName = sanitizeString($newUserName);
    $newEmail = sanitizeString($newEmail);
    $newFirstName = sanitizeString($newFirstName);
    $newLastName = sanitizeString($newLastName);
 
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
            $_SESSION['displayMessage'] = InfoMessage::profileUpdated();
            $db->closeDBConnection();
            return false;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::profileUpdateFailed();
            $db->closeDBConnection();
            return true;
        } 
    } else {
        $db->closeDBConnection();
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        return false;
    }
}

// function to update password

function updatePassword($userName, $newPassword, $db) {
    
    // sanitize input
    
    $userName = sanitizeString($userName);
    $newPassword = sanitizeString($newPassword);
    
    // declare and initialize variables
    
    $salt1 = '1234';
    $salt2 = '4321';
    if ($newPassword != '') {
        $token = hash("ripemd128", "$salt1$newPassword$salt2");
    }
 
    // open database connection
        
    $con = $db->getDBConnection();

    // check for errors

    if (!$con->connect_error) {

        // prepare update statement

        $statement = $con->prepare("CALL usp_updatePassword(?, ?);");
        $statement->bind_param('ss', $tempUser, $tempPass);

        // get properties

        $tempUser = $userName;
        $tempPass = $token;

        $statement->execute();

        // check for errors

        if ($statement->error == '') {
            $_SESSION['displayMessage'] = InfoMessage::passwordUpdated();
            $db->closeDBConnection();
            return true;
        } else {
            $db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::passwordUpdateFailed();
            return false;
        } 
    } else {
        $db->closeDBConnection();
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        return false;
    } 
}

// function to send account creation email

function sendAccountCreationEmail($sendTo) {
    
    // construct email message
    // https://css-tricks.com/sending-nice-html-email-with-php/

    $emailSubject = "Account Created";
    $emailBody = "Hello world.";
    $emailHeaders = "From: mistyvesper@gmail.com\r\n";
    $emailHeaders .= "Reply-To: mistyvesper@gmail.com \r\n";
//    $emailHeaders .= "MIME-Version: 1.0\r\n";
//    $emailHeaders .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
//    $emailBody = "<html><body>";
//    $emailBody .= "<h1>Your Account Has Been Successfully Created</h1>";
//    $emailBody .= "<p>Thank you for creating an account.";
//    $emailBody .= "<br><br>&nbsp;Username: $user";
//    $emailBody .= "<br><br>Please log in to your new account <a href='http://localhost/login.php'>here</a>.";
//    $emailBody .= "</body></html>";
    
    mail($sendTo, $emailSubject, $emailMessage, $emailHeaders);
}

// function to validate account registration information

function validateAccountRegistration($email, $userName, $password, $db) {
    
    // sanitize input
    
    $email = sanitizeString($email);
    $userName = sanitizeString($userName);
    $password = sanitizeString($password);
    
    // check for errors
    
    if ($email != '' && $userName != '' && $password != '' && !checkForExistingAccount($userName, $db) && !checkForExistingEmail($email, $db)) {
        return true;
    } else if (($email == '' || $userName == '' || $password == '')) {
        $_SESSION['displayMessage'] = InfoMessage::invalidEntries();
        return false;
    } else if (strpos($email, '@') === false) {
        $_SESSION['displayMessage'] = InfoMessage::invalidEmail();
        return false;
    } else if ($email != '' && $userName != '' && $password != '' && checkForExistingEmail($email, $db)) {
        $_SESSION['displayMessage'] = InfoMessage::emailTaken($email);
        return false;
    } else if ($submitted == 1 && $email != '' && $userName != '' && $password != '' && checkForExistingAccount($userName, $db)) {
        $_SESSION['displayMessage'] = InfoMessage::accountTaken($user);
        return false;
    }
}

// function to delete account

function deleteAccount($user, $db) {
    
    // open database connection
        
    $con = $db->getDBConnection();

    // check for errors

    if (!$con->connect_error) {

        // prepare update statement

        $statement = $con->prepare("CALL usp_deleteAccount(?);");
        $statement->bind_param('s', $tempUser);

        // get properties

        $tempUser = $user;

        $statement->execute();

        // check for errors

        if ($statement->error != '') {
            $_SESSION['displayMessage'] = InfoMessage::accountDeleted();
            $db->closeDBConnection();
            return true;
        } else {
            $db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::accountNotDeleted();
            return false;
        } 
    } else {
        $db->closeDBConnection();
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        return false;
    } 
}
