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
        return "<span class='errorMessage' id='spnAccountCreateFail'>ERROR: Unable to create $appUser account. Please try again.</span>";
    }
    
    public function accountTaken($appUser) {
        return "<span class='errorMessage' id='spnAccountTaken'>ERROR: The username $appUser has already been taken. Please try again.</span>";
    }
    
    public function accountDeleted() {
        return "<span class='infoMessage' id='spnAccountDeleted'>Your account has been deleted. Redirecting...</span>";
    }
    
    public function accountNotDeleted($appUser) {
        return "<span class='errorMessage-large' id='spnAccountNotDeleted'>ERROR: Your account could not be deleted. Please try again.</span>";
    }
    
    public function attachmentsNoneSelected() {
        return "<span class='infoMessage' id='spnNoAttachSelected'>No documents selected.</span>";
    }
    
    public function dbConnectError() {
        return "<span class='errorMessage' id='spnDBConnectError'>ERROR: Error connecting to database. Please contact the system administrator.</span>";
    }
    
    public function dbNoRecords() {
        return "<span class='recordsMessage' id='spnDBNoRecords'>No records available.</span>";
    }
    
    public function documentsAdded() {
        return "<span class='infoMessage' id='spnDocsAdded'>The document(s) you selected have now been added to your collection.</span>";
    }
    
    public function documentsDeleted() {
        return "<span class='infoMessage' id='spnDocsDeleted'>The document(s) you selected have now been deleted.</span>";
    }
    
    public function documentsDuplicate($document) {
        return "<span class='errorMessage-large' id='spnDuplicateDoc'>ERROR: $document has already been added to your collection.</span>";
    }
    
    public function documentsNotAdded() {
        return "<span class='errorMessage-large' id='spnDocsNotAdded'>ERROR: The document(s) you selected could not be added to your collection.</span>";
    }
    
    public function documentsNotDeleted() {
        return "<span class='errorMessage-large' id='spnDocsNotDeleted'>ERROR: The document(s) you selected could not be deleted. Please try again.</span>";
    }
    
    public function documentsNotUpdated() {
        return "<span class='errorMessage-large' id='spnDocsNotUpdated'>ERROR: The document(s) you modified could not be updated. Please try again.</span>";
    }
    
    public function documentsUpdated() {
        return "<span class='infoMessage' id='spnDocsUpdated'>The document(s) you modified have now been updated.</span>";
    }
    
    public function emailTaken($appUserEmail) {
        return "<span class='errorMessage' id='spnEmailTaken'>ERROR: There is already an account associated with $appUserEmail. Please try again.</span>";
    }
    
    public function fileDirectoryNotCreated() {
        return "<span class='errorMessage' id='spnFileDirectoryNotCreated'>ERROR: The new directory could not be created.</span>";
    }
    
    public function fileDuplicate($file) {
        return "<span class='errorMessage-large' id='spnDuplicateFile'>ERROR: $file has already been uploaded. Please try a different file.</span>";
    }
    
    public function fileExceedsMaxSize($file) {
        return "<span class='errorMessage-large' id='spnMaxFileSize'>ERROR: $file max file size exceeded. Please try a different file.</span>";
    }
    
    public function fileNoFilesSelected() {
        return "<span class='errorMessage-large' id='spnNoFilesSelected'>ERROR: No file(s) selected. Please try again.</span>";
    }
    
    public function fileUnsupported($file) {
        return "<span class='errorMessage-large' id='spnFileUnsupported'>ERROR: $file file type not supported. Please try a different file.</span>";
    }

    public function fileUploadFailed($file) {
        return "<span class='errorMessage-large' id='spnFileUploadFailed'>ERROR: $file upload failed. Please try again or else contact the system administrator.</span>";
    }
    
    public function fileUploadSuccessful() {
        return "<span class='infoMessage' id='spnFileUploadSuccessful'>File upload successful. Redirecting...</span>";
    }
    
    public function friendDeleted($friend) {
        return "<span class='infoMessage' id='spnFriendDeleted'>You have now ended your friendship with $friend.</span>";
    }
    
    public function friendsRequestAccepted() {
        return "<span class='infoMessage' id='spnFriendAccepted'>You have accepted.</span>";
    }
    
    public function friendsRequestIgnored() {
        return "<span class='infoMessage' id='spnFriendIgnored'>You have ignored this friend request.</span>";
    }
    
    public function friendsRequestNotAccepted() {
        return "<span class='errorMessage-large' id='spnFriendNotAccepted'>ERROR: Unable to accept the request. Please try again.</span>";
    }
    
    public function friendsRequestNotIgnored() {
        return "<span class='errorMessage-large' id='spnFriendNotIgnored'>ERROR: Unable to ignore the request. Please try again.</span>";
    }
    
    public function friendsNo() {
        return "<span class='recordsMessage' id='spnMissingUserAndPW'>You have no friends.</span>";
    }
    
    public function friendsRequestNotSent() {
        return "<span class='errorMessage-large' id='spnFriendRequestNotSent'>ERROR: Your friend request could not be sent. Please try again.</span>";
    }
    
    public function friendsRequestSent($user) {
        return "<span class='infoMessage' id='spnFriendRequestSent'>Your friend request has been sent to $user.</span>";
    }
    
    public function invalidEmail() {
        return "<span class='errorMessage-large' id='spnInvalidEmail'>ERROR: Please provide a valid email address.</span>";
    }
    
    public function invalidEntries() {
        return "<span class='errorMessage-large' id='spnInvalidEntries'>ERROR: Please provide valid entries for each field.</span>";
    }
    
    public function invalidPassword() {
        return "<span class='errorMessage-large' id='spnInvalidPassword'>ERROR: The old password you provided does not match your current password. Please try again.</span>";
    }
    
    public function loginUnsuccessful() {
        return "<span class='errorMessage' id='spnLoginUnsuccessful'>ERROR: Login unsucessful. Please try again.</span>";
    }
    
    public function messageNotSent() {
        return "<span class='errorMessage-large' id='spnMessageNotSent'>ERROR: Unable to send message. Please try again.</span>";
    }
    
    public function messageSent($sentTo) {
        return "<span class='infoMessage' id='spnMessageSent'>Your message has been sent to $sentTo. Redirecting...</span>";
    }
    
    public function messagesDeleted() {
        return "<span class='infoMessage' id='spnMessageDeleted'>The message(s) you selected has been successfully deleted. </span>";
    }
    
    public function messagesNoReceived() {
        return "<span class='recordsMessage' id='spnNoReceivedMessages'>You have no received messages.</span>";
    }
    
    public function messagesNoSent() {
        return "<span class='recordsMessage' id='spnNoSentMessages'>You have not sent any messages.</span>";
    }
    
    public function messagesNotDeleted() {
        return "<span class='errorMessage-large' id='spnMessageNotDeleted'>ERROR: The message(s) you selected could not be deleted. Please try again. </span>";
    }
    
    public function missingTo() {
        return "<span class='errorMessage-large' id='spnMissingTo'>ERROR: You must enter a valid user to share these documents with. Please try again.</span>";
    }
    
    public function missingPW() {
        return "<span class='errorMessage' id='spnMissingPW'>ERROR: Please provide a valid password.</span>";
    }
    
    public function missingUser() {
        return "<span class='errorMessage' id='spnMissingUser'>ERROR: Please provide a valid username.</span>";
    }
    
    public function missingUserAndPW() {
        return "<span class='errorMessage' id='spnMissingUserAndPW'>ERROR: Please provide a valid username and password.</span>";
    }
    
    public function passwordsDontMatch() {
        return "<span class='errorMessage-large' id='spnPasswordsDontMatch'>ERROR: The new passwords you entered don't match. Please try again.</span>";
    }
    
    public function passwordUpdated() {
        return "<span class='infoMessage' id='spnPWUpdated'>ERROR: Your password has been updated.</span>";
    }
    
    public function passwordUpdateFailed() {
        return "<span class='errorMessage-large' id='spnPWUpdateFailed'>ERROR: Your password could not be updated. Please try again.</span>";
    }
    
    public function publicCollectionBlankTitle() {
        return "<span class='errorMessage-large' id='spnBlankCollectTitle'>ERROR: The title field cannot be blank. Please try again.</span>";
    }
    
    public function publicCollectionAdded($collectionTitle) {
        return "<span class='infoMessage' id='spnPublicCollectAdded'>The '$collectionTitle' collection has been successfully added.</span>";
    }
    
    public function publicCollectionNotAdded($collectionTitle) {
        return "<span class='errorMessage-large' id='spnPublicCollectNotAdded'>ERROR: The '$collectionTitle' collection could not be added. Please try again.</span>";
    }
    
    public function publicCollectionDeleted($collectionTitle) {
        return "<span class='infoMessage' id='spnPublicCollectDeleted'>The '$collectionTitle' collection has been deleted.</span>";
    }
    
    public function publicCollectionNotDeleted($collectionTitle) {
        return "<span class='errorMessage-large' id='spnPublicCollectNotDeleted'>ERROR: The '$collectionTitle' collection could not be deleted. Please try again.</span>";
    }
    
    public function publicCollectionNotSelected() {
        return "<span class='errorMessage-large' id='spnPublicCollectNotSelected'>ERROR: No valid collection has been selected. Please return to the <a href='managePublicCollections'>View/Manage Public Collections</a> page to make a selection.</span>";
    }
    
    public function publicCollectionNoDocumentsSelected() {
        return "<span class='errorMessage-large' id='spnPublicCollectNoDocsSelected'>ERROR: No documents have been selected for sharing. Please add some documents and try again.</span>";
    }
    
    public function publicCollectionDocumentsNotAdded() {
        return "<span class='errorMessage-large' id='spnPublicCollectNoDocsAdded'>ERROR: The documents you selected could not be added to a collection. Please try again.</span>";
    }
    
    public function publicCollectionNoneAvailable() {
        return "<span class='recordsMessage' id='spnPublicCollectNone'>No collections available. Please visit the <a href='managePublicCollections'>View/Manage Public Collections</a> page to create a collection.</span>";
    }
    
    public function publicCollectionDuplicateDocument($documentTitle) {
        return "<span class='errorMessage-large' id='spnPublicCollectDuplicate'>ERROR: $documentTitle could not be added to the collection because it's already been added. Please try adding a different document.</span>";
    }
    
    public function profileUpdated() {
        return "<span class='infoMessage' id='spnProfileUpdated'>Your profile has been updated.</span>";
    }
    
    public function profileUpdateFailed() {
        return "<span class='errorMessage-large' id='spnProfileUpdateFailed'>ERROR: Your profile could not be updated. Please try again.</span>";
    }
    
    public function searchValue($searchValue) {
        return "$searchValue";
    }
    
    public function usersNo() {
        return "<span class='recordsMessage' id='spnNoUsers'>No users found.</span>";
    }
}
