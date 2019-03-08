$('document').ready(function() {
    
    $('#tblMyDocuments').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": false,
        "searching": false
    });
    
    $('#tblPendingShareDocuments').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": false
    });
    
    $('#tblPendingPublicCollectDocs').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": false
    });
    
    $('#tblViewAllFriends').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": true
    });
    
    $('#tblViewAllUsers').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": true
    });
    
    $('#tblViewPublicCollection').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": true
    });
    
    $('#tblShowPublicCollections').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": true
    });
        
    $('#tblReceivedMessages').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": true
    });
    
    $('#tblReceivedDocumentsMsg').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": false
    });
    
    $('#tblSentMessages').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": true
    });
    
    $('#tblSentMessagesMsg').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": false
    });
    
    $('#tblUploadDocuments').DataTable({
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "ordering": true,
        "searching": false
    });
});