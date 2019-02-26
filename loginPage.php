<?php

/**
 * Description
 * 
 * Displays login page.  
 * 
 * @author misty
 */
        
    require_once 'header.php';

    // display web form
    
    echo "<html>
            <head>
                <meta charset='UTF-8'>
                <title>Nano-site Login</title>
                <style>
                    @import url('/Stylesheets/main.css');
                </style>
            </head>
            <body class='initial' id='bdyLoginPage'>
                <span class='initial' id='spnLoginForm'>
                    <h1 class='initial' id='hdrLoginPage'>Nano-site Login</h1>
                        <form class='initial' id='frmLoginForm' method='post' action='loginPage.php' enctype='multipart/form-data'>
                            <table class='initial' id='tblLoginForm'>
                                <tr class='initial' id='trLoginUser'>
                                    <td class='form-label' id='tdLoginUserLabel'>User Name:</td>
                                    <td class='form-input' id='tdLoginUserInput'><input class='form-input' id='inLoginUserName' type='text' name='userName' maxlength='25'></td>
                                </tr>
                                <tr class='initial' id='trLoginPassword'>
                                    <td class='form-label' id='tdLoginPassLabel'>Password:</td>
                                    <td class='form-input' id='tdLoginPassInput'><input class='form-input' id='inLoginUserPass' type='password' name='password' maxlength='25'></td>
                                </tr>
                            </table>
                            <br><input class='form-submit-button' id='subLogin' type='submit' name='login' value='Login'>
                        </form>
                        <a class='link' id='lnkRegisterForAccount' href='registerForAccount.php'>Don't have an account? Register here.</a>";
                        
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo "<br><br>";
        echo $_SESSION['displayMessage'];
    }

    echo "</span></body></html>";