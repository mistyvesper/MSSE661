<?php

/**
 * Description of Users
 *
 * Provides CRUD methods for users.
 * 
 * @author misty
 */

require_once 'Message.php';
require_once '/var/www/html/Resources/databaseMaintenance.php';

class Users {
    
    // encapsulate Users properties by declaring private
    
    private $users = []; // array of users
    private $friends = []; // array of friends
    private $user;
    private $friend;
    private $db;
    private $seed; // for reseeding AUTO_INCREMENT
    
    // constructor requires user and database connection to instantiate
    
    public function __construct($user, $database) {
        $this->user = $user;
        $this->db = $database;
    }
    
    // function to get users array
    
    public function getUsers() {
        
        // reset users array

        $this->users = [];

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get messages

            $query = "CALL usp_getUsers('$this->user');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->users[] = $row;
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }

        // close result and database connection

        if ($result && isset($this->users)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return users
        
        if (isset($this->users)) {
            $_SESSION['users'] = $this->users;
            return $this->users;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return false;
        }
    }
    
    // function to get user by id
    
    public function getUserByID($uid) {

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$his->db->con->connect_error ) {

            // get user

            $query = "CALL usp_getUserByID('$uid');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $user = $row;
            }
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }

        // close result and database connection

        if ($result && isset($user)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return user
        
        if (isset($user)) {
            return $user;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return false;
        }
    }
    
    // function to get friend by user id
    
    public function getFriendStatus($appUser, $friend) {

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get user

            $query = "CALL usp_getFriendStatus('$appUser', '$friend');";
            $result = mysqli_query($this->db->con, $query);
            $active = mysqli_fetch_array($result, MYSQLI_NUM)[0];
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }

        // close result and database connection

        if($result && isset($active)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return user
        
        if (isset($active)) {
            return $active;
        } else {
            return false;
        }
    }
    
    // function to get friends array

    public function getFriends() { 
            
        // reset friends array

        $this->friends = [];

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get messages

            $query = "CALL usp_getFriends('$this->user');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->friends[] = $row;
            }
        } else {
            $result->close();
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }

        // close result and database connection

        if ($result && isset($this->friends)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return friends
        
        if (isset($this->friends)) {
            $_SESSION['friends'] = $this->friends;
            return $this->friends;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return false;
        }
    }
    
    // function to accept friend request
    
    public function acceptFriendRequest($msgID) {
        
        // get message
        
        $messages = new Messages($this->user, $this->db);
        $message = $messages->getReceivedMessageByID($msgID)->getMessage();
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error && isset($message)) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_addFriend(?, ?);");
            $statement->bind_param('ss', $appUser, $userFriend);
            
            // get friend properties
        
            $appUser = $this->user;
            $userFriend = $message['from'];
        
            // add friend
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
                return false;
            } 
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::friendsRequestAccepted();
        $this->db->closeDBConnection();
        return true;   
    }
    
    // function to ignore friend request
    
    public function ignoreFriendRequest($msgID) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_deleteReceivedMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // delete message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
                return false;
            } 
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::friendsRequestIgnored();
        $this->db->closeDBConnection();
        return true; 
    }
    
    // function to send friend request
    
    public function sendFriendRequest($from, $to, $sharedDate) {
        
        // set message properties
        
        $subject = '**New Friend Request**';
        $body = "$from wants to be your friend. Do you accept?";
        $message = new Message($subject, $body, $to, $from, $sharedDate, $this->db);
        
        // send message
        
        if ($message->sendMessage()) {
            $_SESSION['displayMessage'] = InfoMessage::messageSent($to);
            return true;
        } else {
            $_SESSION['displayMessage'] = InfoMessage::messageNotSent();
            return false;
        }  
    }
    
    // function to delete friend
    
    public function deleteFriend($friend) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_removeFriend(?, ?);");
            $statement->bind_param('ss', $user, $userFriend);
            
            // get friend properties
        
            $user = $this->user;
            $userFriend = $friend;
        
            // delete message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                $this->db->closeDBConnection();
                return false;
            } 
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            $this->db->closeDBConnection();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::friendDeleted($friend);
        $this->db->closeDBConnection();
        return true;         
    }
    
    // function to show navigation bar
    
    public function showUserNavigationBar() {
        
        if (__FILE__ == 'viewAllFriends.php') {
            echo "<span class='documents' id='spnFriendsAndUsers'>
                    <div class='topnav' id='divFriendsAndUsers>
                        <a class='link-active' id='lnkViewAllFriends' href='viewAllFriends.php'>View All Friends</a>
                        <a class='link' id='lnkViewAllUsers' href='viewAllUsers.php'>View All Users</a>
                    </div>";
        } else {
            echo "<span class='documents' id='spnFriendsAndUsers'>
                    <div class='topnav' id='divFriendsAndUsers'>
                        <a class='link' id='lnkViewAllFriends' href='viewAllFriends.php'>View All Friends</a>
                        <a class='link-active' id='lnkViewAllUsers' href='viewAllUsers.php'>View All Users</a>
                    </div>";
        }   
    }
    
    // function to show all friends
    
    public function showAllFriends() {
        
        // check for existing friends

        if (count($this->friends) == 0 || !isset($_SESSION['friends'])) {
            
            // create new error message
  
            echo infoMessage::friendsNo();
            
        } else {
        
            // iterate through messages array to display Friends

            echo "<form class='documents' id='frmViewAllFriends' method='post' action='viewAllFriends.php' enctype='multipart/form-data'>
                    <table class='documents' id='tblViewAllFriends'>
                        <tr class='documents' id='trViewAllFriendsHeaders'>
                            <th class='form-submit-small-header-center' id='thViewAllFriendsSelectHeader'>
                                <input class='form-submit-small-header-center' id='subViewAllFriendsSelectHeader' type='submit' name='select' value='Select' disabled>
                            </th>
                            <th class='form-submit-medium-header-center' id='thViewAllFriendsUserNameHeader'>
                                <input class='form-submit-small-header-center' id='subViewAllFriendsUserNameHeader' type='submit' name='sortUserName' value='User Name'>
                            </th>
                            <th class='form-submit-medium-header-center' id='thViewAllFriendsFirstNameHeader'>
                                <input class='form-submit-medium-header-center' id='subViewAllFriendsFirstNameHeader' type='submit' name='sortFirstName' value='First Name'>
                            </th>
                            <th class='form-submit-small-medium-center' id='thViewAllFriendsLastNameHeader'>
                                <input class='form-submit-medium-header-center' id='subViewAllFriendsLastNameHeader' type='submit' name='sortLastName' value='Last Name'>
                            </th>
                            <th class='form-submit-large-header-left' id='thViewAllFriendsEmailHeader'>
                                <input class='form-submit-large-header-left' id='subViewAllFriendsEmailHeader' type='submit' name='sortEmail' value='Email'>
                            </th>
                            <th class='form-submit-medium-header-center' id='thViewAllFriendsSendMsgHeader'>
                                <input class='form-submit-medium-header-center' id='thViewAllFriendsSendMsgHeader' type='submit' name='sendMessage' value='Send Message' disabled>
                            </th>
                        </tr>";
            
            foreach ($this->friends as $key => $friend) {
                
                $userID = $friend['userID'];
                $userName = $friend['userName'];
                $email = $friend['userEmail'];
                $firstName = $friend['userFirstName'];
                $lastName = $friend['userLastName'];

                echo "<tr class='documents' id='trViewAllFriends'>
                        <td class='form-text-small-center' id='tdViewAllFriendsSelect'>
                            <input class='checkbox' id='chkViewAllFriendsSelect' type='checkbox' name='messages[]' value='$userID'>
                        </td>
                        <td class='form-text-medium-center' id='tdViewAllFriendsUserName'>$userName</td>
                        <td class='form-text-medium-center' id='tdViewAllFriendsFirstName'>$firstName</td>
                        <td class='form-text-medium-center' id='tdViewAllFriendsLastName'>$lastName</td>
                        <td class='form-text-large-left' id='tdViewAllFriendsEmail'>$email</td>
                        <td class='form-submit-medium-center-gray' id='tdViewAllFriendsSendMsg'>
                            <input class='form-submit-medium-center-gray' id='subViewAllFriendsSendMsg' type='submit' name='sendMessage[$userID]' value='Send Message'>
                        </td>
                    </tr>";
            }

            echo "</table>";
            echo "<br>
                    <input class='form-submit-button' id='subViewAllFriendsDelete' type='submit' name='deleteFriends[$userID]' value='Delete'>
                </form>";
        }
    }
    
    // function to show all users
    
    public function showAllUsers() {
        
        // check for existing users

        if (count($this->users) == 0 || !isset($_SESSION['users'])) {
            
            // create new error message
  
            echo infoMessage::usersNo();
            
        } else {
        
            // iterate through messages array to display Users

            echo "<form class='documents' id='frmViewAllUsers' method='post' action='viewAllUsers.php' enctype='multipart/form-data'>
                    <table class='documents' id='tblViewAllUsers'>
                        <tr class='documents' id='trViewAllUsersHeaders'>
                            <th class='form-submit-medium-header-center' id='thViewAllUsersUserNameHeader'>
                                <input class='form-submit-medium-header-center' id='subViewAllUsersUserNameHeader' type='submit' name='submit' value='User Name'>
                            </th>
                            <th class='form-submit-medium-header-center' id='thViewAllUsersFirstNameHeader'>
                                <input class='form-submit-medium-header-center' id='subViewAllUsersFirstNameHeader' type='submit' name='sortUserFirstName' value='First Name'>
                            </th>
                            <th class='form-submit-medium-header-center' id='thViewAllUsersLastNameHeader'>
                                <input class='form-submit-medium-header-center' id='subViewAllUsersLastNameHeader' type='submit' name='sortUserLastName' value='Last Name'>
                            </th>
                            <th class='form-submit-large-header-left' id='thViewAllUsersEmailHeader'>
                                <input class='form-submit-large-header-left' id='subViewAllUsersEmailHeader' type='submit' name='sortUserEmail' value='Email'>
                            </th>
                            <th class='form-submit-medium-header-center' id='thViewAllUsersSendFriendRequest'>
                                <input class='form-submit-medium-header-center' id='subViewAllUsersSendFriendRequest' type='submit' name='requestFriend' value='Send Friend Request'>
                            </th>
                        </tr>";
            
            foreach ($this->users as $key => $user) {
                
                $userID = $user['userID'];
                $userName = $user['userName'];
                $email = $user['userEmail'];
                $firstName = $user['userFirstName'];
                $lastName = $user['userLastName'];
                $friendStatus = $this->getFriendStatus($_SESSION['user'], $userName);

                echo "<tr class='documents' id='trViewAllUsers'>
                        <td class='form-text-medium-center' id='tdViewAllUsersUserName'>$userName</td>
                        <td class='form-text-medium-center' id='tdViewAllUsersFirstName'>$firstName</td>
                        <td class='form-text-medium-center' id='tdViewAllUsersLastName'>$lastName</td>
                        <td class='form-text-large-left' id='tdViewAllUsersEmail'>$email</td>";
                
                if ($friendStatus) {
                    echo "<td class='form-submit-medium-center-gray' id='tdViewAllUsersSendFriendRequest'>
                            <input class='form-submit-medium-center-gray' id='subViewAllUsersSendFriendRequest' type='submit' name='requestFriend[$userID]' value='Already Friends' disabled>
                        </td>
                    </tr>";
                } else {
                    echo "<td class='form-submit-medium-center-gray' id='tdViewAllUsersSendFriendRequest'>
                            <input class='form-submit-medium-center-gray' id='subViewAllUsersSendFriendRequest' type='submit' name='requestFriend[$userID]' value='Send Request'>
                        </td>
                    </tr>";
                }
            }

            echo "</table></div></form>";
        }
    }
}
