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

    $database->getDBConnection();

    // check for errors

    if (!$database->con->connect_error ) {

        // get user info

        $query = "CALL usp_getUserInfo('$appUser');";
        $result = mysqli_query($database->con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $userEmail = $row['userEmail'];
            $userFirstName = $row['userFirstName'];
            $userLastName = $row['userLastName'];
        }
    } else {
        $_SESSION['newDisplayMessage'] = InfoMessage::dbConnectError();
    }

    // close result and database connection

    $result->close();
    $database->closeDBConnection();
    
    // show navigation bar
    
    echo "<span class='documents' id='spnChangePassword'>
            <div class='topnav'>
                <a class='link' id='lnkManageProfile' href='manageProfile.php'>Manage Profile</a>
                <a class='link-active' id='lnkChangePassword' href='changePassword.php'>Change Password</a>
              </div>";
    
    // display form
        
    echo "<h1 class='documents' id='hdrChangePassword'>Change Password</h1>";
    
    // display error messages
    
    if (isset($_SESSION['newDisplayMessage'])) {
        echo $_SESSION['newDisplayMessage'];
        echo "<br><br>";
    }
    
    echo "<form class='documents' id='frmChangePassword' method='post' action='changePassword.php' enctype='multipart/form-data'>
            <table class='documents' id='tblChangePassword'>
                <tr class='documents' id='trChangePasswordOld'>
                    <td class='form-label-large-left' id='tdChangePasswordOldLabel'>Enter Old Password:</td>
                    <td class='form-text-large-left' id='tdChangePasswordOldInput'>
                        <input class='form-text-large-left' id='txtChangePasswordOldInput' type='password' name='oldPassword' maxlength='25'>
                    </td>
                </tr>
                <tr class='documents' id='trChangePasswordNew1'>
                    <td class='form-label-large-left' id='tdChangePasswordNew1Label'>Enter New Password:</td>
                    <td class='form-text-large-left' id='tdChangePasswordNew1Input'>
                        <input class='form-text-large-left' id='txtChangePasswordNew1Input' type='password' name='newPassword1' maxlength='25'>
                    </td>
                </tr>
                <tr class='documents' id='trChangePasswordNew2'>
                    <td class='form-label-large-left' id='tdChangePasswordNew2Label'>Enter New Password Again:</td>
                    <td class='form-text-large-left' id='tdChangePasswordNew2Input'>
                        <input class='form-text-large-left' id='txtChangePasswordNew2Input' type='password' name='newPassword2' maxlength='25'>
                    </td>
                </tr>
            </table>
        <br>
        <input class='form-submit-button' id='subChangePasswordUpdate' type='submit' name='updatePassword' value='Update'>
    </form>";
    
    echo "</span></body></html>";

