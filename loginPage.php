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
                <title>Nano-docs Login</title>
                <style>
                    @import url('/Stylesheets/main.css');
                </style>
                <style>
                    #spnLoginForm {
                        position: absolute;
                        top: 25%;
                        left: 22%;
                        padding: 80px 200px 50px 175px;
                    }
                    #trLoginHeader {
                        text-align: center;
                    }
                    #trLoginSubmit {
                        text-align: right;
                    }
                    #trRegisterForAccount {
                        text-align: center;
                        vertical-align: bottom;
                        height: 50px;
                    }
                </style>
            </head>
            <body class='initial' id='bdyLoginPage'>
                <span class='initial' id='spnLoginForm'>
                    <form class='initial' id='frmLoginForm' method='post' action='loginPage.php' enctype='multipart/form-data'>
                        <table class='initial' id='tblLoginForm'>
                            <tr class='initial' id='trLoginHeader'>
                                <td class='initial' id='tdLoginHeader' colspan=2>
                                    <h1 class='initial' id='hdrLoginPage'>Nano-docs Login</h1>
                                </td>
                            </tr>
                            <tr class='initial' id='trLoginUser'>
                                <td class='form-label' id='tdLoginUserLabel'>User Name:</td>
                                <td class='form-input' id='tdLoginUserInput'><input class='form-input' id='inLoginUserName' type='text' name='userName' maxlength='25'></td>
                            </tr>
                            <tr class='initial' id='trLoginPassword'>
                                <td class='form-label' id='tdLoginPassLabel'>Password:</td>
                                <td class='form-input' id='tdLoginPassInput'><input class='form-input' id='inLoginUserPass' type='password' name='password' maxlength='25'></td>
                            </tr>
                            <tr class='initial' id='trLoginSubmit'>
                                <td class='initial' id='tdLoginSubmit' colspan=2>
                                    <input class='form-submit-button' id='subLogin' type='submit' name='login' value='Login'>
                                </td>
                            </tr>
                            <tr class='initial' id='trRegisterForAccount'>
                                <td class='initial' id='tdRegisterForAccount' colspan=2>
                                    <a class='link' id='lnkRegisterForAccount' href='registerForAccount.php'>Don't have an account? Register here.</a>
                                </td>
                            </tr>
                        </table>
                    </form>";
                        
    // check for errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo "<br><br>";
        echo $_SESSION['displayMessage'];
    }

    echo "</span></body></html>";