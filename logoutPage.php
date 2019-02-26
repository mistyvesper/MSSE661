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
$appUser = '';
session_destroy();

// display message

echo "<html>
        <head>
            <meta charset='UTF-8'>
            <title>Logged Out</title>
            <style>
                @import url('/Stylesheets/main.css');
            </style>
        </head>
        <body class='initial' id='bdyLoggedOut'>
            <span class='initial' id='spnLoggedOut'>
                <h1 class='initial' id='hdrLoggedOut'>Logged Out</h1>
                <p class='initial' id='pLoggedOut'>You have been successfully logged out. 
                    To log back in, please visit the <a class='link' id='lnkBackToLoginFromLogout' href='loginPage.php'>Login Page</a>.
                </p>
            </span>
        </body>
    </html>";