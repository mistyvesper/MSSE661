<?php

/**
 * Description
 * 
 * Provides different error messages to be displayed by web app.
 *
 * @author misty
 */

class Message {
    
    public function dbConnectError() {
        echo "<div>Error connecting to database. Please contact the system administrator.<br></div>";
    }
    
    public function dbNoRecords() {
        echo "<div>No records available.</echo>";
    }
    
    public function dbInsertError() {
        echo "<div>Unable to insert record. Please try again or else contact the system administrator.<br></div>";
    }
    
    public function dbDeleteError() {
        echo "<div>Unable to delete record. Please try again or else contact the system administrator.<br></div>";
    }
    
    public function fileUploadSuccessful() {
        echo "<div>File upload successful.</div>";
    }
    
    public function fileUploadFailed() {
        echo "<div>File upload failed. Please try again or else contact the system administrator.</div>";
    }
    
    public function fileDuplicate() {
        echo "<div>File has already been uploaded. Please try a different file.</div>";
    }
    
    public function fileUnsupported() {
        echo "<div>File type not supported. Please try a different file.</div>";
    }
    
    public function fileExceedsMaxSize() {
        echo "<div>Max file size exceeded. Please try a different file.</div>";
    }
    
    public function loginSuccessful() {
        echo '<div>Successfully logged in.</div>';
    }
    
    public function loginUnsuccessful() {
        echo '<div>Login unsucessful. Please try again.</div>';
    }
    
    public function missingUserAndPW() {
        echo '<div>Please provide a valid username and password.</div>';
    }
    
    public function missingUser() {
        echo '<div>Please provide a valid username.</div>';
    }
    
    public function missingPW() {
        echo '<div>Please provide a valid password.</div>';
    }
    
    public function accountTaken($appUser) {
        echo "<div>The username $appUser has already been taken. Please try again.</div>";
    }
    
    public function emailTaken($appUserEmail) {
        echo "<div>There is already an account associated with $appUserEmail. Please try again.</div>";
    }
    
    public function accountCreationUnsuccessful($appUser) {
        echo "<div>Unable to create $appUser account. Please try again.</div>";
    }
    
    public function invalidEntries() {
        echo '<div>Please provide valid entries for each field.</div>';
    }
    
    public function invalidEmail() {
        echo '<div>Please provide a valid email address.</div>';
    }
}
