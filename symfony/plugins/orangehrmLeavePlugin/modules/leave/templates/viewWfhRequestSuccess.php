<?php use_javascript(plugin_web_path('orangehrmLeavePlugin', 'js/viewCompOffRequestSuccess.js'));?>

<?php if($wfhListPermissions->canRead()){?>
<div id="processing"></div>

<!--this is ajax message place -->
<div id="msgPlace"></div>
<!-- end of ajax message place -->
<?php include_component('core', 'ohrmList', array('requestComments' => $requestComments)); ?>
<input type="hidden" name="hdnMode" value="<?php echo $mode; ?>" />

<!-- comment dialog -->
<div class="modal midsize hide" id="commentDialog">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('WFH Comments'); ?></h3>
  </div>
  <div class="modal-body">
    <p>
    <form action="updateWfhComment" method="post" id="frmCommentSave">
        <input type="hidden" id="leaveId" />
        <input type="hidden" id="leaveOrRequest" />        
        <?php echo $leavecommentForm ?>
        <div id="existingComments">  
            <span><?php echo __('Loading') . '...';?></span>
        </div>
        <?php if ($commentPermissions->canCreate()):?>
        <br class="clear" />
        <br class="clear" />
        <textarea name="leaveComment" id="leaveComment" cols="40" rows="4" class="commentTextArea"></textarea>
        <span id="commentError"></span>
        <?php endif;?>
    </form>        
    </p>
  </div>
  <div class="modal-footer">
    <?php if ($commentPermissions->canCreate()):?>
    <input type="button" class="btn" id="commentSave" value="<?php echo __('Save'); ?>" />
    <?php endif;?>
    <input type="button" class="btn reset" data-dismiss="modal" id="commentCancel" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- end of comment dialog-->
<?php }?>

<script type="text/javascript">
    //<![CDATA[

    var compOffRequestId = <?php echo $wfhRequestId; ?>;
    var leave_status_pending = 'Pending Approval'; // TO DO: Fix, check if compatible with localization
    var ess_mode = '<?php echo ($essMode) ? '1' : '0'; ?>';
    var lang_Required = '<?php echo __(ValidationMessages::REQUIRED);?>';
    var lang_comment_successfully_saved = '<?php echo __(TopLevelMessages::SAVE_SUCCESS); ?>';
    var lang_comment_save_failed = '<?php echo __(TopLevelMessages::SAVE_FAILURE); ?>'; 
    var lang_Processing = '<?php echo __('Processing'); ?>...';
    var lang_Close = '<?php echo __('Close');?>';
    var lang_Date = '<?php echo __('Date');?>';
    var lang_Time = '<?php echo __('Time');?>';
    var lang_Author = '<?php echo __('Author');?>';
    var lang_Comment = '<?php echo __('Comment');?>';
    var lang_Loading = '<?php echo __('Loading');?>...';
    var lang_LengthExceeded = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 255)); ?>';
    var lang_LeaveComments = '<?php echo __('WFH Comments'); ?>';
    var lang_LeaveRequestComments = '<?php echo __('WFH Request Comments'); ?>';
    var lang_selectAction = '<?php echo __("Select Action");?>';
    var lang_Close = '<?php echo __('Close');?>';    
    var getCommentsUrl = '<?php echo url_for('leave/getWfhCommentsAjax'); ?>';
    var commentUpdateUrl = '<?php echo public_path('index.php/leave/updateWfhComment'); ?>';
    var backUrl = '<?php echo url_for($backUrl); ?>';   
    function handleSaveButton() {
    $('#processing').html('');
    $('.messageBalloon_success').remove();
    $('.messageBalloon_warning').remove();
    $(this).attr('disabled', true);
    
    var selectedActions = 0;
    
    $('select[name^="select_leave_action_"]').each(function() {
        var id = $(this).attr('id').replace('select_leave_action_', '');
        if ($(this).val() == '') {
            $('#hdnLeaveRequest_' + id).attr('disabled', true);
        } else {
            selectedActions++;
            $('#hdnLeaveRequest_' + id).val('WF' + $(this).val());
        }

        if ($(this).val() == '') {
            $('#hdnLeave_' + id).attr('disabled', true);
        } else {
            $('#hdnLeave_' + id).val('WF' + $(this).val());
        }
    });

    if (selectedActions > 0) {
        document.frmList_ohrmListComponent.action = "<?php echo url_for('leave/changeWfhStatus?id='.$wfhRequestId);?>"; 
           // document.frmList_ohrmListComponent.submit(); 
//        var action = $('#frmList_ohrmListComponent').attr('action');
//        action = action + '/id/' + compOffRequestId;

       // $('#frmList_ohrmListComponent').attr('action', action);

        $('#helpText').before('<div class="message success">' + lang_Processing + '</div>');

        // check the correct url here
        document.frmList_ohrmListComponent.submit()
       // $('#frmList_ohrmListComponent').submit();
    } else {
        $('#helpText').before('<div class="message warning fadable">' + lang_selectAction + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');
        setTimeout(function(){
            $("div.fadable").fadeOut("slow", function () {
                $("div.fadable").remove();
            });
        }, 2000);
        $(this).attr('disabled', false);      
        return false;
    }
        

}
    //]]>
</script>

