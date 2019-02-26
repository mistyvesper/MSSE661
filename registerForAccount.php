<?php

/**
 * Description
 * 
 * Displays account registration page.  
 * 
 * @author misty
 */

    require_once 'header.php';
 
    // display web form
    
    echo "<html>
            <head>
                <meta charset='UTF-8'>
                <title>Account Registration</title>
                <style>
                    @import url('/Stylesheets/main.css');
                </style>
            </head>
            <body class='initial' id='bdyAccountRegistration'>
                <span class='initial' id='spnAccountRegistration'>
                    <h1 class='initial' id='hdrAccountRegistration'>Account Registration</h1>
                    <form class='initial' id='frmAccountRegistration' method='post' action='registerForAccount.php' enctype='multipart/form-data'>
                        <table class='initial' id='tblAccountRegistration'>
                            <tr class='initial' id='trAccountRegisterEmail'>
                                <td class='form-label' id='tdAccountRegisterEmailLabel'>Email Address:</td>
                                <td class='form-input' id='tdAccounRegisterEmailInput'><input class='form-input' id='inAccountRegisterEmail' type='text' name='email' maxlength='50'></td>
                            <tr>
                            <tr class='initial' id='trAccountRegisterUser'>
                                <td class='form-label' id='tdAccountRegisterUserLabel'>User Name:</td>
                                <td class='form-input' id='tdAccountRegisterUserInput'><input class='form-input' id='inAccounRegisterUser' type='text' name='userName' maxlength='25'></td>
                            </tr>
                            <tr class='initial' id='trAccountRegisterPassword'>
                                <td class='form-label' id='tdAccountRegisterPass'>Password:</td>
                                <td class='form-input' id='tdAccountRegisterPass'><input class='form-input' id='inAccountRegisterPass' type='password' name='password' maxlength='25'></td>
                            </tr>
                        </table>
                        <br>
                        <input class='form-submit-button' id='subRegisterForAccount' type='submit' name='registerForAccount' value='Submit'>
                        <br>
                        <br>
                        <a class='link' id='lnkBackToLogin' href='loginPage.php'>Back to Login Page</a>
                    </form>";
    
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo "<br><br>";
        echo $_SESSION['displayMessage'];
    }
    
    echo "</span></body></html>";