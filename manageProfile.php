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
        $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
    }

    // close result and database connection

    $result->close();
    $database->closeDBConnection();
    
    // show navigation bar
    
    echo "<span class='documents' id='spnManageProfile'>
            <div class='topnav' id='divManageProfile'>
                <a class='link-active' id='lnkManageProfile' href='manageProfile.php'>Manage Profile</a>
                <a class='link' id='lnkChangePassword' href='changePassword.php'>Change Password</a>
              </div>";
    
    // display form
        
    echo "<h1 class='documents' id='hdrManageProfile'>Manage Profile</h1>";
    
    // display errors
    
    if (isset($_SESSION['displayMessage'])) {
        echo $_SESSION['displayMessage'];
        echo "<br><br>";
    }
    
    echo "<form class='documents' id='frmMaangeProfile' method='post' action='manageProfile.php' enctype='multipart/form-data'>
            <table class='documents' id='tblManageProfile'>
                <tr class='documents' id='trManageProfileUser'>
                    <td class='form-label-large-left' id='tdManageProfileUserLabel'>User Name:</td>
                    <td class='form-text-large-left' id='tdManageProfileUserInput'>
                        <input class='form-text-large-left' id='txtManageProfileUserInput' type='text' name='profileUserName' value='$appUser' maxlength='25'>
                    </td>
                </tr>
                <tr class='documents' id='trManageProfileEmail'>
                    <td class='form-label-large-left' id='tdManageProfileEmailLabel'>Email:</td>
                    <td class='form-text-large-left' id='tdManageProfileEmailInput'>
                        <input class='form-text-large-left' id='txtManageProfileEmailInput' type='text' name='profileEmail' value='$userEmail' maxlength='50'>
                    </td>
                </tr>
                <tr class='documents' id='trManageProfileFirstName'>
                    <td class='form-label-large-left' id='tdManageProfileFirstNameLabel'>First Name:</td>
                    <td class='form-text-large-left' id='tdManageProfileFirstNameInput'>
                        <input class='form-text-large-left' id='txtManageProfileFirstNameInput' type='text' name='profileFirstName' value='$userFirstName' maxlength='50'>
                    </td>
                </tr>
                <tr class='documents' id='trManageProfileLastName'>
                    <td class='form-label-large-left' id='tdManageProfileLastNameInputLabel'>Last Name:</td>
                    <td class='form-text-large-left' id='tdManageProfileLastNameInput'>
                        <input class='form-text-large-left' id='txtManageProfileLastNameInput' type='text' name='profileLastName' value='$userLastName' maxlength='50'>
                    </td>
                </tr>
            </table>
            <br>
            <input class='form-submit-button' id='subManageProfileUpdate' type='submit' name='updateProfile' value='Update'>
        </form>";
    
    echo "</span></body></html>";

