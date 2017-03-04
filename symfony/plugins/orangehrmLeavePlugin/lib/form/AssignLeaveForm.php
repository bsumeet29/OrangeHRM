<?php

/*
 *
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
 *
 */

/**
 * Assign Leave form class
 */
class AssignLeaveForm extends sfForm {

    const ALL_DAYS = 'all';
    const START_DAY_ONLY = 'start';
    const END_DAY_ONLY = 'end';
    const START_AND_END_DAY = 'start_end';
    const IN_BETWEEN = 'asdsad';  
    protected $leavePeriodService;    
    protected $configService;

    public function getConfigService() {
        
        if (!$this->configService instanceof ConfigService) {
            $this->configService = new ConfigService();
        }
        
        return $this->configService;        
    }

    public function setConfigService($configService) {
        $this->configService = $configService;
    }  
    
    public function getLeavePeriodService() {
        
        if (is_null($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
        }
        return $this->leavePeriodService;
    }

    public function setLeavePeriodService($leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }
    
    /**
     * Configure Form
     *
     */
    public function configure() {

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());

       // $this->setDefault('leaveBalance', '--');

        $this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'postValidation'))));

        $this->getWidgetSchema()->setNameFormat('assignleave[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }

    /**
     *
     * @return array
     */
    protected function getLeaveTypeChoices($leaveTypeList = null) {

        if (!$leaveTypeList) {
            $leaveTypeList = $this->getOption('leaveTypes');
        }
        
        $leaveTypeChoices = array('' => '--' . __('Select') . '--');

        foreach ($leaveTypeList as $leaveType) {
            $leaveTypeChoices[$leaveType->getId()] = $leaveType->getName();
        }

        return $leaveTypeChoices;
    }

    /**
     * Post validation
     * @param $validator
     * @param $values
     * @return unknown_type
     */
    public function postValidation($validator, $values) {

        $errorList = array();

        $fromDateTimeStamp = strtotime($values['txtFromDate']);
        $toDateTimeStamp = strtotime($values['txtToDate']);
        
        if (is_int($fromDateTimeStamp) && is_int($toDateTimeStamp)) {
            if (($values['txtFromDate'] == $values['txtToDate'])) {
                // Single Day leave request
                $duration = $values['duration'];
                $durationType = $duration['duration'];
                if ($durationType == 'specify_time') {
                    
                    $error = $this->validateTimeRange($duration['time']);
                    if (!is_null($error)) {
                        $errorList['duration'] = $error;
                    }
                }
                
                // For compatibility, set total leave time
                $values['txtLeaveTotalTime'] = $this->getDuration($duration['time']['from'], $duration['time']['to']);
                
            } else {
                // Multi Day leave request
                
                $partialDayOption = $values['partialDays'];
                if ($partialDayOption != '') {
                    // check first duration
                    if ($values['firstDuration']['duration'] == 'specify_time') {
                        $error = $this->validateTimeRange($values['firstDuration']['time']);
                        if (!is_null($error)) {
                            $errorList['firstDuration'] = $error;
                        }               
                    }
                    // check second duration
                    if ($partialDayOption == 'start_end') {
                        if ($values['secondDuration']['duration'] == 'specify_time') {

                            $error = $this->validateTimeRange($values['secondDuration']['time']);
                            if (!is_null($error)) {
                                $errorList['secondDuration'] = $error;
                            }                         
                        }
                    }
                }

                if (($toDateTimeStamp - $fromDateTimeStamp) < 0) {
                    $errorList['txtFromDate'] = new sfValidatorError($validator, ' From date should be a before to date');
                }
            }
        }

//        $maxDate = $this->getLeaveAssignDateLimit();
//        $maxTimeStamp = strtotime($maxDate);
//        
//        if (is_int($toDateTimeStamp) && ($toDateTimeStamp > $maxTimeStamp)) {
//            $errorList['txtToDate'] = new sfValidatorError($validator, __('Cannot assign leave beyond ') . $maxDate);
//        }           

        if (count($errorList) > 0) {

            throw new sfValidatorErrorSchema($validator, $errorList);
        }     
        
        $values['txtFromDate'] = date('Y-m-d', $fromDateTimeStamp);
        $values['txtToDate'] = date('Y-m-d', $toDateTimeStamp);

        return $values;
    }
    
    /**
     * @returns NULL or sfValidatorError
     */
    protected function validateTimeRange($duration, $validator) {
        $error = NULL;
        
        $fromTime = $duration['from'];
        $fromTimetimeStamp = strtotime($fromTime);
        $toTime = $duration['to'];
        $toTimetimeStamp = strtotime($toTime);
        if (!is_int($fromTimetimeStamp) || !is_int($fromTimetimeStamp)) {
            $error = new sfValidatorError($validator, ' Invalid time values selected');
        } else if (($toTimetimeStamp - $fromTimetimeStamp) < 0) {
            $error = new sfValidatorError($validator, ' From time should be before to time');
        }
        
        return $error;
    }
    
    protected function getLeaveAssignDateLimit() {
        // If leave period is defined (enforced or not enforced), don't allow apply assign beyond next Leave period
        // If no leave period, don't allow apply/assign beyond next calender year
        $todayNextYear = new DateTime();
       // print_r($todayNextYear);
        //$todayNextYear->add(new DateInterval('P1Y'));
            
        if ($this->getConfigService()->isLeavePeriodDefined()) {
            $period = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate($todayNextYear->format('Y-m-d'));
            $maxDate = $period[1];
            // print_r($maxDate);
        } else {
            $nextYear = $todayNextYear->format('Y');
            $maxDate = $nextYear . '-12-31';
        }        
        
        return $maxDate;
    }
    
      private function getEmployeeNumber() {
        return $_SESSION['empID'];
    }
    
      private function getEmployeeListAsJson() {

        $jsonArray = array();

        $properties = array("empNumber", "firstName", "middleName", "lastName", 'termination_id');

        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_ACTION => array('assign_leave'));

        $employeeList = UserRoleManagerFactory::getUserRoleManager()
                ->getAccessibleEntityProperties('Employee', $properties, null, null, array(), array(), $requiredPermissions);
      $obj=new EmployeeDao();
      $employeeList=$obj->getEmployeeList();
        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            $terminationId = $employee['termination_id'];
            $empNumber = $employee['empNumber'];
            if (!isset($employeeUnique[$empNumber]) && empty($terminationId) && $empNumber != $this->getEmployeeNumber()&&(!empty($obj->getReportToObject($empNumber, $this->getEmployeeNumber())))) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);

                $employeeUnique[$empNumber] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $empNumber);
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    /**
     *
     * @return array
     */
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();

        return $styleSheets;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/assignLeaveSuccess.js');

        return $javaScripts;
    }     

    /**
     *
     * @return array
     */
    protected function getFormWidgets() {
        $today=date('d-m-Y');
        $today = strtotime($today);
        $date = strtotime('-3 days',$today);
        $mindate = date('d-m-Y',$date);
        $partialDayChoices = array(
            '' => __('None'), 
           // self::ALL_DAYS => __('All Days'), 
            self::START_DAY_ONLY => __('Start Day Only'), 
            self::END_DAY_ONLY => __('End Day Only'),
            self::IN_BETWEEN => __('IN bet'),
            
            self::START_AND_END_DAY => __('Start and End Day'));
        $workType=array(
            ''=>__('Select'),
            '1'=>__('Urgent Deliverable'),
            '2'=>__('Pending task'),
            '3'=>__('Training')
        );
        
                
        $widgets = array(
            'txtEmpWorkShift' => new sfWidgetFormInputHidden(),
//            'txtLeaveType' => new sfWidgetFormChoice(array('choices' => $this->getLeaveTypeChoices())),
//            'leaveBalance' => new ohrmWidgetDiv(array()),
            'txtFromDate' => new ohrmWidgetDatePicker(array(), array('id' => 'assignleave_txtFromDate','minDate' => $mindate)),
            'txtToDate' => new ohrmWidgetDatePicker(array(), array('id' => 'assignleave_txtToDate','minDate' => $mindate)),
            'duration' => new ohrmWidgetFormLeaveDuration(),
            'partialDays' => new sfWidgetFormChoice(array('choices' => $partialDayChoices)),
            'firstDuration' => new ohrmWidgetFormLeaveDuration(array('enable_full_day' => false)),
            'secondDuration' => new ohrmWidgetFormLeaveDuration(array('enable_full_day' => false)),
            'txtComment' => new sfWidgetFormTextarea(array(), array('rows' => '3', 'cols' => '30')),
            'txtEmployee' => new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
            'txtWorkType'=>new sfWidgetFormChoice(array('choices' => $workType)),
            'txtExpectedTask' => new ohrmWidgetDiv(array()),
            'txtTask'=>new sfWidgetFormInputText(array(), array(),array()),    
            
            
        );

        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators() {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

//        $leaveTypeIds = array_keys($this->getLeaveTypeChoices());        
        
        $validators = array(
            'txtEmployee' => new ohrmValidatorEmployeeNameAutoFill(),
            'txtEmpWorkShift' => new sfValidatorString(array('required' => false)),
//            'txtLeaveType' => new sfValidatorChoice(array('choices' => $leaveTypeIds, 'required' => true)),
            'txtFromDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'txtToDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'duration' => new sfValidatorPass(),
            'partialDays' => new sfValidatorPass(),
            'firstDuration' => new sfValidatorPass(),
            'secondDuration' => new sfValidatorPass(), 
            'txtWorkType'=>new sfValidatorPass(),
            'txtComment' => new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            'txtTask'=>new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            
            
        );

        return $validators;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $requiredMarker = ' <em>*</em>';

        $labels = array(
            'txtEmployee' => __('Called By') . $requiredMarker,
//            'txtLeaveType' => __('Leave Type') . $requiredMarker,
//            'leaveBalance' => __('Leave Balance'),
            'txtFromDate' => __('From Date') . $requiredMarker,
            'txtToDate' => __('To Date') . $requiredMarker,
            'duration' => __('Duration').$requiredMarker,
            'partialDays' => __('Partial Days'),
            'firstDuration' => __('Duration'),
            'secondDuration' => __('Duration'),
            'txtComment' => __('Reason').$requiredMarker,
            'txtWorkType'=>__('Work Type').$requiredMarker,
            'txtTask'=>__('Task').$requiredMarker,
            'txtExpectedTask'=>__('Expected tasks to be completed for the above day(s)')
        );

        return $labels;
    }
    
    protected function getDuration($fromTime, $toTime) {
        list($startHour, $startMin) = explode(':', $fromTime);
        list($endHour, $endMin) = explode(':', $toTime);

        $durationMinutes = (intVal($endHour) - intVal($startHour)) * 60 + (intVal($endMin) - intVal($startMin));
        $hours = $durationMinutes / 60;

        return number_format($hours, 2);
    }    

}

