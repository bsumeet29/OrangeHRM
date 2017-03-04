$(document).ready(function () {


    //form validation
    $("#frmEmpContactDetails").validate({
        rules: {
            'contact[emp_hm_telephone]': {phone: true},
            'contact[emp_mobile]': {
                // phone: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            'contact[emp_work_telephone]': {phone: true},
            'contact[emp_work_email]': {
                email: true,
                uniqueWorkEmail: true,
                onkeyup: 'if_invalid'
            },
            'contact[emp_oth_email]': {
                email: true,
                uniqueOtherEmail: true,
                onkeyup: 'if_invalid'
            },
            'contact[city]': {
                required: false,
                regx: /^[A-Za-z_ ]{0,20}$/
            },
            'contact[province]': {
                required: false,
                regx: /^[A-Za-z_ ]{0,20}$/
            },
            'contact[permanent_city]': {
                required: false,
                regx: /^[A-Za-z_ ]{0,20}$/
            },
            'contact[permanent_province]': {
                required: false,
                regx: /^[A-Za-z_ ]{0,20}$/
            }

        },
        messages: {
            'contact[emp_hm_telephone]': {phone: invalidHomePhoneNumber},
            'contact[emp_mobile]': {
                digits: 'Allows numbers only.',
                minlength: 'Mobile number should be 10 digit only.',
                maxlength: 'Mobile number should be 10 digit only.'

            },
            'contact[emp_work_telephone]': {phone: invalidWorkPhoneNumber},
            'contact[emp_work_email]': {
                email: incorrectWorkEmail,
                uniqueWorkEmail: lang_emailExistmsg
            },
            'contact[emp_oth_email]': {
                email: incorrectOtherEmail,
                uniqueOtherEmail: lang_emailExistmsg
            },
            'contact[city]': {
                regx: "Please enter only characters."
            },
            'contact[province]': {
                regx: "Please enter only characters."
            },
            'contact[permanent_city]': {
                regx: "Please enter only characters."
            },
            'contact[permanent_province]': {
                regx: "Please enter only characters."
            }
        }
    });
    $.validator.addMethod("regx", function (value, element, regexpr) {
        return regexpr.test(value);
    }, "Please enter a valid first name.");
    $.validator.addMethod("phone", function (value, element) {
        return (checkPhone(element));
    });

    $.validator.addMethod("uniqueWorkEmail", function (value, element, params) {
        var temp = true;
        var i;
        var currentEmp;
        var empNo = parseInt(empNumber, 10);
        var emailCount = emailList.length;
        for (var j = 0; j < emailCount; j++) {
            if (empNo == emailList[j].empNo) {
                currentEmp = j;
            }
        }

        workEmail = $.trim($('#contact_emp_work_email').val()).toLowerCase();
        otherEmail = $.trim($('#contact_emp_oth_email').val()).toLowerCase();
        for (i = 0; i < emailCount; i++) {
            if (workEmail != '') {
                if (emailList[i].workEmail) {
                    arrayName1 = emailList[i].workEmail.toLowerCase();
                    if (workEmail == arrayName1) {
                        temp = false
                        break;
                    }
                }
                if (emailList[i].othEmail) {
                    arrayName2 = emailList[i].othEmail.toLowerCase();
                    if (workEmail == arrayName2) {
                        temp = false
                        break;
                    }
                }
                if (workEmail == otherEmail) {
                    temp = false
                    break;
                }
            }
        }
        if (currentEmp != null) {
            if (emailList[currentEmp].workEmail != null) {
                if (workEmail == emailList[currentEmp].workEmail.toLowerCase()) {
                    temp = true;
                }
            }
        }
        return temp;
    });
    $.validator.addMethod("uniqueOtherEmail", function (value, element, params) {
        var temp = true;
        var i;
        var currentEmp;
        var empNo = parseInt(empNumber, 10);
        var emailCount = emailList.length;
        for (var j = 0; j < emailCount; j++) {
            if (empNo == emailList[j].empNo) {
                currentEmp = j;
            }
        }
        otherEmail = $.trim($('#contact_emp_oth_email').val()).toLowerCase();
        workEmail = $.trim($('#contact_emp_work_email').val()).toLowerCase();
        for (i = 0; i < emailCount; i++) {
            if (otherEmail != '') {
                if (emailList[i].workEmail) {
                    arrayName1 = emailList[i].workEmail.toLowerCase();
                    if (otherEmail == arrayName1) {
                        temp = false
                        break;
                    }
                }
                if (emailList[i].othEmail) {
                    arrayName2 = emailList[i].othEmail.toLowerCase();
                    if (otherEmail == arrayName2) {
                        temp = false
                        break;
                    }
                }
                if (workEmail == otherEmail) {
                    temp = false
                    break;
                }

            }
        }
        if (currentEmp != null) {
            if (emailList[currentEmp].othEmail != null) {
                if (otherEmail == emailList[currentEmp].othEmail.toLowerCase()) {
                    temp = true;
                }
            }
        }
        return temp;
    });

    //on form loading
    $("form#frmEmpContactDetails .formInputText").attr("disabled", "disabled");
    setCountryState();

    //on check of check box

    $('#contact_checkbox').click(function () {
        if ($('#contact_checkbox').is(':checked')) {
            $('#contact_permanent_street1').attr('value', $('#contact_street1').val());
            $('#contact_permanent_street2').attr('value', $('#contact_street2').val());
            $('#contact_permanent_city').attr('value', $('#contact_city').val());
            $('#contact_permanent_province').attr('value', $('#contact_province').val());
            $('#contact_permanent_emp_zipcode').attr('value', $('#contact_emp_zipcode').val());
            $('#contact_permanent_country').attr('value', $('#contact_country').val());
            //apply readonly for element when check box check
            $('#contact_permanent_street1').attr("disabled", "disabled");
            $('#contact_permanent_street2').attr("disabled", "disabled");
            $('#contact_permanent_city').attr("disabled", "disabled");
            $('#contact_permanent_province').attr("disabled", "disabled");
            $('#contact_permanent_emp_zipcode').attr("disabled", "disabled");
            $('#contact_permanent_country').attr("disabled", "disabled");
        } else {
            $('#contact_permanent_street1').attr('value', '');
            $('#contact_permanent_street2').attr('value', '');
            $('#contact_permanent_city').attr('value', '');
            $('#contact_permanent_province').attr('value', '');
            $('#contact_permanent_emp_zipcode').attr('value', '');
            $('#contact_permanent_country').attr('value', '0');
            //remove readonly for element when check box is unchecked
            $('#contact_permanent_street1').removeAttr('disabled');
            $('#contact_permanent_street2').removeAttr('disabled');
            $('#contact_permanent_city').removeAttr('disabled');
            $('#contact_permanent_province').removeAttr('disabled');
            $('#contact_permanent_emp_zipcode').removeAttr('disabled');
            $('#contact_permanent_country').removeAttr('disabled');
        }
    });
    $("#frmEmpContactDetails").submit(function () {
        if ($("#frmEmpContactDetails").valid()) {
            if ($('#contact_checkbox').is(':checked')) {
                $('#contact_permanent_street1').removeAttr('disabled');
                $('#contact_permanent_street2').removeAttr('disabled');
                $('#contact_permanent_city').removeAttr('disabled');
                $('#contact_permanent_province').removeAttr('disabled');
                $('#contact_permanent_emp_zipcode').removeAttr('disabled');
                $('#contact_permanent_country').removeAttr('disabled');

            }
            $('#contact_emp_work_email').removeAttr('disabled');
        }
    });
    $("#btnSave").click(function () {
        //if user clicks on Edit make all fields editable
        if ($("#btnSave").attr('value') == edit) {
            $(".formInputText").removeAttr("disabled");
            if ($('#contact_checkbox').is(':checked')) {
                $('#contact_permanent_street1').attr("disabled", "disabled");
                $('#contact_permanent_street2').attr("disabled", "disabled");
                $('#contact_permanent_city').attr("disabled", "disabled");
                $('#contact_permanent_province').attr("disabled", "disabled");
                $('#contact_permanent_emp_zipcode').attr("disabled", "disabled");
                $('#contact_permanent_country').attr("disabled", "disabled");
            } else {
                $('#contact_permanent_street1').removeAttr('disabled');
                $('#contact_permanent_street2').removeAttr('disabled');
                $('#contact_permanent_city').removeAttr('disabled');
                $('#contact_permanent_province').removeAttr('disabled');
                $('#contact_permanent_emp_zipcode').removeAttr('disabled');
                $('#contact_permanent_country').removeAttr('disabled');
            }
            $('#contact_emp_work_email').attr("disabled", "disabled");
            $("#btnSave").attr('value', save);
            return;
        }

        if ($("#btnSave").attr('value') == save) {
            $("#frmEmpContactDetails").submit();
        }
    });

    //on changing of country
    $("#contact_country").change(function () {
        setCountryState();
    });

    function setCountryState() {
        var hide = "display:none;";
        var show = "display:block;";

        $("#contact_state").hide();
        $("#contact_province").show();

        if ($("#contact_country").attr('value') == 'US') {
            $("#contact_state").show();
            $("#contact_province").hide();
        }
    }

});