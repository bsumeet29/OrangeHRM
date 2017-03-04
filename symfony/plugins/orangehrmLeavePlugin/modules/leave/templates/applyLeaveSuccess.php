<?php
use_javascripts_for_form($applyLeaveForm);
use_stylesheets_for_form($applyLeaveForm);
use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/assignLeaveSuccess.css'));
//use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/easy-autocomplete.themes.min.css'))
?>
<?php //use_javascript(plugin_web_path('js/jquery/jquery.easy-autocomplete.js')); ?>

<?php include_partial('overlapping_leave', array('overlapLeave' => $overlapLeave, 'workshiftLengthExceeded' => $workshiftLengthExceeded)); ?>

<div class="box" id="apply-leave">
    <div class="head">
        <h1><?php echo __('Apply Leave') ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <?php if ($applyLeaveForm->hasErrors()): ?>
            <?php include_partial('global/form_errors', array('form' => $applyLeaveForm)); ?>
        <?php endif; ?>        
        <?php if (count($leaveTypes) > 1) : ?>           
            <form id="frmLeaveApply" name="frmLeaveApply" method="post" action="">

                <?php include_component('core', 'ohrmPluginPannel', array('location' => 'apply-leave-form-elements')) ?>
                <fieldset>     
                    <?php
                    $requiredMarker = ' <em>*</em>';
                    ?>
                    <?php echo $applyLeaveForm['_csrf_token']; ?>
                    <?php echo $applyLeaveForm['txtEmpID']->render(); ?>

                    <ol>

                        <li>
                            <label for="txtFromDate"><?php echo __('From Date') . $requiredMarker; ?></label>
                            <?php echo $applyLeaveForm['txtFromDate']->render(); ?>
                        </li>
                        <li>
                            <label for="txtToDate"><?php echo __('To Date') . $requiredMarker; ?></label>
                            <?php echo $applyLeaveForm['txtToDate']->render(); ?>
                        </li>
                        <li>
                            <label for="duration"><?php echo __('Duration'); ?></label>
                            <?php echo $applyLeaveForm['duration']->render(); ?>
                        </li>
                        <li>
                            <label for="partialDays"><?php echo __('Partial Days'); ?></label>
                            <?php echo $applyLeaveForm['partialDays']->render(); ?>
                        </li>

                        <li>
                            <label for="firstDuration"><?php echo __('Duration'); ?></label>
                            <?php echo $applyLeaveForm['firstDuration']->render(); ?>
                        </li>

                        <li>
                            <label for="secondDuration"><?php echo __('Duration'); ?></label>
                            <?php echo $applyLeaveForm['secondDuration']->render(); ?>
                        </li>
                        <li>
                            <label for="txtLeaveType"><?php echo __('Leave Type') . $requiredMarker; ?></label>
                            <?php echo $applyLeaveForm['txtLeaveType']->render(); ?>
                        </li>
                        <li>
                            <label for="leaveBalance"><?php echo __('Leave Balance'); ?></label>
                            <?php echo $applyLeaveForm['leaveBalance']->render(); ?>
                        </li>
                        <li>
                            <label for="txtComment"><?php echo __('Reason of leave') . $requiredMarker; ?></label>
                            <?php echo $applyLeaveForm['txtComment']->render(); ?>
                        </li>
                        <li>
                            <label for="txtContact"><?php echo __('Contact Details during absence') . $requiredMarker; ?></label>
                            <?php echo $applyLeaveForm['txtContact']->render(); ?>
                        </li>

                        <!-- <?php for ($i = 0; $i < 6; $i++): ?>
                            <?php if (isset($applyLeaveForm['txtPreviousTask_' . $i]) && isset($applyLeaveForm['txtContactPerson_' . $i])): ?>
                                 
                                    <li>
                                         <li id="li_<?php echo $i . $i; ?>" class="tasklist">
                                        <label for="txtPreviousTask_<?php echo $i; ?>"><?php echo __('Task'); ?></label>
                                <?php echo $applyLeaveForm['txtPreviousTask_' . $i]->render(); ?>
                                        <label for="txtContactPerson_<?php echo $i; ?>"><?php echo __('&nbsp;&nbsp;&nbsp;Contact Person'); ?></label>
                                <?php echo $applyLeaveForm['txtContactPerson_' . $i]->render() . $requiredMarker; ?>
                                        &nbsp;&nbsp;&nbsp;<a id="<?php echo $i . $i; ?>" class="deleteTask"> Delete Task</a>
                                   </li>
                            <?php endif; ?>
                        <?php endfor; ?>-->
                        <li id ="previousTasks"></li>
                        <li id="task">
                            <label for="txtTask"><?php echo __('Task') . $requiredMarker; ?></label>
                            <?php echo $applyLeaveForm['txtTask']->render(); ?>
                            <label for="txtContactPerson"><?php echo __('&nbsp;&nbsp;&nbsp;Contact Person') . $requiredMarker; ?></label>
                            <?php echo $applyLeaveForm['txtContactPerson']->render(); ?>

                        </li>

                        <li id="li_1" class="tasklist">
                            <label for="txtTask1"><?php echo __('Task'); ?></label>
                            <?php echo $applyLeaveForm['txtTask1']->render(); ?>
                            <label for="txtContactPerson1"><?php echo __('&nbsp;&nbsp;&nbsp;Contact Person'); ?></label>
                            <?php echo $applyLeaveForm['txtContactPerson1']->render(); ?>
                            &nbsp;&nbsp;&nbsp;<a id="1" class="deleteTask"> Delete Task</a>

                        </li>
                        <li id="li_2" class="tasklist">
                            <label for="txtTask2"><?php echo __('Task'); ?></label>
                            <?php echo $applyLeaveForm['txtTask2']->render(); ?>
                            <label for="txtContactPerson2"><?php echo __('&nbsp;&nbsp;&nbsp;Contact Person'); ?></label>
                            <?php echo $applyLeaveForm['txtContactPerson2']->render(); ?>
                            &nbsp;&nbsp;&nbsp;<a id="2" class="deleteTask"> Delete Task</a>

                        </li>
                        <li id="li_3" class="tasklist">
                            <label for="txtTask3"><?php echo __('Task'); ?></label>
                            <?php echo $applyLeaveForm['txtTask3']->render(); ?>
                            <label for="txtContactPerson3"><?php echo __('&nbsp;&nbsp;&nbsp;Contact Person'); ?></label>
                            <?php echo $applyLeaveForm['txtContactPerson3']->render(); ?>
                            &nbsp;&nbsp;&nbsp;<a id="3" class="deleteTask"> Delete Task</a>

                        </li>
                        <li id="li_4" class="tasklist">
                            <label for="txtTask4"><?php echo __('Task'); ?></label>
                            <?php echo $applyLeaveForm['txtTask4']->render(); ?>
                            <label for="txtContactPerson4"><?php echo __('&nbsp;&nbsp;&nbsp;Contact Person'); ?></label>
                            <?php echo $applyLeaveForm['txtContactPerson4']->render(); ?>
                            &nbsp;&nbsp;&nbsp;<a id="4" class="deleteTask"> Delete Task</a>

                        </li>
                        <li id="li_5" class="tasklist">
                            <label for="txtTask5"><?php echo __('Task'); ?></label>
                            <?php echo $applyLeaveForm['txtTask5']->render(); ?>
                            <label for="txtContactPerson5"><?php echo __('&nbsp;&nbsp;&nbsp;Contact Person'); ?></label>
                            <?php echo $applyLeaveForm['txtContactPerson5']->render(); ?>
                            &nbsp;&nbsp;&nbsp;<a id="5" class="deleteTask"> Delete Task</a>
                        </li>
                        <li>
                            <a id="addtask" class="glyphicon glyphicon-trash" aria-hidden="true">Add Task</a>
                        </li>

                        <li class="required new">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>                      
                    </ol>            

                    <p>
                        <input type="button" id="applyBtn" value="<?php echo __("Apply") ?>"/>
                    </p>                
                </fieldset>

            </form>
        <?php endif ?>           
    </div> <!-- inner -->

