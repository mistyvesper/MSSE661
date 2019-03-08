$('document').ready(function() {    
    
    checkPendingCollectionDocsCount();
    
    // disable buttons to start out with
    
    $('#subMyDocumentsUpdate').attr({disabled: true});
    $('#subMyDocumentsShare').attr({disabled: true});
    $('#subMyDocumentsAddToCollection').attr({disabled: true});
    $('#subMyDocumentsDelete').attr({disabled: true});
    $('#subViewAllFriendsDelete').attr({disabled: true});
    $('#subReceivedMessagesDelete').attr({disabled: true});
    $('#subSentMessagesDelete').attr({disabled: true});
    if ($('#selShareDocumentsWith').val() === '') {
        $('#subPendingShareDocumentsSend').attr({disabled: true});
    } else {
        $('#subPendingShareDocumentsSend').attr({disabled: false});
    }
    
    // add event listeners to elements
    
    $('select').focus(checkForTypeUpdates);
    $('.inMyDocumentsTitle').keypress(checkForTitleUpdates);
    $('.inMyDocumentsTitle').mouseover(checkForTitleBlanks);
    $('.checkbox').change(checkForSelections);
    $('#subMyDocumentsUpdate').mouseover(checkForTitleBlanks);
    $('#selShareDocumentsWith').blur(checkForBlankRecipient);
    $('#subPendingShareDocumentsSend').mouseover(checkForBlankRecipient);
    
    // function to check for updates to document type
    
    function checkForTypeUpdates() {
        $('#subMyDocumentsUpdate').attr({disabled: false});
    }
    
    // function to check for updates to document title

    function checkForTitleUpdates() {
        $('#subMyDocumentsUpdate').attr({disabled: false});
    }
    
    // function to check for blank titles
    
    function checkForTitleBlanks() {
        $('.inMyDocumentsTitle').each(function() {
            if ($(this).val() === '') {
                $('#subMyDocumentsUpdate').attr({disabled: true});
            } 
        })
    }
    
    // function to check for selections
    
    function checkForSelections() {
        
        checkedCount = 0;
        checkboxes = $('.checkbox');
        checkboxesLength = checkboxes.length;
        href = document.location.href;
        lastPathSegment = href.substr(href.lastIndexOf('/') + 1);   
        
        switch (lastPathSegment) {
            case 'index.php':
                for (i=0; i<checkboxes.length; i++) {
                    checkbox = $('#chkMyDocumentsCheckbox' + i);
                    if (checkbox.is(':checked')) {
                        checkedCount++;
                    }
                }
                toggleDocumentButtons();
            case 'viewAllFriends.php':
                for (i=0; i<checkboxes.length; i++) {
                    checkbox = $('#chkViewAllFriendsSelect' + i);
                    if (checkbox.is(':checked')) {
                        checkedCount++;
                    }
                }
                toggleFriendsButton();
            case 'viewAllReceivedMessages.php':
                for (i=0; i<checkboxes.length; i++) {
                    checkbox = $('#chkReceivedMessagesCheckbox' + i);
                    if (checkbox.is(':checked')) {
                        checkedCount++;
                    }
                }
                toggleReceivedMessagesButton();
            case 'viewAllSentMessages.php':
                for (i=0; i<checkboxes.length; i++) {
                    checkbox = $('#chkSentMessagesCheckbox' + i);
                    if (checkbox.is(':checked')) {
                        checkedCount++;
                    }
                }
                toggleSentMessagesButton();
        }
    }
    
    // function to check if count of pending collection docs > 0
    
    function checkPendingCollectionDocsCount() {
        
        pendingPublicCollectDocs = $('#tbodyPendingPublicCollectDocs').children('tr');
        if (pendingPublicCollectDocs.length === 0) {
            $('#subAddToCollectionAddConfirm').attr({disabled: true});
        } else {
            $('#subAddToCollectionAddConfirm').attr({disabled: false});
        }
    }
    
    // function to toggle document buttons
    
    function toggleDocumentButtons() {
        if (checkedCount > 0) {
            $('#subMyDocumentsShare').attr({disabled: false});
            $('#subMyDocumentsAddToCollection').attr({disabled: false});
            $('#subMyDocumentsDelete').attr({disabled: false});
        } else {
            $('#subMyDocumentsShare').attr({disabled: true});
            $('#subMyDocumentsAddToCollection').attr({disabled: true});
            $('#subMyDocumentsDelete').attr({disabled: true});
        }
    }
    
    // function to toggle friends button
    
    function toggleFriendsButton() {
        if (checkedCount > 0) {
            $('#subViewAllFriendsDelete').attr({disabled: false});
        } else {
            $('#subViewAllFriendsDelete').attr({disabled: true});
        }
    }
    
    // function to toggle received messages button
    
    function toggleReceivedMessagesButton() {
        if (checkedCount > 0) {
            $('#subReceivedMessagesDelete').attr({disabled: false});
        } else {
            $('#subReceivedMessagesDelete').attr({disabled: true});
        }
    }
    
    // function to toggle sent messages button
    
    function toggleSentMessagesButton() {
        if (checkedCount > 0) {
            $('#subSentMessagesDelete').attr({disabled: false});
        } else {
            $('#subSentMessagesDelete').attr({disabled: true});
        }
    }
    
    // function to check for blank message recipients 
    
    function checkForBlankRecipient() {
        if ($('#selShareDocumentsWith').val() === '') {
            $('#subPendingShareDocumentsSend').attr({disabled: true});
        } else {
            $('#subPendingShareDocumentsSend').attr({disabled: false});
        }
    }
    
});