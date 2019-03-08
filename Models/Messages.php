<?php

/**
 * Description of Messages
 *
 * Provides CRUD methods for messages shared between users.
 * 
 * @author misty
 */

class Messages {
    
     // encapsulate Messages properties by declaring private
    
    private $messages = []; // array of messages
    private $message;
    private $sentMessages = [];
    private $messagesUser;
    private $db;
    private $seed; // for reseeding AUTO_INCREMENT
    
    // constructor requires user and database connection to instantiate
    
    public function __construct($user, $database) {
        $this->messagesUser = $user;
        $this->db = $database;
    }    
    
    // function to get messages array

    public function getReceivedMessages($user) { 
            
        // reset messages array

        $this->messages = [];

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get messages

            $query = "CALL usp_getReceivedMessagesByUser('$user');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->messages[] = $row;
            }
            
            // return messages
            
            $_SESSION['messages'] = $this->messages;
            return $this->messages;
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return false;
        }

        // close result and database connection

        if ($result && isset($this->messages)) {
            $result->close();
        }
        $this->db->closeDBConnection();
    }
    
    // function to get sent messages array

    public function getSentMessages($user) { 
            
        // reset messages array

        $this->sentMessages = [];

        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get messages

            $query = "CALL usp_getSentMessagesByUser('$user');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->sentMessages[] = $row;
            }
            
            // return messages
            
            $_SESSION['sentMessages'] = $this->sentMessages;
            return $this->sentMessages;
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbNoRecords();
            return false;
        }

        // close result and database connection

        if ($result && isset($this->sentMessages)) {
            $result->close();
        }
        
        $this->db->closeDBConnection();
    }
    
    // function to get message by id
    
    public function getReceivedMessageByID($msgID) {
        
        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get message

            $query = "CALL usp_getMessageByID('$msgID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $subject = $row['messageSubject'];
                $body = $row['messageBody'];
                $sharedDate = $row['sharedDate'];
                $sharedBy = $row['sharedBy'];
            }
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }

        // close result and database connection

        if ($result && isset($subject)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return message
        
        $this->message = new Message($subject, $body, $this->messagesUser, $sharedBy, $sharedDate, $this->db);
        if (isset($this->message)) {
            return $this->message;
        } else {
            return false;
        }
    }
    
    // function to get message by id
    
    public function getSentMessageByID($msgID) {
        
        // open database connection

        $this->db->getDBConnection();

        // check for errors

        if (!$this->db->con->connect_error ) {

            // get message

            $query = "CALL usp_getMessageByID('$msgID');";
            $result = mysqli_query($this->db->con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $subject = $row['messageSubject'];
                $body = $row['messageBody'];
                $sharedDate = $row['sharedDate'];
                $sharedBy = $row['sharedBy'];
            }
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }

        // close result and database connection

        if ($result && isset($subject)) {
            $result->close();
        }
        $this->db->closeDBConnection();
        
        // return message
        
        $this->message = new Message($subject, $body, $sharedBy, $this->messagesUser, $sharedDate, $this->db);
        if (isset($this->message)) {
            return $this->message;
        } else {
            return false;
        }
    }
    
    // function to mark message as read
    
    public function readMessage($msgID) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_readMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // read message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                $this->db->closeDBConnection();
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                return false;
            } 
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;   
    }
    
    // function to delete received messages 
    
    public function deleteReceivedMessages() {
        
        // initialize error
        
        $error = false;
        
        // delete received messages
        
        foreach ($_POST['messages'] as $item) {
            if (!$this->deleteReceivedMessage($item)) {
                $error = true;
            }
        }
        
        // check for errors
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::messagesNotDeleted();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::messagesDeleted();
        }
    }
    
    // function to delete received message
       
    public function deleteReceivedMessage($msgID) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_deleteReceivedMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // delete message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                $this->db->closeDBConnection();
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                return false;
            } 
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;   
    }
    
    // function to delete sent messages
    
    public function deleteSentMessages() {
        
        // initialize error
        
        $error = false;
        
        // delete messages
        
        foreach ($_POST['messages'] as $item) {
            if (!$this->deleteSentMessage($item)) {
                $error = true;
            }
        }
        
        // check for errors
        
        if ($error) {
            $_SESSION['displayMessage'] = InfoMessage::messagesNotDeleted();
        } else {
            $_SESSION['displayMessage'] = InfoMessage::messagesDeleted();
        }
    }
    
    // function to delete sent message
       
    public function deleteSentMessage($msgID) {
        
        // open database connection
        
        $this->db->getDBConnection();
        
        // check for errors
        
        if (!$this->db->con->connect_error ) {
            
            // prepare insert statement
            
            $statement = $this->db->con->prepare("CALL usp_deleteSentMessage(?);");
            $statement->bind_param('i', $messageID);
            
            // get message properties
        
            $messageID = $msgID;
        
            // delete message
        
            $statement->execute();
            
            // check for errors
            
            if ($statement->error != '') {
                $this->db->closeDBConnection();
                $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
                return false;
            } 
        } else {
            $this->db->closeDBConnection();
            $_SESSION['displayMessage'] = InfoMessage::dbConnectError();
            return false;
        }
        
        // close database connection
        
        $this->db->closeDBConnection();
        return true;   
    }
    
    // function to show navigation bar
    
    public function showMessageNavigationBar() {
        
        if (__FILE__ == 'viewAllReceivedMessages.php') {
            echo "<span class='documents' id='spnReceivedMessages'>
                    <div class='topnav' id='divReceivedMessages'>
                        <a class='linkActive' id='lnkViewReceivedMessages' href='viewAllReceivedMessages.php'>Received Messages</a>
                        <a class='link' id='lnkViewSentMessages' href='viewAllSentMessages.php'>Sent Messages</a>
                      </div>";
        } else {
            echo "<span class='documents' id='spnReceivedMessages'>
                    <div class='topnav' id='divReceivedMessages'>
                        <a class='link' id='lnkViewReceivedMessages' href='viewAllReceivedMessages.php'>Received Messages</a>
                        <a class='linkActive' id='lnkViewSentMessages' href='viewAllSentMessages.php'>Sent Messages</a>
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

            echo "<form class='documents' id='formReceivedMessages' method='post' action='viewAllReceivedMessages.php' enctype='multipart/form-data'>
                    <table class='documents' id='tblReceivedMessages'>
                        <thead class='documents' id='theadReceivedMessages'>
                            <tr class='documents' id='trReceivedMessagesHeaders'>
                                <th class='form-submit-small-header-center' id='thReceivedMessagesSelectHeader' disabled>
                                    <input class='form-submit-small-header-center' id='subReceivedMessagesSelectHeader' type='submit' name='select' value='Select' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thReceivedMessagesReadHeader'>
                                    <input class='form-submit-small-header-center' id='subReceivedMessagesReadHeader' type='submit' name='sortRead' value='Read' disabled>
                                </th>
                                <th class='form-submit-large-header-left' id='thReceivedMessagesSubjectHeader'>
                                    <input class='form-submit-large-header-left' id='subReceivedMessagesSubjectHeader' type='submit' name='sortSubject' value='Subject' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thReceivedMessagesFromHeader'>
                                    <input class='form-submit-small-header-center' id='subReceivedMessagesFromHeader' type='submit' name='sortFrom' value='From' disabled>
                                </th>
                                <th class='form-submit-medium-header-center' id='thReceivedMessagesSharedDateHeader'>
                                    <input class='form-submit-medium-header-center' id='subReceivedMessagesSharedDateHeader' type='submit' name='sortSharedDate' value='Shared Date' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thReceivedMessagesAttachmentsHeader'>
                                    <input class='form-submit-small-header-center' id='subReceivedMessagesAttachmentsHeader' type='submit' name='sortAttachments' value='Attach #' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thReceivedMessagesAttachmentsHeader'>
                                    <input class='form-submit-small-header-center' id='subReceivedMessagesAttachmentsHeader' type='submit' name='viewReceivedMessage' value='View' disabled>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='documents' id='tbodyReceivedMessages'>";
            
            foreach ($this->messages as $key => $message) {
                
                $messageID = $message['messageID'];
                $readFlag = $message['readFlag'];
                $subject = $message['messageSubject'];
                $from = $message['sharedBy'];
                $sharedDate = $message['sharedDate'];
                $attachments = $message['attachmentCount'];
                $url = "viewReceivedMessage.php?messageID=". $messageID;

                echo "<tr class='documents' id='trReceivedMessages'>
                        <td class='form-text-small-center' id='tdReceivedMessagesCheckbox'>
                            <input class='checkbox' id='chkReceivedMessagesCheckbox" . $key . "' type='checkbox' name='messages[]' value='$messageID'>
                        </td>
                        <td class='form-text-small-center' id='tdReceivedMessagesRead'>$readFlag</td>
                        <td class='form-text-large-left' id='tdReceivedMessagesSubject'>$subject</td>
                        <td class='form-text-small-center' id='tdReceivedMessagesFrom'>$from</td>
                        <td class='form-text-medium-center' id='tdReceivedMessagesSharedDate'>$sharedDate</td>
                        <td class='form-text-small-center' id='tdReceivedMessagesAttachments'>$attachments</td>
                        <td class='form-text-small-center' id='tdReceivedMessagesView'>
                            <input class='form-submit-small-center-gray' id='subReceivedMessagesView' type='submit' name='viewReceivedMessage[$messageID]' value='View'>
                        </td>
                    </tr>";
            }

            echo "</tbody></table>
                    <br>
                    <input class='form-submit-button' id='subReceivedMessagesDelete' type='submit' name='deleteReceivedMessages' value='Delete'>
                </form>";
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

            echo "<form class='documents' id='frmSentMessages' method='post' action='viewAllSentMessages.php' enctype='multipart/form-data'>
                    <table class='documents' id='tblSentMessages'>
                        <thead class='documents' id='theadSentMessages'>
                            <tr class='documents' id='trSentMessagesHeaders'>
                                <th class='form-submit-small-header-center' id='thSentMessagesSelectHeader'>
                                    <input class='form-submit-small-header-center' id='subSentMessagesSelectHeader' type='submit' name='select' value='Select' disabled>
                                </th>
                                <th class='form-submit-small-small-center' id='thSentMessagesReadHeader'>
                                    <input class='form-submit-small-header-center' id='subSentMessagesReadHeader' type='submit' name='submit' value='Read' disabled>
                                </th>
                                <th class='form-submit-large-header-left' id='thSentMessagesSubjectHeader'>
                                    <input class='form-submit-large-header-left' id='subSentMessagesSubjectHeader' type='submit' name='sortSubject' value='Subject' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thSentMessagesFromHeader'>
                                    <input class='form-submit-small-header-center' id='subSentMessagesFromHeader' type='submit' name='sortFrom' value='To' disabled>
                                </th>
                                <th class='form-submit-medium-header-center' id='thSentMessagesShareDateHeader'>
                                    <input class='form-submit-medium-header-center' id='subMessagesShareDateHeader' type='submit' name='sortSharedDate' value='Shared Date' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thSentMessagesAttachmentsHeader'>
                                    <input class='form-submit-small-header-center' id='subSentMessagesAttachmentsHeader' type='submit' name='sortAttachments' value='Attach #' disabled>
                                </th>
                                <th class='form-submit-small-header-center' id='thSentMessagesViewHeader'>
                                    <input class='form-submit-small-header-center' id='thSentMessagesViewHeader' type='submit' name='viewSentMessage' value='View' disabled>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='documents' id='tbodySentMessages'>";
            
            foreach ($this->sentMessages as $key => $message) {
                
                $messageID = $message['messageID'];
                $readFlag = $message['readFlag'];
                $subject = $message['messageSubject'];
                $from = $message['sharedBy'];
                $sharedDate = $message['sharedDate'];
                $attachments = $message['attachmentCount'];
                $url = "viewSentMessage.php?messageID=". $messageID;

                echo "<tr class='documents' id='trSentMessages'>
                        <td class='form-text-small-center' id='tdSentMessagesCheckbox'>
                            <input class='checkbox' id='chkSentMessagesCheckbox" . $key . "' type='checkbox' name='messages[]' value='$messageID'>
                        </td>
                        <td class='form-text-small-center' id='tdSentMessagesRead'>$readFlag</td>
                        <td class='form-text-large-left' id='tdSentMessagesSubject'>$subject</td>
                        <td class='form-text-small-center' id='tdSentMessagesFrom'>$from</td>
                        <td class='form-text-medium-center' id='tdSentMessagesShareDate'>$sharedDate</td>
                        <td class='form-text-small-center' id='tdSentMessagesAttachments'>$attachments</td>
                        <td class='form-text-small-center' id='tdSentMessagesView'>
                            <input class='form-submit-small-center-gray' id='subSentMessagesView' type='submit' name='viewSentMessage[$messageID]' value='View'>
                        </td>
                    </tr>";
            }

            echo "</tbody></table>
                    <br>
                    <input class='form-submit-button' id='subSentMessagesDelete' type='submit' name='deleteSentMessages' value='Delete'>
                </form>";
        }
    }
}
