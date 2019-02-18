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
    private $readMessages = []; // array of read messages
    private $unreadMessages = []; // array of unread messages
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

    public function getMessages() { 
            
        // reset messages array

        $this->messages = [];
        $messageUser = $this->messagesUser;

        // open database connection

        $this->con = $this->db->getDBConnection();

        // check for errors

        if (!$this->con->connect_error ) {

            // get messages

            $query = "CALL usp_getMessagesByUser('$this->messagesUser');";
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
    
    // function to get message by id
    
    public function getMessageByID($msgID) {
        
        // open database connection

        $this->con = $this->db->getDBConnection();

        // check for errors

        if (!$con->connect_error ) {

            // get user info

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
        
            // add document
        
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
    
    // function to delete message
    
    // function to mark message as read
    
    public function deleteMessage($msgID) {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_deleteMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // add document
        
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
    
    // function to show all messages
    
    public function showAllMessages() {
        
        // check for existing documents

        if (count($this->messages) == 0 || !isset($_SESSION['messages'])) {
            
            // create new error message
  
            $_SESSION['displayMessage'] = infoMessage::messagesNo();
            
        } else {
        
            // iterate through messages array to display Messages

            echo "<form  method='post' action='viewAllMessages.php' enctype='multipart/form-data'><div id='nojavascript'><table><tr>"
                    . "<th align='center' style='width:75px'><input type='submit' name='select' value='Select' style='width:75px'></th>"
                    . "<th align='center' style='width:150px'><input type='submit' name='submit' value='Read/Unread' style='width:150px'></th>"
                    . "<th align='left' style='width:350px'><input type='submit' name='sortSubject' value='Subject' style='width:350px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='sortFrom' value='From' style='width:75px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sortSharedDate' value='Shared Date' style='width:100px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sortAttachments' value='Attachments' style='width:100px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='viewMessage' value='View' style='width:75px'></th></tr>";
            
            foreach ($this->messages as $key => $message) {
                
                $messageID = $message['messageID'];
                $readFlag = $message['readFlag'];
                $subject = $message['messageSubject'];
                $from = $message['sharedBy'];
                $sharedDate = $message['sharedDate'];
                $attachments = $message['attachmentCount'];
                $url = "viewMessage.php?messageID=". $messageID;

                echo "<tr><td align='center'><input type='checkbox' name='messages[]' value='$messageID'></td>"
                        . "<td align='center'>$readFlag</td>"
                        . "<td>$subject</td>"
                        . "<td align='center'>$from</td>"
                        . "<td align='center'>$sharedDate</td>"
                        . "<td align='center'>$attachments</td>"
                        . "<td align='center'><input type='submit' name='viewMessage[$messageID]' value='View'></td></tr>";
            }

            echo "</table></div>";
            echo "<br><div><input type='submit' name='deleteMessages' value='Delete'></div></form>";
        }
    }
}
