<?php

/**
 * Description of Message
 *
 * Provides CRUD methods for messages shared between users.
 * 
 * @author misty
 */

class Message {
    
    // encapsulate Message properties by declaring private

    private $message = [];
    private $db; // database
    private $con; // Database connection
    
    // constructor to initialize Message properties
    
    public function __construct($msgSubject, $msgBody, $msgTo, $msgFrom, $msgSharedDate, $database) {

        $this->message['subject'] = $msgSubject;
        $this->message['body'] = $msgBody;
        $this->message['to'] = $msgTo;
        $this->message['from'] = $msgFrom;
        $this->message['sharedDate'] = $msgSharedDate;
        $this->db = $database;
    }
    
    // function to get subject
    
    public function getMessageSubject() {
        return $message['subject'];
    }
    
    // function to set subject
    
    public function setMessageSubject($msgSubject) {
        $message['subject'] = $msgSubject;
    }
    
    // function to get body
    
    public function getMessageBody() {
        return $message['body'];
    }
    
    // function to set body 
    
    public function setMessageBody($msgBody) {
        $message['body'] = $msgBody;
    }
    
    // function to get to
    
    public function getMessageTo() {
        return $message['to'];
    }
    
    // function to set to
    
    public function setMessageTo($msgTo) {
        $message['to'] = $msgTo;
    }
    
    // function to get from
    
    public function getMessageFrom() {
        return $message['from'];
    }
    
    // function to set from
    
    public function setMessageFrom($msgFrom) {
        $message['from'] = $msgFrom;
    }
    
    // function to get shared date
    
    public function getMessageSharedDate() {
        return $message['sharedDate'];
    }
    
    // function to set shared date
    
    public function setMessageSharedDate($msgDate) {
        $message['sharedDate'] = $msgDate;
    }
    
    // public to send message
    
    public function sendMessage() {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->con->prepare("CALL usp_addMessage(?, ?, ?, ?, ?);");
            $statement->bind_param('sssss', $msgSubject, $msgBody, $msgSharedDate, $msgFrom, $msgTo);
            
            // get message properties
        
            $msgSubject = $this->message['subject'];
            $msgBody = $this->message['body'];
            $msgSharedDate = $this->message['sharedDate'];
            $msgFrom = $this->message['from'];
            $msgTo = $this->message['to'];
        
            // add message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
    }
    
    // function to get message id
    
    public function getMessageID() {
        
        // open database connection
        
        $this->con = $this->db->getDBConnection();
        
        // get message details
        
        $subject = $this->message['subject'];
        $body = $this->message['body'];
        $to = $this->message['to'];
        $from = $this->message['from'];
        $sharedDate = $this->message['sharedDate'];
        
        // check for errors
        
        if (!$this->con->connect_error ) {
            
            // get message ID
        
            $query = "CALL usp_getMessageID('$subject', '$body', '$sharedDate', '$from', '$to');";
            $result = mysqli_query($this->con, $query);
            $messageID = mysqli_fetch_array($result, MYSQLI_NUM)[0];
            
            if ($messageID) {
                return $messageID;
            } else {
                return false;
            }
        } else {
            return false;
        }
        
        // close result and close database connection
        
        $result->close();
        $this->db->closeDBConnection();
    }
    
    // function to get message
    
    public function getMessage() {
        return $this->message;
    }
    
    // function to set message 
    
    public function setMessage($msgSubject, $msgBody, $msgTo, $msgFrom, $msgSharedDate) {
        $this->message['subject'] = $msgSubject;
        $this->message['body'] = $msgBody;
        $this->message['to'] = $msgTo;
        $this->message['from'] = $msgFrom;
        $this->message['sharedDate'] = $msgSharedate;
    }
    
    // function to show message
    
    public function showMessage() {
        
        $sharedBy = $this->message['from'];
        $subject = $this->message['subject'];
        $body = $this->message['body'];
        
        echo "<div><h4>From</h4>"
            . "$sharedBy"
            . "<h4>Subject</h4>"
            . "$subject"
            . "<h4>Message</h4>"
            . "$body"
            . "<h4>Attachments</h4></div>";
    }
}
