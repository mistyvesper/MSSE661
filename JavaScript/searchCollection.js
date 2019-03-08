$('document').ready(function() {

    $('#inSearchValue').keyup(getSearchValues); 

    function getSearchValues() {

        searchValue = $('#inSearchValue').val();

        $.ajax({
        async: true,
        type: "POST",
        url: "Ajax/postFunctions.php",
        data: {search: searchValue},
        dataType: "text",
        success: function(data) {
            $('#tblMyDocuments').load('/index.php #tblMyDocuments');
        },
        done: function(data) {
            console.log('done: ' + data)
        },
        error: function(data) {
            console.log('error: ' + data.responseText)
        }
      });
    }
});