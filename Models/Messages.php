<?php

/**
 * Description of Messages
 *
 * Provides CRUD methods for messages shared between users.
 * 
 * @author misty
 */

class Messages {
    
    private $messages = []; // array of messages
    private $message;
    private $sentMessages = [];
    private $messagesUser;
    private $db;
    private $con;
    private $seed; // for reseeding AUTO_INCREMENT
    
    // constructor requires user and database connection to instantiate
    
    public function __construct($user, $database) {
        $this->messagesUser = $user;
        $this->db = $database;
    }    
    
    // function to get messages array

    public function getReceivedMessages() { 
            
        // reset messages array

        $this->messages = [];
        $messageUser = $this->messagesUser;

        // open database connection

        $this->con = $this->db->getDBConnection();

        // check for errors

        if (!$this->con->connect_error ) {

            // get messages

            $query = "CALL usp_getReceivedMessagesByUser('$this->messagesUser');";
            $result = mysqli_query($this->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->messages[] = $row;
            }
            $_SESSION['messages'] = $this->messages;
            return $this->messages;
        } else {
            return false;
        }

        // close result and database connection

        $result->close();
        $this->db->closeDBConnection();
    }
    
    // function to get sent messages array

    public function getSentMessages() { 
            
        // reset messages array

        $this->sentMessages = [];
        $messageUser = $this->messagesUser;

        // open database connection

        $this->con = $this->db->getDBConnection();

        // check for errors

        if (!$this->con->connect_error ) {

            // get messages

            $query = "CALL usp_getSentMessagesByUser('$this->messagesUser');";
            $result = mysqli_query($this->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->sentMessages[] = $row;
            }
            $_SESSION['sentMessages'] = $this->sentMessages;
            return $this->sentMessages;
        } else {
            return false;
        }

        // close result and database connection

        $result->close();
        $this->db->closeDBConnection();
    }
    
    // function to get message by id
    
