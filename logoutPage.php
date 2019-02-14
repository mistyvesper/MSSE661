<?php

/**
 * Description
 * 
 * Displays successful account registration page.  
 * 
 * @author misty
 */

require_once 'header.php';

// destroy session

unset($_SESSION['user']);
$loggedIn = FALSE;
$appUser = '';
session_destroy();

// display message

echo "<html>
        <head>
            <meta charset='UTF-8'>
            <title>Logged Out</title>
        </head>
        <body>
            <h1>Logged Out</h1>
            <p>You have been successfully logged out. To log back in, please visit the <a href='loginPage.php'>Login Page</a>.</p>
        <body>
    </html>";