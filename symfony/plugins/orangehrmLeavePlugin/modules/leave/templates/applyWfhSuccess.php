<?php
use_javascripts_for_form($form);
use_stylesheets_for_form($form);
use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/assignLeaveSuccess.css'));
?>

<?php include_partial('overlapping_wfh', array('overlapWfh' => $overlapWfh, 'workshiftLengthExceeded' => $workshiftLengthExceeded));?>

<div class="box" id="assign-leave">
    <div class="head">
        <h1><?php echo __('Apply WFH') ?></h1>
    </div>
    <div class="inner"id="applyWfh">
        <?php include_partial('global/flash_messages'); ?>
        <?php if ($form->hasErrors()): ?>
                <?php include_partial('global/form_errors', array('form' => $form)); ?>
        <?php endif; ?>        
       
        <form id="frmLeaveApply" name="frmLeaveApply" method="post" action="">
            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                    <li class="end">
                        <a id="addtask" class="glyphicon glyphicon-trash" aria-hidden="true">Add Task</a>
                     </li>
                    <li class="required new">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>    
                    <li class="cloneObject">
                       <label class="labeltag" >Task</label>
                       <input type="text" name="" class="inputtag" >
                       &nbsp;&nbsp;&nbsp;<a class="deletetask"> Delete Task</a>
                    </li>
                    
                </ol>                            
                <p>
                    <input type="submit" id="assignBtn" value="<?php echo __("Apply") ?>"/>
                </p>                
            </fieldset>            
        </form>
      
    </div> <!-- inner -->
    
</div> <!-- assign leave -->


<?php include_component('core', 'ohrmPluginPannel', array('location' => 'assign-leave-javascript'))?>
<!-- leave balance details HTML: Ends -->

<?php

    $dateFormat = get_datepicker_date_format($sf_user->getDateFormat());
    $displayDateFormat = str_replace('yy', 'yyyy', $dateFormat);
?>

<script type="text/javascript">
//<![CDATA[    
  
    var datepickerDateFormat = '<?php echo $dateFormat; ?>';
    var displayDateFormat = '<?php echo $displayDateFormat; ?>';
    var leaveBalanceUrl = '<?php echo url_for('leave/getLeaveBalanceAjax'); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => $displayDateFormat)) ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_details = '<?php echo __("View Details") ?>';
    var lang_Required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_CommentLengthExceeded = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_FromTimeLessThanToTime = "<?php echo __('From time should be less than To time'); ?>";
    var lang_DurationShouldBeLessThanWorkshift = "<?php echo __('Duration should be less than work shift length'); ?>";
    var lang_validEmployee = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_BalanceNotSufficient = "<?php echo __("Balance not sufficient");?>";
    var lang_Duration = "<?php echo __('Duration');?>";
    var lang_StartDay = "<?php echo __('Start Day');?>";
    var lang_EndDay = "<?php echo __('End Day');?>";
    $('#assignleave_txtTask').attr('name','task[]');
    var count=0;
    
//]]>    
</script>    
    