    public function getReceivedMessageByID($msgID) {
        
        // open database connection

        $this->con = $this->db->getDBConnection();

        // check for errors

        if (!$con->connect_error ) {

            // get message

            $query = "CALL usp_getMessageByID('$msgID');";
            $result = mysqli_query($this->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $subject = $row['messageSubject'];
                $body = $row['messageBody'];
                $sharedDate = $row['sharedDate'];
                $sharedBy = $row['sharedBy'];
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        }

        // close result and database connection

        $result->close();
        $this->db->closeDBConnection();
        
        // return message
        
        $this->message = new Message($subject, $body, $this->messagesUser, $sharedBy, $sharedDate, $this->db);
        return $this->message;
    }
    
    // function to get message by id
    
    public function getSentMessageByID($msgID) {
        
        // open database connection

        $this->con = $this->db->getDBConnection();

        // check for errors

        if (!$con->connect_error ) {

            // get message

            $query = "CALL usp_getMessageByID('$msgID');";
            $result = mysqli_query($this->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $subject = $row['messageSubject'];
                $body = $row['messageBody'];
                $sharedDate = $row['sharedDate'];
                $sharedBy = $row['sharedBy'];
            }
        } else {
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
        }

        // close result and database connection

        $result->close();
        $this->db->closeDBConnection();
        
        // return message
        
        $this->message = new Message($subject, $body, $sharedBy, $this->messagesUser, $sharedDate, $this->db);
        return $this->message;
    }
    
    // function to mark message as read
    
    public function readMessage($msgID) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_readMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // read message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                return false;
            } 
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;   
    }
    
    // function to delete received message
       
    public function deleteReceivedMessage($msgID) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_deleteReceivedMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // delete message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                return false;
            } 
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;   
    }
    
    // function to delete sent message
       
    public function deleteSentMessage($msgID) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_deleteSentMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // delete message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                return false;
            } 
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;   
    }
    
    // function to show navigation bar
    
    public function showMessageNavigationBar() {
        
        if (__FILE__ == 'viewAllReceivedMessages.php') {
            echo "<div class='topnav'>
                    <a class='active' href='viewAllReceivedMessages.php'>Received Messages</a>
                    <a href='viewAllSentMessages.php'>Sent Messages</a>
                  </div>";
        } else {
            echo "<div class='topnav'>
                    <a href='viewAllReceivedMessages.php'>Received Messages</a>
                    <a class='active' href='viewAllSentMessages.php'>Sent Messages</a>
                  </div>";
        }
        
    }
    
    // function to show all received messages
    
    public function showAllReceivedMessages() {
        
        // check for existing messages

        if (count($this->messages) == 0 || !isset($_SESSION['messages'])) {
            
            // create new error message
  
            $_SESSION['displayMessage'] = infoMessage::messagesNoReceived();
            
        } else {
        
            // iterate through messages array to display Messages

            echo "<form  method='post' action='viewAllReceivedMessages.php' enctype='multipart/form-data'><div><table><tr>"
                    . "<th align='center' style='width:75px'><input type='submit' name='select' value='Select' style='width:75px'></th>"
                    . "<th align='center' style='width:150px'><input type='submit' name='submit' value='Read/Unread' style='width:150px'></th>"
                    . "<th align='left' style='width:350px'><input type='submit' name='sortSubject' value='Subject' style='width:350px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='sortFrom' value='From' style='width:75px'></th>"
                    . "<th align='center' style='width:150px'><input type='submit' name='sortSharedDate' value='Shared Date' style='width:150px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sortAttachments' value='Attachments' style='width:100px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='viewReceivedMessage' value='View' style='width:75px'></th></tr>";
            
            foreach ($this->messages as $key => $message) {
                
                $messageID = $message['messageID'];
                $readFlag = $message['readFlag'];
                $subject = $message['messageSubject'];
                $from = $message['sharedBy'];
                $sharedDate = $message['sharedDate'];
                $attachments = $message['attachmentCount'];
                $url = "viewReceivedMessage.php?messageID=". $messageID;

                echo "<tr><td align='center' style='width:75px'><input type='checkbox' name='messages[]' value='$messageID'></td>"
                        . "<td align='center' style='width:150px'>$readFlag</td>"
                        . "<td style='width:350px'>$subject</td>"
                        . "<td align='center' style='width:75px'>$from</td>"
                        . "<td align='center' style='width:150px'>$sharedDate</td>"
                        . "<td align='center' style='width:100px'>$attachments</td>"
                        . "<td align='center' style='width:75px'><input type='submit' name='viewReceivedMessage[$messageID]' value='View' style='width:75px'></td></tr>";
            }

            echo "</table></div>";
            echo "<br><div><input type='submit' name='deleteReceivedMessages' value='Delete'></div></form>";
        }
    }
    
    // function to show all sent messages
    
    public function showAllSentMessages() {
        
        // check for existing messages

        if (count($this->sentMessages) == 0 || !isset($_SESSION['sentMessages'])) {
            
            // create new error message
  
            $_SESSION['displayMessage'] = infoMessage::messagesNoSent();
            
        } else {
        
            // iterate through messages array to display Messages

            echo "<form  method='post' action='viewAllReceivedMessages.php' enctype='multipart/form-data'><div><table><tr>"
                    . "<th align='center' style='width:75px'><input type='submit' name='select' value='Select' style='width:75px'></th>"
                    . "<th align='center' style='width:150px'><input type='submit' name='submit' value='Read/Unread' style='width:150px'></th>"
                    . "<th align='left' style='width:350px'><input type='submit' name='sortSubject' value='Subject' style='width:350px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='sortFrom' value='To' style='width:75px'></th>"
                    . "<th align='center' style='width:150px'><input type='submit' name='sortSharedDate' value='Shared Date' style='width:150px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sortAttachments' value='Attachments' style='width:100px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='viewSentMessage' value='View' style='width:75px'></th></tr>";
            
            foreach ($this->sentMessages as $key => $message) {
                
                $messageID = $message['messageID'];
                $readFlag = $message['readFlag'];
                $subject = $message['messageSubject'];
                $from = $message['sharedBy'];
                $sharedDate = $message['sharedDate'];
                $attachments = $message['attachmentCount'];
                $url = "viewSentMessage.php?messageID=". $messageID;

                echo "<tr><td align='center' style='width:75px'><input type='checkbox' name='messages[]' value='$messageID'></td>"
                        . "<td align='center' style='width:150px'>$readFlag</td>"
                        . "<td style='width:350px'>$subject</td>"
                        . "<td align='center' style='width:75px'>$from</td>"
                        . "<td align='center' style='width:150px'>$sharedDate</td>"
                        . "<td align='center' style='width:100px'>$attachments</td>"
                        . "<td align='center' style='width:75px'><input type='submit' name='viewSentMessage[$messageID]' value='View' style='width:75px'></td></tr>";
            }

            echo "</table></div>";
            echo "<br><div><input type='submit' name='deleteSentMessages' value='Delete'></div></form>";
        }
    }
}
