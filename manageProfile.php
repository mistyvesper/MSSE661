<?php

/**
 * Description
 * 
 * Enables a user to update his/her user profile.  
 * 
 * @author misty
 */

    require_once 'header.php';    
    
    // open database connection

    $con = $database->getDBConnection();

    // check for errors

    if (!$con->connect_error ) {

        // get user info

        $query = "CALL usp_getUserInfo('$appUser');";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $userEmail = $row['userEmail'];
            $userFirstName = $row['userFirstName'];
            $userLastName = $row['userLastName'];
        }
    } else {
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
    }

    // close result and database connection

    $result->close();
    $database->closeDBConnection();
    
    echo "<form  method='post' action='manageProfile.php' enctype='multipart/form-data'>
            <div>
                <table>
                    <tr>
                        <td><input type='submit' name='manage' value='Manage Account'></td>
                        <td><input type='submit' name='changePassword' value='Change Password'></td>
                    </tr>
                </table>
            </div>
        </form>";
    
    // display web page
    
    if (isset($_POST['manage']) || (!isset($_POST['manage']) && !isset($_POST['changePassword']) && !isset($_POST['updatePassword'])) || isset($_POST['updateProfile'])) {
        
        echo "<h1>Manage Profile</h1>
            <form  method='post' action='manageProfile.php' enctype='multipart/form-data'>
                <div>
                    <table style='width:400px'>
                        <tr>
                            <td>User Name:</td>
                            <td><input type='text' name='profileUserName' value='$appUser' maxlength='25'></td>
                        </tr>
                        <tr>
                            <td>Email:</td>
                            <td><input type='text' name='profileEmail' value='$userEmail' maxlength='50'></td>
                        </tr>
                        <tr>
                            <td>First Name:</td>
                            <td><input type='text' name='profileFirstName' value='$userFirstName' maxlength='50'></td>
                        </tr>
                        <tr>
                            <td>Last Name:</td>
                            <td><input type='text' name='profileLastName' value='$userLastName' maxlength='50'></td>
                        </tr>
                    </table>
                </div>
                <br>
                <input type='submit' name='updateProfile' value='Update'>
            </form>";
    } else if (isset($_POST['changePassword']) || isset($_POST['updatePassword'])) {
        
        echo "<h1>Change Password</h1>
            <form  method='post' action='manageProfile.php' enctype='multipart/form-data'>
                <div>
                    <table style='width:400px'>
                        <tr>
                            <td>Enter Old Password:</td>
                            <td><input type='password' name='oldPassword' maxlength='25'></td>
                        </tr>
                        <tr>
                            <td>Enter New Password:</td>
                            <td><input type='password' name='newPassword1' maxlength='25'></td>
                        </tr>
                        <tr>
                            <td>Enter New Password Again:</td>
                            <td><input type='password' name='newPassword2' maxlength='25'></td>
                        </tr>
                    </table>
                </div>
                <br>
                <input type='submit' name='updatePassword' value='Update'>
            </form>";
    }
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
    }
    
    echo "</body>
        </html>";

