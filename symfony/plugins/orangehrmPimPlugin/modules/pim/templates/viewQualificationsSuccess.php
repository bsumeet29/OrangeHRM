<?php use_javascripts_for_form($workExperienceForm) ?>
<?php use_stylesheets_for_form($workExperienceForm) ?>
<?php use_javascripts_for_form($educationForm) ?>
<?php use_stylesheets_for_form($educationForm) ?>
<?php use_javascripts_for_form($skillForm) ?>
<?php use_stylesheets_for_form($skillForm) ?>
<?php use_javascripts_for_form($languageForm) ?>
<?php use_stylesheets_for_form($languageForm) ?>
<?php use_javascripts_for_form($licenseForm) ?>
<?php use_stylesheets_for_form($licenseForm) ?>

<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/viewQualificationsSuccess')); ?>

<?php
$haveWorkExperience = count($workExperienceForm->workExperiences)>0;
?>

<div class="box pimPane">

    <?php 
        $form = $workExperienceForm;
        echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));
    ?>
<!--    
    <div class="head">
        <h1><?php echo __('Qualifications'); ?></h1>
    </div>
    -->
    
    <a name="workexperience"></a>
    <!-- this is work experience section -->
    <?php if ($workExperiencePermissions->canCreate() || ($haveWorkExperience && $workExperiencePermissions->canUpdate())) { ?>
        <div id="changeWorkExperience">
            <div class="head">
                <h1 id="headChangeWorkExperience"><?php echo __('Add Work Experience'); ?></h1>
            </div>
                
            <div class="inner">
                <form id="frmWorkExperience" action="<?php echo url_for('pim/saveDeleteWorkExperience?empNumber=' . 
                        $empNumber . "&option=save"); ?>" method="post">
                    <?php echo $workExperienceForm['_csrf_token']; ?>
                    <?php echo $workExperienceForm['emp_number']->render(); ?>
                    <?php echo $workExperienceForm["seqno"]->render(); ?>

                    <fieldset>
                        <ol>
                            <li>
                                <?php echo $workExperienceForm['employer']->renderLabel(__('Company Name') . ' <em>*</em>'); ?>
                                <?php echo $workExperienceForm['employer']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                            </li>
                            <li>
                                <?php echo $workExperienceForm['jobtitle']->renderLabel(__('Job Title') . ' <em>*</em>'); ?>
                                <?php echo $workExperienceForm['jobtitle']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                            </li>
                            <li>
                                <?php echo $workExperienceForm['from_date']->renderLabel(__('From')); ?>
                                <?php echo $workExperienceForm['from_date']->render(array("class" => "formInputText","readonly"=>"readonly")); ?>
                            </li>
                            <li>
                                <?php echo $workExperienceForm['to_date']->renderLabel(__('To')); ?>
                                <?php echo $workExperienceForm['to_date']->render(array("class" => "formInputText","readonly"=>"readonly")); ?>
                            </li>
                            <li>
                                <?php echo $workExperienceForm['last_appraisal_date']->renderLabel(__('Last Appraisal Date')); ?>
                                <?php echo $workExperienceForm['last_appraisal_date']->render(array("class" => "formInputText","readonly"=>"readonly")); ?>
                            </li>
                            <li>
                                <?php echo $workExperienceForm['previous_ctc']->renderLabel(__('CTC (previous company)')); ?>
                                <?php echo $workExperienceForm['previous_ctc']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
                            </li>
                            <li>
                                <?php echo $workExperienceForm['relevant_exp']->renderLabel(__('Relevant Experience').'<br />(in years)'); ?>
                                <?php echo $workExperienceForm['relevant_exp']->render(array("class" => "formInputText", "maxlength" => 5)); 
                                        ?>
                            </li>
                            <li>
                                <?php echo $workExperienceForm['total_exp']->renderLabel(__('Total Experience').'<br />(in years)'); ?>
                                <?php echo $workExperienceForm['total_exp']->render(array("class" => "formInputText", "maxlength" => 5)); ?>
                            </li>
                            
                            
