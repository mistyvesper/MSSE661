<?php

/**
 * Description
 * 
 * Displays main collection page.  
 * 
 * @author misty
 */
    
    require_once 'header.php';
    
    // check if user logged in
    
    if (!$loggedIn) {
        header('Location: loginPage.php');
    }

    // create database connection

    $database = new Database($dbHost, $dbUser, $dbPassword, $dbDBName);

    // create Collection

    $collection = new Collection($appUser, $database);
    
    // capitalize appUser
    
    $upperAppUser = strtoupper($appUser);
    
    // display web page

    echo "<html>
            <head>
                <meta charset='UTF-8'>
                <title>Nano-site</title>
            </head>
            <body>
                <div>
                    <table style='width:100%'>
                        <tr>
                            <td style='width:3%'><img src='Media/person_icon.png' style='width:35px;height:35px;'></td>
                            <td style='width:18%'>Signed in as: $upperAppUser</td>
                            <td style='width:80%' align='right'><a href='logoutPage.php'>Logout</a></td>
                        </tr>
                    </table>
                </div>
                <h1>Documents</h1>
                <div>
                    <table>
                        <td style='align:left'><input type='text'><button>Search</button></td>
                        <td style='align:right'><a href='uploadDocument.php'>Upload Document</a></td>
                    </table>
                </div>
                <br>";
    
    // show Collection

    $collection->showCollection();
    
    // end of web page
    
    echo "</body>
        </html>";