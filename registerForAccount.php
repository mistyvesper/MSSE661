<?php

/**
 * Description
 * 
 * Displays account registration page.  
 * 
 * @author misty
 */

    require_once 'header.php';

    // declare and initialize variables

    $email = $_POST['email'];
    $user = $_POST['user'];
    $password = $_POST['password'];
    $submitted = $_POST['submitted'];
    $database = new Database($dbHost, $dbUser, $dbPassword, $dbDBName);
    $dbConnection = $database->getDBConnection();
    
    // construct email message
    // https://css-tricks.com/sending-nice-html-email-with-php/
    
    $emailTo = $email;
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
    
    // check for existing account, create account as needed, and login if account creation successful
    
    if ($submitted == 1 && $email != '' && $user != '' && $password != '' && !checkForExistingAccount($user, $dbConnection) && !checkForExistingEmail($email, $dbConnection)) {
        if (createAccount($email, $user, $password, $dbConnection)) {
            mail($emailTo, $emailSubject, $emailMessage, $emailHeaders);
            createUserDirectory($user, $uploadDirectory);
            header('Location: accountCreated.php');
        } else {
            $accountCreateFailed = 1;
        }
    }

    // display web form
    
    echo "<html>
            <head>
                <meta charset='UTF-8'>
                <title>Account Registration</title>
            </head>
            <body>
                <h1>Account Registration</h1>
                    <div><form method='post' action='registerForAccount.php' enctype='multipart/form-data'>
                        <table>
                            <tr>
                                <td>Email Address:</td>
                                <td><input type='text' name='email' maxlength='50'></td>
                            <tr>
                            <tr>
                                <td>User Name:</td>
                                <td><input type='text' name='user' maxlength='25'></td>
                            </tr>
                            <tr>
                                <td>Password:</td>
                                <td><input type='password' name='password' maxlength='25'></td>
                            </tr>
                        </table>
                        <input type='hidden' name='submitted' value='1'>
                        <br><input type='submit' value='Submit'>
                    </form></div>
            </body>
        </html>";
    
    // check for existing account
    
    if ($submitted == 1 && ($email == '' || $user == '' || $password == '')) {
        Message::invalidEntries();
    } else if ($submitted == 1 && strpos($email, '@') === false) {
        Message::invalidEmail();
    } else if ($accountCreateFailed == 1) {
        Message::accountCreationUnsucessful($user);
    } else if ($submitted == 1 && $email != '' && $user != '' && $password != '' && checkForExistingEmail($email, $dbConnection)) {
        Message::emailTaken($email);
    } else if ($submitted == 1 && $email != '' && $user != '' && $password != '' && checkForExistingAccount($user, $dbConnection)) {
        Message::accountTaken($user);
    }  
    
    // close database connection
    
    $database->closeDBConnection();