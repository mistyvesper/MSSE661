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
                <style>
                    #spnAccountRegistration {
                        position: absolute;
                        top: 25%;
                        left: 17%;
                        padding: 110px 0px 70px 225px;
                        width: 575px;
                    }
                    span.errorMessage {
                        position: absolute;
                        top: 75%;
                        left: 30%;
                    }
                    .hidden {
                        display: none;
                        width: 20px;
                        height: 20px;
                    }
                    img.form-results {
                        display: inline-block;
                        width: 20px;
                        height: 20px;
                    }
                    .form-label {
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .black-text {
                        color: black;
                        width: 200px;
                        text-align: left;
                        font-style: italic;
                        font-size: 11pt;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .red-text {
                        font-size: 11pt;
                        color: red;
                        width: 200px;
                        text-align: left;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .green-text {
                        font-size: 11pt;
                        color: green;
                        width: 200px;
                        text-align: left;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    #trAccountRegisterHeader {
                        text-align: center;
                    }
                    #trAccountRegisterSubmit {
                        text-align: right;
                        padding: 35px 0px;
                    }
                    #trGoBackToLogin {
                        text-align: center;
                        height: 80px;
                    }
                </style>
                <script src='http://code.jquery.com/jquery-latest.min.js'></script>
                <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'
                    integrity='sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30='crossorigin='anonymous'>
                </script>
                <script src='/JavaScript/accountRegistrationValidation.js'></script>
            </head>
            <body class='initial' id='bdyAccountRegistration'>
                <span class='initial' id='spnAccountRegistration'>
                    <form class='initial' id='frmAccountRegistration' method='post' action='registerForAccount.php' enctype='multipart/form-data'>
                        <table class='initial' id='tblAccountRegistration'>
                            <tr class='initial' id='trAccountRegisterHeader'>
                                <td class=initial' id='tdAccountRegisterHeader' colspan=2>
                                    <h1 class='initial' id='hdrAccountRegistration'>Account Registration</h1>
                                </td>
                            </tr>
                            <tr class='initial' id='trAccountRegisterEmail'>
                                <td class='form-label' id='tdAccountRegisterEmailLabel'>Email Address:</td>
                                <td class='form-input' id='tdAccounRegisterEmailInput'>
                                    <input class='form-input' id='inAccountRegisterEmail' type='text' name='email' maxlength='50'>
                                </td>
                                <td class='form-results' id='tdAccountRegisterEmailResults'>
                                    <img class='hidden' id='imgAccountRegisterEmailResults' src='/QuizTaker/Media/checkmark.png' />
                                </td>
                                <td class='form-results' id='tdAccountRegisterEmailTextResults'></td>
                            <tr>
                            <tr class='initial' id='trAccountRegisterUser'>
                                <td class='form-label' id='tdAccountRegisterUserLabel'>User Name:</td>
                                <td class='form-input' id='tdAccountRegisterUserInput'>
                                    <input class='form-input' id='inAccountRegisterUser' type='text' name='userName' maxlength='25'>
                                </td>
                                <td class='form-results' id='inAccountRegisterUserResults'>
                                    <img class='hidden' id='imgAccountRegisterUserResults' src='/QuizTaker/Media/checkmark.png' />
                                </td>
                                <td class='form-results' id='tdAccountRegisterUserTextResults'></td>
                            </tr>
                            <tr class='initial' id='trAccountRegisterPassword'>
                                <td class='form-label' id='tdAccountRegisterPass'>Password:</td>
                                <td class='form-input' id='tdAccountRegisterPass'>
                                    <input class='form-input' id='inAccountRegisterPass' type='password' name='password' maxlength='25'>
                                </td>
                                <td class='form-results' id='inAccountRegisterPassResults'>
                                    <img class='hidden' id='imgAccountRegisterPassResults' src='/QuizTaker/Media/checkmark.png' />
                                </td>
                                <td class='form-results' id='tdAccountRegisterPassTextResults'></td>
                            </tr>
                            <tr class='initial' id='trAccountRegisterSubmit'>
                                <td class='initial' id='tdAccountRegisterSubmit' colspan=2>
                                    <input class='form-submit-button' id='subRegisterForAccount' type='submit' name='registerForAccount' value='Submit'>
                                </td>
                            </tr>
                            <tr class='initial' id='trGoBackToLogin'>
                                <td class='initial' id='tdGoBackToLogin' colspan=2>
                                    <a class='link' id='lnkBackToLogin' href='loginPage.php'>Back to Login Page</a>
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