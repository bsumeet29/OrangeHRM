$(document).ready(function() {
//    $("#chkLogin").attr("checked", true);
    $(".loginSection").hide();
    if (ldapInstalled == 'true') {
        $("#password_required").hide();
        $("#rePassword_required").hide();
    }    

    

    $("#addEmployeeTbl td div:empty").remove();
    $("#addEmployeeTbl td:empty").remove();
    
    $('#photofile').after('<label class="fieldHelpBottom">'+fieldHelpBottom+'</label>');

    if(createUserAccount == 0) {
        //hiding login section by default
        
        $("#chkLogin").removeAttr("checked");
    }

    //default edit button behavior
    $("#btnSave").click(function() {
        $("#frmAddEmp").submit();
    });

//    $("#chkLogin").click(function() {
//        $(".loginSection").hide();
//
////        $("#user_name").val("");
////        $("#user_password").val("");
////        $("#re_password").val("");
//        $("#status").val("Enabled");
//
//        if($("#chkLogin").is(':checked')) {
//            $(".loginSection").show();
//        }
//    });

        //form validation
    $("#frmAddEmp").validate({
        rules: {
            'firstName': {required: true },
            'lastName': { required: true },
            'user_name': { 
                required: true,
                email:true
            },
            'user_password': {required: true},
            're_password': {validateReCheckPassword: true},
            'status': {required: true },
            'location': {required: true },
            'paymentMode':{required: true }
        },
        messages: {
            'firstName': { required: lang_firstNameRequired },
            'lastName': { required: lang_lastNameRequired },
            'user_name': { 
                required: lang_userNameRequired,
                email:"Invalid email format."
            },
            'user_password': {required: lang_passwordRequired},
            're_password': {validateReCheckPassword: lang_unMatchingPassword},
            'status': {required: lang_statusRequired },
            'location': {required: lang_locationRequired },
            'paymentMode':{required: lang_locationRequired }
        }
    });

    $.validator.addMethod("validateLoginName", function(value, element) {
        if($("#chkLogin").is(':checked') && !(ldapInstalled == 'true')) {
            if(value.length < 5) {
                return false;
            }
        } else if ($("#chkLogin").is(':checked') && (ldapInstalled == 'true')) {
            if(value.length < 1) {
                return false;
            }
		}
        return true;
    });

    $.validator.addMethod("validatePassword", function(value, element) {
        if($("#chkLogin").is(':checked') && !(ldapInstalled == 'true')) {
            if(value.length < 4) {
                return false;
            }
        }
        return true;
    });

    $.validator.addMethod("validateReCheckPassword", function(value, element) {
        
            if(value != $("#user_password").val()) {
                return false;
            }
        
        return true;
    });

    $.validator.addMethod("validateStatusRequired", function(value, element) {
        if($("#chkLogin").is(':checked')) {
            if(value == "") {
                return false;
            }
        }
        return true;
    });

    $("#btnCancel").click(function(){
       navigateUrl("viewEmployeeList");
    });
    
    $('#paymentMode').change(function(){
        if($('#paymentMode').val()){
            $('#btnSave').prop('disabled', true);
            paymentmode=$('#paymentMode').val();
            var params = 'paymentMode=' +paymentmode;
            $.ajax({
            type: 'POST',
            url: getLatestEmployeeIdURL,
            data: params,
            success: function(data) {
              // alert(data);
               $('#employeeId').attr('value','P'+data);
               $('#btnSave').prop('disabled', false);
            }
            });
        
        }
    });
});