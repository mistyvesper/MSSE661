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
    
    // function to show all messages
    
    public function showAllMessages() {
        
        // check for existing documents

        if (count($this->messages) == 0) {
            
            // create new error message
  
            $_SESSION['displayMessage'] = infoMessage::dbConnectError();
            
        } else {
        
            // iterate through messages array to display Messages

            echo "<form  method='post' action='index.php' enctype='multipart/form-data'><div id='nojavascript'><table><tr>"
                    . "<th align='center' style='width:75px'><input type='submit' name='select' value='Select' style='width:75px'></th>"
                    . "<th align='left' style='width:350px'><input type='submit' name='sortSubject' value='Subject' style='width:350px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='sortFrom' value='From' style='width:75px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sortSharedDate' value='Shared Date' style='width:100px'></th>"
                    . "<th align='center' style='width:100px'><input type='submit' name='sortAttachments' value='Attachments' style='width:100px'></th>"
                    . "<th align='center' style='width:75px'><input type='submit' name='viewMessage' value='View' style='width:75px'></th></tr>";
            
            foreach ($this->messages as $key => $message) {
                
                $subject = $message['messageSubject'];
                $from = $message['sharedBy'];
                $sharedDate = $message['sharedDate'];
                $attachments = $message['attachmentCount'];

                echo "<tr><td align='center'><input type='checkbox' name='document[]' value='$key'></td>"
                        . "<td>$subject</td>"
                        . "<td align='center'>$from</td>"
                        . "<td align='center'>$sharedDate</td>"
                        . "<td align='center'>$attachments</td>"
                        . "<td align='center'><a href=''>View</a></td></tr>";
            }

            echo "</table></div>";
            echo "<br><div><input type='submit' name='delete' value='Delete'></div></form>";
        }
    }
}