</div> <!-- apply leave -->

<!-- leave balance details HTML: Begins -->
<div class="modal hide" id="balance_details">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo 'OrangeHRM - ' . __('Leave Balance Details'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('As of Date') . ':'; ?> <span id="balance_as_of"></span></p>
        <table border="0" cellspacing="0" cellpadding="0" class="table">
            <tbody>
                <tr class="odd">
                    <td><?php echo __('Entitled'); ?></td>
                    <td id="balance_entitled">0.00</td>
                </tr>
                <tr class="odd" id="container-adjustment">
                    <td><?php echo __('Adjustment'); ?></td>
                    <td id="balance_adjustment">0.00</td>
                </tr>
                <tr class="even">
                    <td><?php echo __('Taken'); ?></td>
                    <td id="balance_taken">0.00</td>
                </tr>
                <tr class="odd">
                    <td><?php echo __('Scheduled'); ?></td>
                    <td id="balance_scheduled">0.00</td>
                </tr>
                <tr class="even">
                    <td><?php echo __('Pending Approval'); ?></td>
                    <td id="balance_pending">0.00</td>
                </tr>      
                <tr class="odd">
                    <td><?php echo __('Conditional Approved'); ?></td>
                    <td id="balance_conditional_Approved">0.00</td>
                </tr> 
            </tbody>
            <tfoot>
                <tr class="total">
                    <td><?php echo __('Balance'); ?></td>
                    <td id="balance_total"></td>
                </tr>
            </tfoot>     
        </table>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="closeButton" value="<?php echo __('Ok'); ?>" />
    </div>
</div>
<!-- leave balance details HTML: Ends -->

<!-- leave balance details HTML: Begins -->
<div class="modal hide" id="multiperiod_balance">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo 'OrangeHRM - ' . __('Leave Balance Details'); ?></h3>
    </div>
    <div class="modal-body">
        <table border="0" cellspacing="0" cellpadding="0" class="table">
            <thead>
                <tr>
                    <th><?php echo __('Leave Period'); ?></th>
                    <th><?php echo __('Initial Balance'); ?></th>
                    <th><?php echo __('Leave Date'); ?></th>
                    <th><?php echo __('Available Balance'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="odd">
                    <td></td>
                </tr>                    
            </tbody>       
        </table>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="closeButton" value="<?php echo __('Ok'); ?>" />
    </div>
</div>
<!-- new tasks modal begins -->
<div class="modal hide" id="leavetasks">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo 'OrangeHRM - ' . __('Leave Tasks Details'); ?></h3>
    </div>
    <div class="modal-body">
        <table border="0" cellspacing="0" cellpadding="0" class="table">
            <thead>
                <tr>
                    <th><?php echo __('Task Name'); ?></th>

                </tr>
            </thead>
            <tbody>
                <tr class="odd">
                    <td></td>
                </tr>                    
            </tbody>       
        </table>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="closeButton" value="<?php echo __('Ok'); ?>" />
    </div>
</div>
<script type="text/javascript">
//<![CDATA[        
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var leaveBalanceUrl = '<?php echo url_for('leave/getLeaveBalanceAjax'); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_details = '<?php echo __("View Details") ?>';
    var lang_BalanceNotSufficient = "<?php echo __("Balance not sufficient"); ?>";
    var lang_Duration = "<?php echo __('Duration'); ?>";
    var lang_StartDay = "<?php echo __('Start Day'); ?>";
    var lang_EndDay = "<?php echo __('End Day'); ?>";
    var leaveTaskUrl = '<?php echo url_for('leave/getPreviousTaskAjax'); ?>';
    var employeeListUrl = '<?php echo url_for('leave/employeeListAjax'); ?>';
    var employeeList;
    $(document).ready(function () {
        for (j = 1; j <= 5; j++) {
            $('#applyleave_txtTask' + j).attr('value', '');
            $('#applyleave_txtContactPerson' + j + '_empId').attr('value', '');
            $('#applyleave_txtContactPerson' + j + '_empName').attr('value', '');
            $('#li_' + j).hide();
        }
        // $('.tasklist').hide();   


        var idvaluemain = 0;
        $('.deleteTask').click(function () {
            idvalue = this.id;
            $('#applyleave_txtTask' + idvalue).attr('value', '');
            $('#applyleave_txtContactPerson' + idvalue + '_empId').attr('value', '');
            $('#applyleave_txtContactPerson' + idvalue + '_empName').attr('value', '');
            $('#li_' + idvalue).hide();
            $('#' + (idvalue - 1)).show();
            idvaluemain--;

        });
        $('#addtask').click(function () {
            if (idvaluemain < 5) {
                idvaluemain++;
                $('#applyleave_txtTask' + idvaluemain).attr('value', '');
                $('#applyleave_txtContactPerson' + idvaluemain + '_empId').attr('value', '');
                $('#applyleave_txtContactPerson' + idvaluemain + '_empName').attr('value', '');
                $('#li_' + idvaluemain).show();

                for (i = 0; i < idvaluemain; i++) {
                    $('#' + i).hide();
                }

            } else {
                idvaluemain = 5;
                alert("Max 6 task are allowed");
            }

        });

        showTimeControls(false, false);


        updateLeaveBalance();

        $('#applyleave_txtFromDate').change(function () {
            fromDateBlur($(this).val());
            updateLeaveBalance();
            updateTasks();
        });

        $('#applyleave_txtToDate').change(function () {
            toDateBlur($(this).val());
            updateLeaveBalance();
            updateTasks();
        });

        $('#applyleave_partialDays').change(function () {
            handlePartialDayChange(true);
        });

        if (trim($("#applyleave_txtFromDate").val()) == displayDateFormat || trim($("#applyleave_txtToDate").val()) == displayDateFormat
                || trim($("#applyleave_txtFromDate").val()) == '' || trim($("#applyleave_txtToDate").val()) == '') {
            showTimeControls(false, false);
        } else if (trim($("#applyleave_txtFromDate").val()) == trim($("#applyleave_txtToDate").val())) {
            showTimeControls(true, false);
        } else {
            showTimeControls(false, true);
        }

        // Bind On change event of time elements
        $('select.timepicker').change(function () {
            fillTotalTime($(this));
        });

        $('#applyleave_txtLeaveType').change(function () {
            updateLeaveBalance();
        });


        function updateLeaveBalance() {
            var leaveType = $('#applyleave_txtLeaveType').val();
            var startDate = $('#applyleave_txtFromDate').val();
            var endDate = $('#applyleave_txtToDate').val();
            $('#applyleave_leaveBalance').text('--');
            $('#leaveBalance_details_link').remove();
//shubham->edit for not allowing same day wfh.
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var currentDate = (day<10 ? '0' : '') + day + '-' + (month<10 ? '0' : '') + month + '-' + d.getFullYear();
            if(leaveType == ""){

            }
            else {
                if (leaveType == "6" && startDate == currentDate) {
                    window.alert("Intimation of WFH must be made to the supervisor & the admin one working day prior.");
                    window.location.reload();   
                }
                $('#applyleave_leaveBalance').text('').addClass('loading_message');
                $.ajax({
                    type: 'GET',
                    url: leaveBalanceUrl,
                    data: '&leaveType=' + leaveType + '&startDate=' + startDate + '&endDate=' + endDate,
                    dataType: 'json',
                    success: function (data) {
                        if (data.multiperiod == true) {

                            var leavePeriods = data.data;
                            var leavePeriodCount = leavePeriods.length;

                            var linkTxt = data.negative ? lang_BalanceNotSufficient : lang_details;
                            var balanceTxt = leavePeriodCount == 1 ? leavePeriods[0].balance.balance.toFixed(2) : '';
                            var linkCss = data.negative ? ' class="error" ' : "";

                            $('#applyleave_leaveBalance').text(balanceTxt)
                                    .append('<a href="#multiperiod_balance" data-toggle="modal" id="leaveBalance_details_link"' + linkCss + '>' +
                                            linkTxt + '</a>');

                            var html = '';
                            var rows = 0;
                            for (var i = 0; i < leavePeriodCount; i++) {
                                var leavePeriod = leavePeriods[i];
                                var days = leavePeriod['days'];
                                var leavePeriodFirstRow = true;

                                for (var leaveDate in days) {
                                    if (days.hasOwnProperty(leaveDate)) {
                                        var leaveDateDetails = days[leaveDate];

                                        rows++;
                                        var css = rows % 2 ? "even" : "odd";

                                        var thisLeavePeriod = leavePeriod['period'];
                                        var leavePeriodTxt = '';
                                        var leavePeriodInitialBalance = '';

                                        if (leavePeriodFirstRow) {
                                            leavePeriodTxt = thisLeavePeriod[0] + ' - ' + thisLeavePeriod[1];
                                            leavePeriodInitialBalance = leavePeriod.balance.balance.toFixed(2);
                                            leavePeriodFirstRow = false;
                                        }

                                        var balanceValue = leaveDateDetails.balance === false ? leaveDateDetails.desc : leaveDateDetails.balance.toFixed(2);

                                        html += '<tr class="' + css + '"><td>' + leavePeriodTxt + '</td><td class="right">' + leavePeriodInitialBalance +
                                                '</td><td>' + leaveDate + '</td><td class="right">' + balanceValue + '</td></tr>';
                                    }
                                }
                                }
                                $('div#multiperiod_balance table.table tbody').html('').append(html);

                        } else {
                            var balance = data.balance;
                            var asAtDate = data.asAtDate;
                            var balanceDays = balance.balance;
                            $('#applyleave_leaveBalance').text(balanceDays.toFixed(2))
                                    .append('<a href="#balance_details" data-toggle="modal" id="leaveBalance_details_link">' +
                                            lang_details + '</a>');

                            $('#balance_as_of').text(asAtDate);
                            $('#balance_entitled').text(Number(balance.entitled).toFixed(2));
                            $('#balance_taken').text(Number(balance.taken).toFixed(2));
                            $('#balance_scheduled').text(Number(balance.scheduled).toFixed(2));
                            $('#balance_conditional_Approved').text(Number(balance.conditional_approved).toFixed(2));
                            $('#balance_pending').text(Number(balance.pending).toFixed(2));
                            $('#balance_adjustment').text(Number(balance.adjustment).toFixed(2));
                            $('#balance_total').text(balanceDays.toFixed(2));

                            if (Number(balance.adjustment) == 0) {
                                $('#container-adjustment').hide();
                            }
                        }
                        $('#applyleave_leaveBalance').removeClass('loading_message');
                    }
                });
            }
        }

        // Fetch and display available leave when leave type is changed
        $('#applyleave_leaveBalance').change(function () {
            updateLeaveBalance();
        });
        /* function getTasks(){
         var leaveType = $('#applyleave_txtLeaveType').val();
         var startDate = $('#applyleave_txtFromDate').val();
         var endDate =  $('#applyleave_txtToDate').val();
         $.ajax({
         type: 'GET',
         url: leaveTaskUrl,
         data: '&leaveType=' + leaveType + '&startDate=' + startDate + '&endDate=' + endDate,
         dataType: 'json',
         });
         success: function(data) {
         alert(data);return false;
         }
         }*/
        //Validation
        $("#frmLeaveApply").validate({
            onfocusout: false,
            rules: {
                'applyleave[txtLeaveType]': {
                    required: true,
                    validateLeaveType: true
                    

                },
                'applyleave[txtFromDate]': {
                    required: true,
                    valid_date: function () {
                        return {
                            required: true,
                            format: datepickerDateFormat,
                            displayFormat: displayDateFormat,
                            validateLeaveType: true,
                                                             
                        }
                    }
                },
                'applyleave[txtToDate]': {
                    required: true,
                    valid_date: function () {
                        return {
                            required: true,
                            format: datepickerDateFormat,
                            displayFormat: displayDateFormat,
                            validateLeaveType: true
                        }
                    },
                    date_range: function () {
                        return {
                            format: datepickerDateFormat,
                            displayFormat: displayDateFormat,
                            fromDate: $("#applyleave_txtFromDate").val()
                        }
                    }
                },
                /* 'applyleave[txtContactPerson]':{
                 isValid:function(){  if($("#applyleave_txtTask").val()!=null){
                 if($("#applyleave_txtContactPerson").val()==null)
                 return false;
                 else
                 return true;
                 }
                 }
                 //}
                 },*/
                'applyleave[txtComment]': {
                    required: true,
                    maxlength: 250},
                'applyleave[txtContact]': {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                'applyleave[txtTask]': {
                    required: true
                },
                'applyleave[txtContactPerson][empName]': {
                    required: true
                },
                'applyleave[duration][time][from]': {required: false, validWorkShift: true, validTotalTime: true, validToTime: true},
                'applyleave[duration][time][to]': {required: false, validTotalTime: true},
                'applyleave[firstDuration][time][from]': {required: false, validWorkShift: true, validTotalTime: true, validToTime: true},
                'applyleave[firstDuration][time][to]': {required: false, validTotalTime: true},
                'applyleave[secondDuration][time][from]': {required: false, validWorkShift: true, validTotalTime: true, validToTime: true},
                'applyleave[secondDuration][time][to]': {required: false, validTotalTime: true},
                'applyleave[duration][duration]': {required: true, validDuration: true}
            },
            messages: {
                'applyleave[txtLeaveType]': {
                    required: "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validateLeaveType: "<?php echo __('This type of leaves should be applied 7 days prior to the leave date.'); ?>"
                },
                'applyleave[txtFromDate]': {
                    required: lang_invalidDate,
                    valid_date: lang_invalidDate
                    

                },
                'applyleave[txtToDate]': {
                    required: lang_invalidDate,
                    valid_date: lang_invalidDate,
                    date_range: lang_dateError
                },
                'applyleave[txtComment]': {
                    required: '<br><br><br><br><br>Please enter the reason of leave.',
                    maxlength: "<?php echo __(ValidationMessages::APPLY_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>"
                },
                'applyleave[txtContact]': {
                    required: 'Please enter your mobile number.',
                    digits: 'Allows numbers only.',
                    minlength: 'Mobile number should be 10 digit only.',
                    maxlength: 'Mobile number should be 10 digit only.'

                },
                'applyleave[duration][time][from]': {
                    validTotalTime: "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift: "<?php echo __('Duration should be less than work shift length'); ?>",
                    validToTime: "<?php echo __('From time should be less than To time'); ?>"
                },
                'applyleave[duration][time][to]': {
                    validTotalTime: "<?php echo __(ValidationMessages::REQUIRED); ?>"
                },
                'applyleave[firstDuration][time][from]': {
                    validTotalTime: "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift: "<?php echo __('Duration should be less than work shift length'); ?>",
                    validToTime: "<?php echo __('From time should be less than To time'); ?>"
                },
                'applyleave[firstDuration][time][to]': {
                    validTotalTime: "<?php echo __(ValidationMessages::REQUIRED); ?>"
                },
                'applyleave[secondDuration][time][from]': {
                    validTotalTime: "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift: "<?php echo __('Duration should be less than work shift length'); ?>",
                    validToTime: "<?php echo __('From time should be less than To time'); ?>"
                },
                'applyleave[secondDuration][time][to]': {
                    validTotalTime: "<?php echo __(ValidationMessages::REQUIRED); ?>"
                },
                'applyleave[duration][duration]': {
                    validDuration: "<?php echo __('Please select duration'); ?>"
                },
                'applyleave[txtTask]': {
                    required: 'Please enter the task.'
                },
                'applyleave[txtContactPerson][empName]': {
                    required: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbspPlease select contact person.',
                    isValid: 'Please enter'
                }
            }
        });

        $.validator.addMethod("validTotalTime", function (value, element) {
            var valid = true;

            if ($(element).is(':visible')) {

                if (value == '') {
                    valid = false;
                }
            }

            return valid;
        });

        $.validator.addMethod("validWorkShift", function (value, element) {

            var valid = true;

            if ($(element).is(':visible')) {
                var fromElement = $(element).parent('span').children('select.timepicker').first();
                var toElement = fromElement.siblings('select.timepicker').first();

                var totalTime = getTotalTime(fromElement.val(), toElement.val());
                var workShift = $('#applyleave_txtEmpWorkShift').val();
                if (parseFloat(totalTime) > parseFloat(workShift)) {
                    valid = false;
                }
            }
            return valid;
        });

        $.validator.addMethod("validDuration", function (value, element) {
            var valid = true;
            if ($(element).val() == 0) {
                valid = false;
            }

            return valid;
        });

        $.validator.addMethod("validToTime", function (value, element) {
            var valid = true;

            if ($(element).is(':visible')) {
                var fromElement = $(element).parent('span').children('select.timepicker').first();
                var toElement = fromElement.siblings('select.timepicker').first();

                var totalTime = getTotalTime(fromElement.val(), toElement.val());
                if (parseFloat(totalTime) <= 0) {
                    valid = false;
                }
            }

            return valid;
        });

        $.validator.addMethod("validateLeaveType", function (value, element) {

            var valid = true;
            var today = new Date();
            var dd = today.getDate() + 7;
            var months = new Array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
            var month = months[today.getMonth()];

            if (dd < 10) {
                dd = '0' + dd;
            }
            var nextWeek = dd + '-' + month + '-' + today.getFullYear();
            if (today.getDate() < 10) {
                var todaysDate = today.getFullYear() + '-' + month + '-0' + today.getDate();
            } else {
                var todaysDate = today.getFullYear() + '-' + month + '-' + today.getDate();
            }

            if ($('#applyleave_txtFromDate').val()) {
                var v = $('#applyleave_leaveBalance').text();
                v = v.substr(0, v.indexOf("."));
                var str1 = $('#applyleave_txtFromDate').val();
                var leaveDate = reverse(str1);
                var nextWeekDate = reverse(nextWeek);
                if (leaveDate > nextWeekDate) {
                    valid = true;
                } else if (leaveDate < nextWeekDate && ($('#applyleave_txtLeaveType').val() === '2' || $('#applyleave_txtLeaveType').val() === '4')) {
                    valid = false;
                }
            }
            return valid;
        });

        //Click Submit button
        $('#applyBtn').click(function () {
            //   alert(JSON.stringify($('#applyleave_txtContactPerson').val(), null, 4));return false;
            if ($('#applyleave_txtFromDate').val() == displayDateFormat) {
                $('#applyleave_txtFromDate').val("");
            }
            if ($('#applyleave_txtToDate').val() == displayDateFormat) {
                $('#applyleave_txtToDate').val("");
            }
            $('#frmLeaveApply').submit();
        });
    });

    function showTimeControls(showOneDay, showMultiDay) {

        var oneDayControlIds = ['applyleave_duration_duration'];

        $.each(oneDayControlIds, function (index, value) {

            if (showOneDay) {
                $('#' + value).parent('li').show();
            } else {
                $('#' + value).parent('li').hide();
            }
        });

        var multiDayControlIds = ['applyleave_partialDays'];


        $.each(multiDayControlIds, function (index, value) {

            if (showMultiDay) {
                $('#' + value).parent('li').show();
            } else {
                $('#' + value).parent('li').hide();
            }
        });

        handlePartialDayChange($('#applyleave_partialDays').is(':visible'));
    }

    function handlePartialDayChange(showMultiDay) {

        var partialDay = $('#applyleave_partialDays').val();
        var startLabel = false;
        var endLabel = false;

        if (!showMultiDay || partialDay === '') {
            $('#applyleave_firstDuration_duration').parent('li').hide();
            $('#applyleave_secondDuration_duration').parent('li').hide();
        } else if (partialDay === 'all' || partialDay === 'start') {
            $('#applyleave_firstDuration_duration').parent('li').show();
            $('#applyleave_secondDuration_duration').parent('li').hide();
            startLabel = partialDay === 'all' ? lang_Duration : lang_StartDay;
        } else if (partialDay === 'end') {
            $('#applyleave_firstDuration_duration').parent('li').hide();
            $('#applyleave_secondDuration_duration').parent('li').show();
            endLabel = lang_EndDay;
        } else if (partialDay === 'start_end') {
            $('#applyleave_firstDuration_duration').parent('li').show();
            $('#applyleave_secondDuration_duration').parent('li').show();
            startLabel = lang_StartDay;
            endLabel = lang_EndDay;
        }

        if (startLabel) {
            $('#applyleave_firstDuration_duration').parent('li').children('label:first-child').text(startLabel);
        }
        if (endLabel) {
            $('#applyleave_secondDuration_duration').parent('li').children('label:first-child').text(endLabel);
        }

    }

    function fillTotalTime(element) {

        var fromElement = element.parent('span').children('select.timepicker').first();
        var toElement = fromElement.siblings('select.timepicker').first();
        var durationElement = fromElement.siblings('input.time_range_duration').first();

        var total = getTotalTime(fromElement.val(), toElement.val());
        if (isNaN(total)) {
            total = '';
        }

        durationElement.val(total);
        fromElement.valid();
        toElement.valid();
    }

    function getTotalTime(from, to) {
        var total = 0;
        var fromTime = from.split(":");
        var fromdate = new Date();
        fromdate.setHours(fromTime[0], fromTime[1]);

        var toTime = to.split(":");
        var todate = new Date();
        todate.setHours(toTime[0], toTime[1]);

        var difference = todate - fromdate;
        var floatDeference = parseFloat(difference / 3600000);
        total = Math.round(floatDeference * Math.pow(10, 2)) / Math.pow(10, 2);

        return total;
    }

    function fromDateBlur(date) {

        var fromDateValue = trim(date);
        if (fromDateValue != displayDateFormat && fromDateValue != "") {
            var singleDayLeaveRequest = false;
            var toDateValue = trim($("#applyleave_txtToDate").val());
            if (validateDate(fromDateValue, datepickerDateFormat)) {
                if (fromDateValue == toDateValue) {
                    singleDayLeaveRequest = true;
                }

                if (!validateDate(toDateValue, datepickerDateFormat)) {
                    $('#applyleave_txtToDate').val(fromDateValue);
                    singleDayLeaveRequest = true;
                }
            }
            showTimeControls(singleDayLeaveRequest, !singleDayLeaveRequest);
        } else {
            showTimeControls(false, false);
        }


    }

    function toDateBlur(date) {
        var singleDayLeaveRequest = false;
        var toDateValue = trim(date);
        if (toDateValue != displayDateFormat && toDateValue != "") {
            var fromDateValue = trim($("#applyleave_txtFromDate").val());

            if (validateDate(fromDateValue, datepickerDateFormat) && validateDate(toDateValue, datepickerDateFormat)) {
                singleDayLeaveRequest = (fromDateValue == toDateValue);
                showTimeControls(singleDayLeaveRequest, !singleDayLeaveRequest);
            } else {
                showTimeControls(false, false);
            }
        } else {
            showTimeControls(false, false);
        }
    }
    function reverse(date) {
        date = date.split('-');
        date = date[2] + '-' + date[1] + '-' + date[0];
        return new Date(date);
    }
    function updateTasks() {
        var startDate = $('#applyleave_txtFromDate').val();
        var endDate = $('#applyleave_txtToDate').val();

        $.ajax({
            type: 'GET',
            url: leaveTaskUrl,
            data: '&startDate=' + startDate + '&endDate=' + endDate,
            dataType: 'json',
            success: function (data) {
                if (data != null) {
                    $.ajax({
                        type: 'GET',
                        url: '/hrm/symfony/web/index.php/leave/employeeListAjax',
                        datatype: 'json',
                        success: function (data1) {
                            //alert(JSON.stringify(data1, null, 4));
                            employeeList = data1;
                            var i = 0;
                            $("#previousTasks").empty();
                            var Task = data.taskName;
                            for (i = 0; i < data.count; i++) {
                                $(function () {

// append input control at start of form

                                    $(" <li ><label>Task</label><input type='text' name = applyleave[txtPreviousTask_" + i + "]id = txtPreviousTask_" + i + " value='" + data['taskName'][i] + "' ></input><label>&nbsp;&nbsp;&nbsp;Contact Person</label><div class = my-class><div for = applyleave_txtContactPerson_" + i + "_empName class = ac-results><input type='text' name = applyleave[txtContactPerson_" + i + "][empName] value id = applyleave_txtContactPerson_" + i + "_empName class = 'ac-input' autocomplete = 'off' style = width:213px;height:12px; ></input></div></div ><input type = 'hidden' name = applyleave[txtContactPerson_" + i + "][empId] id = applyleave_txtContactPerson_" + i + "_empId value ></input> </li>")
                                            .prependTo("#previousTasks");

                                    //var applyleave_txtContactPerson_00 = data1;
                                    //console.log(data1);
                                    //var employees_%s = %s;
                                    var nameField = $("#applyleave_txtContactPerson_" + i + "_empName");
                                    var idStoreField = $("#applyleave_txtContactPerson_" + i + "_empId");
                                    var typeHint = 'Type for hints...';
                                    var hintClass = 'inputFormatHint';
                                    var loadingMethod = '';
                                    var loadingHint = 'Loading';
                                    if (idStoreField.val() != '') {
                                        idStoreField.data('item.name', nameField.val());
                                    }

                                    nameField.data('typeHint', typeHint);
                                    nameField.data('loadingHint', loadingHint);

                                    nameField.one('focus', function () {
                                        if ($(this).hasClass(hintClass)) {
                                            $(this).val("");
                                            $(this).removeClass(hintClass);
                                        }

                                    });
                                    var options = {
                                        url: function (phrase) {
                                            return '/hrm/symfony/web/index.php/leave/employeeListAjax?phrase=' + phrase + '&format=json';
                                        },
                                        getValue: "name",
                                        placeholder: "Type for hints...",
                                        list: {
                                            onSelectItemEvent: function () {
                                                var value = $(nameField).getSelectedItemData().id;
                                                $(idStoreField).val(value).trigger("change");
                                            },
                                            match: {
                                                enabled: true
                                            }
                                        },
                                        // theme : "square",
                                    };
                                    $(nameField).easyAutocomplete(options);
                                    /* if( loadingMethod != 'ajax'){
                                     if (nameField.val() == '' || nameField.val() == typeHint) {
                                     nameField.val(typeHint).addClass(hintClass);
                                     }
                                     nameField.autocomplete(applyleave_txtContactPerson_00, {
                                     formatItem: function(item) {
                                     return $('<div/>').text(item.name).html();
                                     },
                                     formatResult: function(item) {
                                     return item.name;
                                     }
                                     ,matchContains:true
                                     }).result(function(event, item) {
                                     idStoreField.val(item.id);
                                     idStoreField.data('item.name', item.name);
                                     }
                                     );
                                     /*   $(function(){
                                     // alert(applyleave_txtContactPerson_00);
                                     nameField.autocomplete({
                                     source : applyleave_txtContactPerson_00  
                                     });
                                     
                                     });*/

                                    /* }else{
                                     var value = nameField.val().trim();
                                     nameField.val(loadingHint).addClass('ac_loading');
                                     $.ajax({
                                     url: "/perennial_hrm/symfony/web/index.php/pim/getEmployeeListAjax",
                                     data: '',
                                     dataType: 'json',
                                     success: function(employeeList){ 
                                     nameField.autocomplete(employeeList, {
                                     formatItem: function(item) {
                                     return $('<div/>').text(item.name).html();
                                     },
                                     formatResult: function(item) {
                                     return item.name;
                                     }
                                     
                                     ,matchContains:true
                                     }).result(function(event, item) {
                                     idStoreField.val(item.id);
                                     idStoreField.data('item.name', item.name);
                                     }
                                     
                                     );
                                     nameField.removeClass('ac_loading'); 
                                     
                                     if(value==''){
                                     nameField.val(typeHint).addClass(hintClass);
                                     } else {
                                     nameField.val(value).addClass();
                                     }
                                     }
                                     });
                                     }*/

                                    // End of $(document).ready





                                });
                            }
                            $("</ div>").appendTo("#tasks");
                        }
                    })
                }
                //alert(JSON.stringify(employeeList, null, 4));
                //alert(employeeList);


            }
        });
    }

    function getEmployeeListAsJson() {
        $.ajax({
            type: 'GET',
            url: employeeListUrl,
            datatype: 'json',
            success: function (data) {
                // alert(data);
                console.log(data);
                return(data);
            }
        })
    }
//]]>
</script>
