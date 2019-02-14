<?php

/**
 * Description
 * 
 * Displays login page.  
 * 
 * @author misty
 */
        
    require_once 'header.php';

    // declare and initialize variables

    $userName = $_POST['userName'];
    $password = $_POST['password'];
    $submitted = $_POST['submitted'];
    $database = new Database($dbHost, $dbUser, $dbPassword, $dbDBName);
    $dbConnection = $database->getDBConnection();
    
    // check if valid username and password provided and redirect as needed
    
    if ($userName != '' && $password != '' && $submitted == 1) {
        validateUser($userName, $password, $dbConnection);
        if (isset($_SESSION['user'])) {
            header('Location: index.php');     
        } 
    } 
    
    // close database connection
    
    $database->closeDBConnection();

    // display web form
    
    echo "<html>
            <head>
                <meta charset='UTF-8'>
                <title></title>
            </head>
            <body>
                <h1>Login Page</h1>
                    <div><form method='post' action='loginPage.php' enctype='multipart/form-data'>
                        <table>
                            <tr>
                                <td>User Name:</td>
                                <td><input type='text' name='userName' maxlength='25'></td>
                            </tr>
                            <tr>
                                <td>Password:</td>
                                <td><input type='password' name='password' maxlength='25'></td>
                            </tr>
                        </table>
                        <input type='hidden' name='submitted' value='1'>
                        <br><input type='submit' value='Login'>
                    </form></div>
                    <div><a href='registerForAccount.php'>Don't have an account? Register here.</a></div>
            </body>
        </html>";
    
    // check for errors
    
    if ($submitted == 1 && $userName != '' && $password != '' && !isset($_SESSION['user'])) {
        Message::loginUnsuccessful();
    } else if ($submitted == 1 && $userName == '' && $password == '') {
        Message::missingUserAndPW();
    } else if ($submitted == 1 && $userName == '') {
        Message::missingUser();
    } else if ($submitted == 1 && $password == '') {
        Message::missingPW();
    }