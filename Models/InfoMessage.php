<?php

/**
 * Description
 * 
 * Provides different error messages to be displayed by web app.
 *
 * @author misty
 */

class InfoMessage {    
    
    public function accountCreationUnsuccessful($appUser) {
        return "<div>Unable to create $appUser account. Please try again.</div>";
    }
    
    public function accountTaken($appUser) {
        return "<div>The username $appUser has already been taken. Please try again.</div>";
    }
    
    public function dbConnectError() {
        return "<div>Error connecting to database. Please contact the system administrator.<br></div>";
    }
    
    public function dbDeleteError() {
        return "<div>Unable to delete record. Please try again or else contact the system administrator.<br></div>";
    }
    
    public function dbInsertError() {
        return"<div>Unable to insert record. Please try again or else contact the system administrator.<br></div>";
    }
    
    public function dbNoRecords() {
        return "<div>No records available.</echo>";
    }
    
    public function emailTaken($appUserEmail) {
        return "<div>There is already an account associated with $appUserEmail. Please try again.</div>";
    }
    
    public function fileDuplicate() {
        return "<div>File has already been uploaded. Please try a different file.</div>";
    }
    
    public function fileExceedsMaxSize() {
        return "<div>Max file size exceeded. Please try a different file.</div>";
    }
    
    public function fileUnsupported() {
        return "<div>File type not supported. Please try a different file.</div>";
    }

    public function fileUploadFailed() {
        return "<div>File upload failed. Please try again or else contact the system administrator.</div>";
    }
    
    public function fileUploadSuccessful() {
        return "<div>File upload successful.</div>";
    }
    
    public function invalidEmail() {
        return '<div>Please provide a valid email address.</div>';
    }
    
    public function invalidEntries() {
        return '<div>Please provide valid entries for each field.</div>';
    }
    
    public function invalidPassword() {
        return '<div>The old password you provided does not match your current password. Please try again.</div>';
    }
    
    public function loginUnsuccessful() {
        return '<div>Login unsucessful. Please try again.</div>';
    }
    
    public function messageNotSent() {
        return "<div>Unable to send message. Please try again.</div>";
    }
    
    public function messageSent($sentTo) {
        return "<div>Your message has been sent to $sentTo.</div>";
    }
    
    public function missingTo() {
        return '<div>You must enter a valid user to share these documents with. Please try again.</div>';
    }
    
    public function missingPW() {
        return '<div>Please provide a valid password.</div>';
    }
    
    public function missingUser() {
        return '<div>Please provide a valid username.</div>';
    }
    
    public function missingUserAndPW() {
        return '<div>Please provide a valid username and password.</div>';
    }
    
    public function passwordsDontMatch() {
        return "<div>The new passwords you entered don't match. Please try again.</div>";
    }
    
    public function passwordUpdated() {
        return '<div>Your password has been updated</div>';
    }
    
    public function passwordUpdateFailed() {
        return '<div>Your password could not be updated. Please try again.</div>';
    }
    
    public function profileUpdated() {
        return '<div>Your profile has been updated.</div>';
    }
    
    public function profileUpdateFailed() {
        return '<div>Your profile could not be updated. Please try again.</div>';
    }
    
    public function searchValue($searchValue) {
        return "<div>Search Value: $searchValue</div>";
    }
}