<!--                            <li class="largeTextBox">
                                <?php echo $workExperienceForm['comments']->renderLabel(__('Comment')); ?>
                                <?php echo $workExperienceForm['comments']->render(array("class" => "formInputText")); ?>
                            </li>
-->
                            <li class="required">
                                <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                            </li>
                        </ol>
                        <p>
                            <input type="button" class="" id="btnWorkExpSave" value="<?php echo __("Save"); ?>" />
                            <?php if ((!$haveWorkExperience) || 
                                    ($haveWorkExperience && $workExperiencePermissions->canCreate()) || 
                                    ($haveWorkExperience && $workExperiencePermissions->canUpdate())) { ?>
                            <input type="button" class="reset" id="btnWorkExpCancel" value="<?php echo __("Cancel"); ?>" />
                            <?php } ?>
                        </p>
                    </fieldset>
                </form>
            </div>
        </div> <!-- changeWorkExperience  -->
    <?php } ?>
        
    <div class="miniList" id="sectionWorkExperience">

        <div class="head">
            <h1><?php echo __("Work Experience"); ?></h1>
        </div>
            
        <div class="inner">

            <?php if ($workExperiencePermissions->canRead()) : ?>

                <?php include_partial('global/flash_messages', array('prefix' => 'workexperience')); ?>

                <form id="frmDelWorkExperience" action="<?php echo url_for('pim/saveDeleteWorkExperience?empNumber=' . 
                        $empNumber . "&option=delete"); ?>" method="post">
                    <?php echo $listForm ?>
                    <p id="actionWorkExperience">
                        <?php if ($workExperiencePermissions->canCreate() ) { ?>
                        <input type="button" value="<?php echo __("Add");?>" class="" id="addWorkExperience" />
                        <?php } ?>
                        <?php if ($workExperiencePermissions->canDelete() ) { ?>
                        <input type="button" value="<?php echo __("Delete");?>" class="delete" id="delWorkExperience" />
                        <?php } ?>
                    </p>
                    <table id="" class="table hover">
                        <thead>
                            <tr>
                                <?php if ($workExperiencePermissions->canDelete()) { ?>
                                <th class="check" style="width:2%"><input type="checkbox" id="workCheckAll" /></th>
                                <?php }?>
                                <th><?php echo __('Company');?></th>
                                <th><?php echo __('Job Title');?></th>
                                <th><?php echo __('CTC (previous company)');?></th>
                                <th><?php echo __('From');?></th>
                                <th><?php echo __('To');?></th>
                                <th><?php echo __('Last Apprisal Date');?></th>
                                <th><?php echo __('Relevant Experience');?></th>
                                <th><?php echo __('Total Experience');?></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$haveWorkExperience) { ?>
                                <tr>
                                    <?php if ($workExperiencePermissions->canDelete()) { ?>
                                    <td class="check"></td>
                                    <?php } ?>
                                    <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php } else { ?>                        
                                <?php
                                $workExperiences = $workExperienceForm->workExperiences;
                                $row = 0;
                                foreach ($workExperiences as $workExperience) :
                                    $cssClass = ($row % 2) ? 'even' : 'odd';
                                    $fromDate = set_datepicker_date_format($workExperience->from_date);
                                    $toDate = set_datepicker_date_format($workExperience->to_date);
                                    $lastApprisalDate= set_datepicker_date_format($workExperience->last_appraisal_date);
                                    ?>
                                    <tr class="<?php echo $cssClass;?>">
                                        <td class="check">
                                            <input type="hidden" id="employer_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo htmlspecialchars($workExperience->employer); ?>" />
                                            <input type="hidden" id="jobtitle_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo htmlspecialchars($workExperience->jobtitle); ?>" />
                                            <input type="hidden" id="fromDate_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo $fromDate; ?>" />
                                            <input type="hidden" id="toDate_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo $toDate; ?>" />
                                            <input type="hidden" id="ctc_previous_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo htmlspecialchars($workExperience->ctc_previous); ?>" />
                                            <input type="hidden" id="last_apprisal_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo $lastApprisalDate; ?>" />
                                            <input type="hidden" id="relevant_experience_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo htmlspecialchars($workExperience->relevant_experience); ?>" />
                                            <input type="hidden" id="total_experience_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo htmlspecialchars($workExperience->total_experience); ?>" />
                                            
<!--                                            <input type="hidden" id="comment_<?php echo $workExperience->seqno; ?>" 
                                                   value="<?php echo htmlspecialchars($workExperience->comments); ?>" />
                                            -->
                                            <?php if ($workExperiencePermissions->canDelete()) {?>
                                            <input type="checkbox" class="chkbox1" value="<?php echo $workExperience->seqno;?>" 
                                                   name="delWorkExp[]"/>
                                            <?php }?>
<!--                                            <input type="hidden" class="chkbox1" value="<?php echo $workExperience->seqno;?>" 
                                                   name="delWorkExp[]"/>-->
                                           
                                        </td>
                                        <td class="name">
                                            <?php if ($workExperiencePermissions->canUpdate()) { ?>
                                            <a class="edit" href="#"><?php echo htmlspecialchars($workExperience->employer);?></a>
                                            <?php } else {
                                                echo htmlspecialchars($workExperience->employer); 
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($workExperience->jobtitle);?></td>
                                        <td><?php echo $workExperience->ctc_previous;?></td>
                                        <td><?php echo $fromDate;?></td>
                                        <td><?php echo $toDate;?></td>
                                        <td><?php echo $lastApprisalDate;?></td>
                                        <td><?php echo $workExperience->relevant_experience;?></td>
                                        <td><?php echo $workExperience->total_experience;?></td>
                                        
                                    </tr>
                                    <?php $row++;
                                endforeach;
                            } ?>
                        </tbody>
                    </table>
                </form>

            <?php else : ?>
                <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
            <?php endif; ?>

        </div>
        
    </div> <!-- miniList-sectionWorkExperience -->
    
    <!-- this is education section -->
    <?php
    include_partial('education', array('empNumber' => $empNumber, 'form' => $educationForm, 
        'section' => $section, 'educationPermissions' => $educationPermissions,'listForm'=>$listForm));
    ?>

    <!-- this is skills section -->
    <?php
    include_partial('skill', array('empNumber' => $empNumber, 'form' => $skillForm, 
        'section' => $section, 'skillPermissions' => $skillPermissions,'listForm'=>$listForm));
    ?>
    
    <!-- this is Languages section -->
    <?php
    include_partial('language', array('empNumber' => $empNumber, 'form' => $languageForm, 
        'section' => $section, 'languagePermissions' => $languagePermissions,'listForm'=>$listForm));
    ?>
    
    <!-- this is Licenses section -->
    <?php
    include_partial('license', array('empNumber' => $empNumber, 'form' => $licenseForm, 
        'section' => $section, 'licensePermissions' => $licensePermissions,'listForm'=>$listForm));
    ?>
    
    <?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => CustomField::SCREEN_QUALIFICATIONS));?>
    <?php echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => EmployeeAttachment::SCREEN_QUALIFICATIONS));?>
        
</div> <!-- Box -->

<script type="text/javascript">
    //<![CDATA[
    var fileModified = 0;
    var lang_addWorkExperience = "<?php echo __('Add Work Experience'); ?>";
    var lang_editWorkExperience = "<?php echo __('Edit Work Experience'); ?>";
    var lang_companyRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_jobTitleRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, 
            array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))); ?>';
    var lang_commentLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 200)); ?>";
    var lang_fromDateLessToDate = "<?php echo __('To date should be after From date'); ?>";
    var lang_selectWrkExprToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
    var lang_jobTitleMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_companyMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var canEdit = '<?php echo $workExperiencePermissions->canUpdate(); ?>';
    //]]>
</script>