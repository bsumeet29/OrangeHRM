<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>

<?php $firstDate = isset($form['employee']) ? 3: 2;?>
<style type="text/css">
    form#search_form li:nth-child(<?php echo $firstDate;?>) {
        width: auto;
        margin-right: 10px;
    }
    
    
</style>

<?php

use_javascripts_for_form($form);
use_stylesheets_for_form($form);

?>

<?php if ($form->hasErrors()): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
    </div>
<?php endif; ?>

<div class="box searchForm toggableForm" id="leave-entitlementsSearch">
    <div class="head">
        <h1><?php echo __($title);?></h1>
    </div>
    <div class="inner">
        <?php 
        if (!$showResultTable) {
            include_partial('global/flash_messages'); 
        }
        ?>
        <form id="search_form" name="frmLeaveEntitlementSearch" method="post" action="">

            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                </ol>
                
                <p>
                    <input type="button" id="searchBtn" value="<?php echo __("Search") ?>" name="_search" />
                </p>                
            </fieldset>
            
        </form>
        
    </div> <!-- inner -->
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div> <!-- employee-information -->

<?php if ($showResultTable) { ?>
    <?php include_component('core', 'ohrmList'); ?>
<?php } ?>


<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('PerennialHRM - Confirmation Required'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->
<!-- New comp-off extension modal begin -->
<div class="modal hide" id="extensionDialog">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('Comp-off extension Request'); ?></h3>
  </div>
  <div class="modal-body">
    <p>
    <form action="saveExtensionRequest" method="post" id="frmRequestSave">
        <input type="hidden" id="leaveId"  value=/>
        <input type="hidden" id="leaveOrRequest" />        
       
       <!-- <div id="existingComments">  
            <span><?php echo __('Loading') . '...';?></span>
        </div>-->
  <!--      <?php //if ($commentPermissions->canCreate()):?>
        <br class="clear" />
        <br class="clear" />
       <!-- <textarea name="leaveComment" id="leaveComment" cols="40" rows="4" class="commentTextArea"></textarea>-->
        <div id ="leaveConditions">
        <?php echo $extensionForm ;?>
        </div>
        <br class="clear" />
        <span id="conditionError" style="padding-left: 2px;" class="validation-error"></span>
        <?php //endif;?>
    </form>        
    </p>
  </div>
  <div class="modal-footer">
    <?php //if ($commentPermissions->canCreate()):?>
    <input type="button" class="btn" id="extensionSave" value="<?php echo __('Save'); ?>" />
    <?php //endif;?>
    <input type="button" class="btn reset" data-dismiss="modal" id="commentCancel" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!--compOff extension modal ends-->
<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var extension_update_url = '<?php echo public_path('index.php/leave/saveExtensionRequest');?>';
    var lang_comment_save_failed = '<?php echo __("Failed to save request");?>';
    var lang_comment_successfully_saved = '<?php echo __("Request saved successfully");?>';
    var EntitlementId;
    var path = '<?php echo __("/hrm/symfony/web/index.php/leave/viewMyLeaveEntitlements/reset/1");?>';
    var lang_Close = '<?php echo __('Close');?>'
    var lang_Required = '<?php echo __("Required field")?>';
        $(document).ready(function(){
    $("#resultTable tbody tr").each(function(){
        var curRow = $(this);
        var td = curRow.find('td:first-child');
       td.on('hover',function() {
         if($(this).text()=='Compoff'){  
        $(this).css('cursor','pointer').attr('title', 'Click here for extension request');
         }
    });
        td.on('click', function(){
            if(path ===window.location.pathname){
            var leaveEntitlementId = $(this).find('input').val();
            EntitlementId = leaveEntitlementId;
            if($(this).text()=='Compoff'){
           $('.toolsHeader').show();
     $('#extensionDialog').modal();
            };
        };
        
    });
    });
        });
        $('#extensionSave').click(function(){
          $('#conditionError').html('').removeClass('validation-error');
        var rawComment = $('#comments').val().trim();
        if(rawComment.length > 250) {
            message: 'length exceeded';
            $('#commentError').html(lang_length_exceeded_error).addClass('validation-error');
            return;
        } else if (rawComment.length == 0) {
            $('#commentError').html(lang_Required).addClass('validation-error');
            return;                                
        }
        var extensionDate = $('#leave_txtExtensionDate').val().trim();
         var commentLabel = $('<div/>').text(rawComment).html();
         //var commentLabel = trimComment(comment);
        var data = {
            entitlementId:EntitlementId,
            extensionComment: rawComment,
            extensionDate:extensionDate,
           
        }
        $.ajax({
            type: 'POST',
            url: extension_update_url,
            data: data,
            success: function(data) {

                $('div.message').remove();
                if(data != 0) {
                    var id = $('#leaveId').val();
                    $('#commentContainer-' + id).html(commentLabel);                        
                    $('#noActionsSelectedWarning').remove();

                    $('#helpText').before('<div class="message success fadable">' + lang_comment_successfully_saved + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');
                } else {
                    $('#helpText').before('<div class="message warning fadable">' + lang_comment_save_failed + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');                        
                }
                setTimeout(function(){
                    $("div.fadable").fadeOut("slow", function () {
                        $("div.fadable").remove();
                    });
                }, 2000);

            }
        });

        $("#extensionDialog").modal('hide');
        return;

    });
                  $('#frmRquestSave').validate({
        rules: {
            'compoffExtension[txtComments]' :{
                required:true
            },
            'compoffExtension[txtExtensionDate]':{
                 required: true
            }
        },
        messages: {
        'compoffExtension[txtComments]' :{
          required: 'please select date.'
        },
        'compoffExtension[txtExtensionDate]':{
          required: 'Please enter the reason.'
        }
        }
        });
       // })
    $(document).ready(function() {        
        
        $("#searchBtn").click(function() {
            $('#search_form').submit();
        });
        $('#btnAdd').click(function() {
            location.href = "<?php echo url_for('leave/addLeaveEntitlement') ?>?savedsearch=1";
        });        
       
        $('#btnDelete').attr('disabled','disabled');
        $("#ohrmList_chkSelectAll").click(function() {
            if($(":checkbox").length == 1) {
                $('#btnDelete').attr('disabled','disabled');
            }
            else {
                if($("#ohrmList_chkSelectAll").is(':checked')) {
                    $('#btnDelete').removeAttr('disabled');
                } else {
                    $('#btnDelete').attr('disabled','disabled');
                }
            }
        });
        
        $(':checkbox[name*="chkSelectRow[]"]').click(function() {
            if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
            }
        });
        
        /* Delete confirmation controls: Begin */
        $('#dialogDeleteBtn').click(function() {
            document.frmList_ohrmListComponent.submit();
        });
        /* Delete confirmation controls: End */
        
        $('#search_form').validate({
                rules: {
                    'entitlements[employee][empName]': {
                        required: true,
                        no_default_value: function(element) {

                            return {
                                defaults: $(element).data('typeHint')
                            }
                        }
                    },
                    'entitlements[date_from]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,                                
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        }
                    },
                    'entitlements[date_to]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        },
                        date_range: function() {
                            return {
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat,
                                fromDate:$("#date_from").val()
                            }
                        }
                    }
                },
                messages: {
                    'entitlements[employee][empName]':{
                        required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                        no_default_value:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                    },
                    'entitlements[date_from]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate
                    },
                    'entitlements[date_to]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate ,
                        date_range: lang_dateError
                    }
            }

        });
     
        
    });
    

</script>

<style type="text/css">
    #comments{
        margin-left: 115px;
    }
</style>
