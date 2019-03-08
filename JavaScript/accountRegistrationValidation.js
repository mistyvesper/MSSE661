$('document').ready(function() {
    
    // declare and initialize variables
    // http://regexlib.com/Search.aspx?k=email
    // https://www.sitepoint.com/jquery-basic-regex-selector-examples/
    
    emailRegEx = /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    userRegEx = /^\w{3,25}/;
    passRegEx = /^\w{6,25}/;
    
    // get directory 
    
    filePath = window.location.pathname;
    directory = filePath.substring(0, filePath.indexOf('/', 1));
    if (directory.indexOf('.php') > 0) {
        directory = '';
    } 
    
    // disable fields to start out with
    
//    $('#subRegisterForAccount').attr({disabled: true});
//    $('#inAccountRegisterUser').attr({disabled: true});
//    $('#inAccountRegisterPass').attr({disabled: true});
    
    // add blur event listeners

    $('#inAccountRegisterEmail').blur(validateEmail);    
    $('#inAccountRegisterUser').blur(validateUser);    
    $('#inAccountRegisterPass').blur(validatePass);
    
    // add focus event listeners
    
    $('#inAccountRegisterEmail').focus(resetEmailResults);
    $('#inAccountRegisterUser').focus(resetUserResults);
    $('#inAccountRegisterPass').focus(resetPassResults);
    
    // add keypress event listeners
    
    $('#inAccountRegisterUser').keypress(showUserHint);
    $('#inAccountRegisterPass').keypress(showPassHint);
    
    // function to validate email
    
    function validateEmail() {
        
        userEmail = $('#inAccountRegisterEmail').val();
        
        if (userEmail === '') {
            $('#imgAccountRegisterEmailResults').attr({src: directory + '/Media/x.png'});
            $('#tdAccountRegisterEmailTextResults').text('Email Cannot Be Blank');
            $('#tdAccountRegisterEmailTextResults').attr({class: 'red-text'});
            $('#imgAccountRegisterEmailResults').attr({class: 'form-results'});
//            $('#inAccountRegisterUser').attr({disabled: true});
//            $('#inAccountRegisterPass').attr({disabled: true});
//            $('#inAccountRegisterEmail').focus();
//            $('#inAccountRegisterEmail').select();
        } else if (!emailRegEx.test(userEmail)) {
            $('#imgAccountRegisterEmailResults').attr({src: directory + '/Media/x.png'});
            $('#tdAccountRegisterEmailTextResults').text('Invalid Email Address');
            $('#tdAccountRegisterEmailTextResults').attr({class: 'red-text'});
            $('#imgAccountRegisterEmailResults').attr({class: 'form-results'});
//            $('#inAccountRegisterUser').attr({disabled: true});
//            $('#inAccountRegisterPass').attr({disabled: true});
//            $('#inAccountRegisterEmail').focus();
//            $('#inAccountRegisterEmail').select();
        } else {
            $.ajax({
                async: false,
                type: "GET",
                url: "Ajax/getFunctions.php",
                data: {email: userEmail},
                dataType: "text",
                success: function(data) {
                    if (data) {
                        $('#imgAccountRegisterEmailResults').attr({src: directory + '/Media/x.png'});
                        $('#tdAccountRegisterEmailTextResults').text('Email Taken');
                        $('#tdAccountRegisterEmailTextResults').attr({class: 'red-text'});
//                        $('#inAccountRegisterUser').attr({disabled: true});
//                        $('#inAccountRegisterPass').attr({disabled: true});
//                        $('#inAccountRegisterEmail').focus();
//                        $('#inAccountRegisterEmail').select();
                    } else {
                        $('#imgAccountRegisterEmailResults').attr({src: directory + '/Media/checkmark.png'});
                        $('#tdAccountRegisterEmailTextResults').text('Email Available');
                        $('#tdAccountRegisterEmailTextResults').attr({class: 'green-text'});
//                        $('#inAccountRegisterUser').attr({disabled: false});
//                        $('#inAccountRegisterUser').focus();
                    }

                    $('#imgAccountRegisterEmailResults').attr({class: 'form-results'});
                },
                done: function(data) {
                    console.log('done: ' + data)
                }
              });
        }
    }
    
    // function to validate user
    
    function validateUser() {
        
        user = $('#inAccountRegisterUser').val();
        
        if (user === '') {
            $('#imgAccountRegisterUserResults').attr({src: directory + '/Media/x.png'});
            $('#tdAccountRegisterUserTextResults').text('User Name Cannot Be Blank');
            $('#tdAccountRegisterUserTextResults').attr({class: 'red-text'});
            $('#imgAccountRegisterUserResults').attr({class: 'form-results'});
//            $('#inAccountRegisterPass').attr({disabled: true});
//            $('#inAccountRegisterUser').focus();
//            $('#inAccountRegisterUser').select();
        } else if (!userRegEx.test(user)) {
            $('#imgAccountRegisterUserResults').attr({src: directory + '/Media/x.png'});
            $('#tdAccountRegisterUserTextResults').text('User Name Invalid');
            $('#tdAccountRegisterUserTextResults').attr({class: 'red-text'});
            $('#imgAccountRegisterUserResults').attr({class: 'form-results'});
//            $('#inAccountRegisterPass').attr({disabled: true});
//            $('#inAccountRegisterUser').focus();
//            $('#inAccountRegisterUser').select();
        } else {
            $.ajax({
            async: false,
            type: "GET",
            url: "Ajax/getFunctions.php",
            dataType: "html",
            data: {userName: user},
            success: function(data) {
                if (data) {
                    $('#imgAccountRegisterUserResults').attr({src: directory + '/Media/x.png'});
                    $('#tdAccountRegisterUserTextResults').text('User Name Taken');
                    $('#tdAccountRegisterUserTextResults').attr({class: 'red-text'});
//                    $('#inAccountRegisterPass').attr({disabled: true});
//                    $('#inAccountRegisterUser').focus();
//                    $('#inAccountRegisterUser').select();
                } else {
                    $('#imgAccountRegisterUserResults').attr({src: directory + '/Media/checkmark.png'});
                    $('#tdAccountRegisterUserTextResults').text('User Name Available');
                    $('#tdAccountRegisterUserTextResults').attr({class: 'green-text'});
//                    $('#inAccountRegisterPass').attr({disabled: false});
//                    $('#inAccountRegisterPass').focus();
                }
                
                $('#imgAccountRegisterUserResults').attr({class: 'form-results'});
            }
          });
        }
    }
    
    // function to validate password
    
    function validatePass() {
        
        pass = $('#inAccountRegisterPass').val();
        
        if (pass === '') {
            $('#imgAccountRegisterPassResults').attr({src: directory + '/Media/x.png'});
            $('#tdAccountRegisterPassTextResults').text('Password Cannot Be Blank');
            $('#tdAccountRegisterPassTextResults').attr({class: 'red-text'});
            $('#imgAccountRegisterPassResults').attr({class: 'form-results'});
//            $('#subRegisterForAccount').attr({disabled: true});
//            $('#inAccountRegisterPass').focus();
//            $('#inAccountRegisterPass').select();
        } else if (!passRegEx.test(pass)) {
            $('#imgAccountRegisterPassResults').attr({src: directory + '/Media/x.png'});
            $('#tdAccountRegisterPassTextResults').text('Password Invalid');
            $('#tdAccountRegisterPassTextResults').attr({class: 'red-text'});
            $('#imgAccountRegisterPassResults').attr({class: 'form-results'});
//            $('#subRegisterForAccount').attr({disabled: true});
//            $('#inAccountRegisterPass').focus();
//            $('#inAccountRegisterPass').select();
        } else {
            $('#imgAccountRegisterPassResults').attr({src: directory + '/Media/checkmark.png'});
            $('#tdAccountRegisterPassTextResults').text('Valid Password');
            $('#tdAccountRegisterPassTextResults').attr({class: 'green-text'});
            $('#imgAccountRegisterPassResults').attr({class: 'form-results'});
//            $('#subRegisterForAccount').attr({disabled: false});
//            $('#subRegisterForAccount').focus();
//            $('#subRegisterForAccount').select();
        }
    }
    
    // function to reset email results 
    
    function resetEmailResults() {
        $('#tdAccountRegisterEmailTextResults').attr({class: 'hidden'});
        $('#imgAccountRegisterEmailResults').attr({class: 'hidden'});
    }
    
    // function to reset user results
    
    function resetUserResults() {
        $('#tdAccountRegisterUserTextResults').attr({class: 'hidden'});
        $('#imgAccountRegisterUserResults').attr({class: 'hidden'});
    }
    
    // function to reset password results
    
    function resetPassResults() {
        $('#tdAccountRegisterPassTextResults').attr({class: 'hidden'});
        $('#imgAccountRegisterPassResults').attr({class: 'hidden'});
    }
    
    // function to display user hint
    
    function showUserHint() {
        $('#tdAccountRegisterUserTextResults').text('3-5 Alphanumeric Chars');
        $('#tdAccountRegisterUserTextResults').attr({class: 'black-text'});
    }
    
    // function to show password hint
    
    function showPassHint() {
        $('#tdAccountRegisterPassTextResults').text('6-25 Alphanumeric Chars');
        $('#tdAccountRegisterPassTextResults').attr({class: 'black-text'});
    }  
} 
);