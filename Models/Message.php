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
        return $this->message['subject'];
    }
    
    // function to set subject
    
    public function setMessageSubject($msgSubject) {
        $this->message['subject'] = $msgSubject;
    }
    
    // function to get body
    
    public function getMessageBody() {
        return $this->message['body'];
    }
    
    // function to set body 
    
    public function setMessageBody($msgBody) {
        $this->message['body'] = $msgBody;
    }
    
    // function to get to
    
    public function getMessageTo() {
        return $this->message['to'];
    }
    
    // function to set to
    
    public function setMessageTo($msgTo) {
        $this->message['to'] = $msgTo;
    }
    
    // function to get from
    
    public function getMessageFrom() {
        return $this->message['from'];
    }
    
    // function to set from
    
    public function setMessageFrom($msgFrom) {
        $this->message['from'] = $msgFrom;
    }
    
    // function to get shared date
    
    public function getMessageSharedDate() {
        return $this->message['sharedDate'];
    }
    
    // function to set shared date
    
    public function setMessageSharedDate($msgDate) {
        $this->message['sharedDate'] = $msgDate;
    }
    
    // public to send message
    
    public function sendMessage() {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_addMessage(?, ?, ?, ?, ?);");
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
                $this->db->closeDBConnection();
                $_SESSION['displayMessage'] = InfoMessage::MessageNotSent();
                return false;
            }
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }
        
        // close database connection
        
        $_SESSION['displayMessage'] = InfoMessage::messageSent($msgTo);
        $this->db->closeDBConnection();
        return true;
    }
    
    // function to get message id
    
    public function getMessageID() {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // get message details
        
        $subject = $this->message['subject'];
        $body = $this->message['body'];
        $to = $this->message['to'];
        $from = $this->message['from'];
        $sharedDate = $this->message['sharedDate'];
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // get message ID
        
            $query = "CALL usp_getMessageID('$subject', '$body', '$sharedDate', '$from', '$to');";
            $result = mysqli_query($this->db->con, $query);
            $messageID = mysqli_fetch_array($result, MYSQLI_NUM)[0];
            
            // return message ID
            
            if ($messageID) {
                return $messageID;
            } else {
                $this->db->closeDBConnection();
                $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
                return false;
            }
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }
        
        // close result and close database connection
        
        if($result && isset($messageID)) {
           $result->close(); 
        }      
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
    
    // function to show received message
    
    public function showReceivedMessage() {
        
        // get message properties
        
        $sharedBy = $this->message['from'];
        $subject = $this->message['subject'];
        $body = $this->message['body'];
        
        // display message
        
        echo "<h4 class='documents' id='hdrReceivedMessageFrom'>From</h4>
                <p class='p-message' id='pReceivedMessageFrom'>$sharedBy</p>
            <h4 class='documents' id='hdrReceivedMessageSubject'>Subject</h4>
                <p class='p-message' id='pReceivedMessageSubject'>$subject</p>
            <h4 class='documents' id='hdrReceivedMessageMessage'>Message</h4>
                <p class='p-message' id='pReceivedMessageMessage'>$body</h4>
            <h4 class='documents' id='hdrReceivedMessageAttachments'>Attachments</h4>";
    }
    
    // function to show sent message
    
    public function showSentMessage() {
        
        // get message properties
        
        $sharedBy = $this->message['to'];
        $subject = $this->message['subject'];
        $body = $this->message['body'];
        
        // show message
        
        echo "<h4 class='form-label' id='hdrSentMessagesMsgTo'>To</h4>
                <p class='p-message' id='pSentMessagesMsgShareBy'>$sharedBy</p>
            <h4 class='form-label' id='hdrSentMessageMsgSubject'>Subject</h4>
                <p class='p-message' id='pSentMessagesMsgSubject'>$subject</p>
            <h4 class='form-label' id='hdrSentMessagesMsgMessage'>Message</h4>
                <p class='p-message' id='pSentMessagesMsgMessage'>$body</p>
            <h4 class='form-label' id='hdrSentMessagesMsgAttachments'>Attachments</h4>";
    }
}
