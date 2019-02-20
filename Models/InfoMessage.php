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
    
    public function attachmentsNoneSelected() {
        return "<div>No documents selected.</div>";
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
    
    public function documentsAdded() {
        return "<div>The document(s) you selected have now been added to your collection.</echo>";
    }
    
    public function documentsDeleted() {
        return "<div>The document(s) you selected have now been deleted.</echo>";
    }
    
    public function documentsDuplicate($document) {
        return "<div>$document has already been added to your collection.</echo>";
    }
    
    public function documentsNotAdded() {
        return "<div>The document(s) you selected could not be added to your collection</div>";
    }
    
    public function documentsNotDeleted() {
        return "<div>The document(s) you selected could not be deleted. Please try again.</echo>";
    }
    
    public function documentsNotUpdated() {
        return "<div>The document(s) you modified could not be updated. Please try again.</echo>";
    }
    
    public function documentsUpdated() {
        return "<div>The document(s) you modified have now been updated.</echo>";
    }
    
    public function emailTaken($appUserEmail) {
        return "<div>There is already an account associated with $appUserEmail. Please try again.</div>";
    }
    
    public function fileDuplicate($file) {
        return "<div>$file has already been uploaded. Please try a different file.</div>";
    }
    
    public function fileExceedsMaxSize($file) {
        return "<div>$file max file size exceeded. Please try a different file.</div>";
    }
    
    public function fileNoFilesSelected() {
        return "<div>No file(s) selected. Please try again.</div>";
    }
    
    public function fileUnsupported($file) {
        return "<div>$file file type not supported. Please try a different file.</div>";
    }

    public function fileUploadFailed($file) {
        return "<div>$file upload failed. Please try again or else contact the system administrator.</div>";
    }
    
    public function fileUploadSuccessful() {
        return "<div>File upload successful. Redirecting...</div>";
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
        return "<div>Your message has been sent to $sentTo. Redirecting...</div>";
    }
    
    public function messagesDeleted() {
        return "<div>The message(s) you selected have been successfully deleted. </div>";
    }
    
    public function messagesNoReceived() {
        return "<div>You have no received messages.</div>";
    }
    
    public function messagesNoSent() {
        return "<div>You have not sent any messages.</div>";
    }
    
    public function messagesNotDeleted() {
        return "<div>The message(s) you selected could not be deleted. Please try again. </div>";
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
        return "$searchValue";
    }
}
