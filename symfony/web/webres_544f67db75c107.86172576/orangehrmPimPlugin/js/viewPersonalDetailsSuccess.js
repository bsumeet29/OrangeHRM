$(document).ready(function() {
// $('.anniversary').hide();
    if($('#personal_cmbMarital').val() == 'Married'){
         $('.anniversary').show();
    }else{
        $('.anniversary').hide(); 
    }
    
    $.validator.addMethod("dateRange", function() {
    var today = new Date();
    var event_date = new Date( $('#personal_txtAnniversaryDate').val() );
    if( event_date <= today ){ return true;}else{return false;}
    }, "Anniversary Date date less than or equal to todays date.");
    $.validator.addMethod("regx", function(value, element, regexpr) {          
            return regexpr.test(value);
        }, "Please enter a valid first name.");
    //form validation
    $("#frmEmpPersonalDetails").validate({
        rules: {
            'personal[txtEmpFirstName]': {
                required: true,
                 maxlength: 20,
                 regx:/^[A-Za-z_ ]{0,20}$/
            },
            'personal[txtEmpLastName]': { 
                 required: true,
                 maxlength: 20,
                 regx:/^[A-Za-z_ ]{0,20}$/
            },
            'personal[txtMothersName]':{
                 required: false,
                 regx:/^[A-Za-z_ ]{0,20}$/
            },
            'personal[txtEmpMiddleName]':{
                 required: false,
                 regx:/^[A-Za-z_ ]{0,20}$/
            },
            'personal[DOB]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat} } },
            'personal[txtLicExpDate]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat} } },
            'personal[txtAnniversaryDate]':{
//                dateRange:true,
                valid_date: function(){
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    };
                }
            }
        },
        messages: {
            'personal[txtEmpFirstName]': { 
                required: lang_firstNameRequired,
                regx:"Please enter only characters."       
                 },
            'personal[txtEmpLastName]': {
                required: lang_lastNameRequired,
                regx:"Please enter only characters." 
                    },
            'personal[txtMothersName]':{
                   regx:"Please enter only characters."
            },
            'personal[txtEmpMiddleName]':{
                  regx:"Please enter only characters."
            },
            'personal[DOB]': { valid_date: lang_invalidDate },
            'personal[txtLicExpDate]': { valid_date: lang_invalidDate },
            'personal[txtAnniversaryDate]':{valid_date: lang_invalidDate}
        }
    });

    $(".editable").each(function(){
        $(this).attr("disabled", "disabled");
    });
    
    // Disable calendar elements
    $(".editable.calendar").datepicker('disable');
    
    $("#frmEmpPersonalDetails").submit(function(){
        if ($("#frmEmpPersonalDetails").valid()) {
           $('#personal_txtEmployeeId').removeAttr('disabled'); 
        }
          
     });
    
    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            
            $("#pdMainContainer .editable").each(function(){
                $(this).removeAttr("disabled");
            });            
            
            // Enable calendar elements that are not in readOnlyFields array
            $(".editable.calendar").each(function() {
                var fieldId = $(this).attr('id');
                
                if (fieldId.indexOf('personal_') == 0) {
                    var idWithoutPrefix = fieldId.slice(9);
                    if (-1 == jQuery.inArray(idWithoutPrefix, readOnlyFields)) {
                        $(this).datepicker('enable');
                    }
                }
            });
            
            
            // handle read only fields                
            for (var j = 0; j < readOnlyFields.length; j++) {
                var fieldId = '#personal_' + readOnlyFields[j];
                var field = $(fieldId);
                var fieldName = 'personal['+ readOnlyFields[j]+']';
                
                $('input[name="' + fieldName + '"]').attr('disabled', 'disabled');
                field.attr('disabled', 'disabled');
            }
            $('#personal_txtEmployeeId').attr("disabled", "disabled");
            $("#btnSave").attr('value', save);
            return;
        }

        if($("#btnSave").attr('value') == save) {
            if ($("#frmEmpPersonalDetails").valid()) {
                $("#btnSave").val(lang_processing);
            }
            $("#frmEmpPersonalDetails").submit();
        }
    });
    $('#personal_cmbMarital').change(function(){
        
       if($('#personal_cmbMarital').val() == 'Married'){
            $('.anniversary').show();
        }else{
            $('#personal_txtAnniversaryDate').attr('value','dd-mm-yyyy');
            $('.anniversary').hide(); 
        }
    });
    
    
    });
