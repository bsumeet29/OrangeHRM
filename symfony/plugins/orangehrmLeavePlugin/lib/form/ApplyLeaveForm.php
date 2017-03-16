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
 * Form class for apply leave
 */
class ApplyLeaveForm extends sfForm {

    protected $leavePeriodService;    
    protected $configService;
    protected $workScheduleService;
    protected $workSchedule;

    const ALL_DAYS = 'all';
    const START_DAY_ONLY = 'start';
    const END_DAY_ONLY = 'end';
    const START_AND_END_DAY = 'start_end';
    const IN_BETWEEN_DAY = 'In_between';

    private $taskList;

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
     * Get work schedule service
     * @return WorkScheduleService
     */
    public function getWorkScheduleService() {
        if (!($this->workScheduleService instanceof WorkScheduleService)) {
            $this->workScheduleService = new WorkScheduleService();
        }
        return $this->workScheduleService;
    }

    /**
     *
     * @param WorkScheduleService $service 
     */
    public function setWorkScheduleService(WorkScheduleService $service) {
        $this->workScheduleService = $service;
    }    
    
    protected function getWorkSchedule() {
        if (is_null($this->workSchedule)) {
            $this->workSchedule = $this->getWorkScheduleService()->getWorkSchedule($this->getEmployeeNumber());
        }
        return $this->workSchedule;
    }
    
    /**
     * Configure ApplyLeaveForm
     *
     */
    public function configure() {

        $this->leaveTypeList = $this->getOption('leaveTypes');
        $this->previousTasks = $this->getOption('tasks');
        $count = count($this->previousTasks);
        $this->setWidgets($this->getFormWidgets($this->previousTasks));
        $this->setValidators($this->getFormValidators($this->previousTasks));
        $workSchedule = $this->getWorkSchedule();
        $workScheduleStartEndTime = $workSchedule->getWorkShiftStartEndTime();
        
        $this->setDefault('txtEmpID', $this->getEmployeeNumber());
        $this->setDefault('txtContact', $this->getEmployeePhoneNumber());
        $this->setDefault('txtEmpWorkShift', $workSchedule->getWorkShiftLength());
        $this->setDefault('leaveBalance', '--');
        
        $specifyTimeDefault = array('time' => 
            array('from' => $workScheduleStartEndTime['start_time'], 
                  'to' => $workScheduleStartEndTime['end_time']));
        
        $this->setDefault('duration', $specifyTimeDefault);
        $this->setDefault('firstDuration', $specifyTimeDefault);
        $this->setDefault('secondDuration', $specifyTimeDefault);

        $this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'postValidation'))));

        $this->getWidgetSchema()->setNameFormat('applyleave[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }

    /**
     *
     * @return array
     */
    public function getLeaveTypeList() {
        return $this->leaveTypeList;
    }

    public static function setLeaveTypes($types) {
        self::$leaveTypeList = $types;
    }

     public function getTaskList() {
        return $this->taskList;
    }

    public static function setTaskList($tasks) {
        self::$taskList = $tasks;
    }
    /**
     * Get Time Choices
     * @return unknown_type
     */
    private function getTimeChoices() {
        $startTime = strtotime("00:00");
        $endTime = strtotime("23:59");
        $interval = 60 * 15;
        $timeChoices = array();
        $timeChoices[''] = '';
        for ($i = $startTime; $i <= $endTime; $i+=$interval) {
            $timeVal = date('H:i', $i);
            $timeChoices[$timeVal] = $timeVal;
        }
        return $timeChoices;
    }

    /**
     * get Leave Request
     * @return LeaveRequest
     */
    public function getLeaveRequest() {

        $posts = $this->getValues();
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setLeaveTypeId($posts['txtLeaveType']);
        $leaveRequest->setDateApplied($posts['txtFromDate']);
        $leaveRequest->setLeavePeriodId($this->getLeavePeriod($posts['txtFromDate']));
        $leaveRequest->setEmpNumber($posts['txtEmpID']);
        $leaveRequest->setLeaveComments($posts['txtComment']);
        $leaveRequest->setTask($posts['txtTask']);
        $leaveRequest->setAlternateContactPerson($posts['txtContactPerson']);
        return $leaveRequest;
    }

    /**
     * Get Leave
     * @return Leave
     */
    public function createLeaveObjectListForAppliedRange() {
        $posts = $this->getValues();

        $leaveList = array();
        $from = strtotime($posts['txtFromDate']);
        $to = strtotime($posts['txtToDate']);

        for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {
            $leave = new Leave();

            $leaveDate = date('Y-m-d', $timeStamp);
            $isWeekend = $this->isWeekend($leaveDate);
            $isHoliday = $this->isHoliday($leaveDate);
            $isHalfday = $this->isHalfDay($leaveDate);
            $isHalfDayHoliday = $this->isHalfdayHoliday($leaveDate);

            $leave->setLeaveDate($leaveDate);
            $leave->setLeaveComments($posts['txtComment']);
            $leave->setLeaveLengthDays($this->calculateDateDeference($isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday));
            $leave->setStartTime(($posts['txtFromTime'] != '') ? $posts['txtFromTime'] : '00:00');
            $leave->setEndTime(($posts['txtToTime'] != '') ? $posts['txtToTime'] : '00:00');
            $leave->setLeaveLengthHours($this->calculateTimeDeference($isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday));
            $leave->setLeaveStatus($this->getLeaveRequestStatus($isWeekend, $isHoliday));

            array_push($leaveList, $leave);
        }
        return $leaveList;
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
                    print_r("12345");
                    if ($partialDayOption == 'In_between') {
                        $errorList['In_between'] = "Try different app";
                        //echo "<script type='text/javascript'>alert('$message');</script>";
                    }
                }
		/*$cd = new DateTime('now',new DateTimeZone('Asia/Kolkata'));			
		if (($fromDateTimeStamp - $cd) < 7) {
                    $errorList['txtFromDate'] = new sfValidatorError($validator, ' It can be applied');}*/
                
		if (($toDateTimeStamp - $fromDateTimeStamp) < 0) {
                    $errorList['txtFromDate'] = new sfValidatorError($validator, ' From date should be a before to date');
                }
            }
        }

        $maxDate = $this->getLeaveAssignDateLimit();
        
        $maxTimeStamp = strtotime($maxDate);
        
        if (is_int($toDateTimeStamp) && ($toDateTimeStamp > $maxTimeStamp)) {
            $errorList['txtToDate'] = new sfValidatorError($validator, __('Cannot assign leave beyond ') . $maxDate);
        }           

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
       // $todayNextYear->add(new DateInterval('P1Y'));
            
        if ($this->getConfigService()->isLeavePeriodDefined()) {
            $period = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate($todayNextYear->format('Y-m-d'));
            $maxDate = $period[1];
        } else {
            $nextYear = $todayNextYear->format('Y');
            $maxDate = $nextYear . '-12-31';
        }        
        
        return $maxDate;
    }
    
    /**
     * Calculate Date deference
     * @return int
     */
    public function calculateDateDeference($isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {
        $posts = $this->getValues();
        
        if ($isWeekend)
            $dayDeference = 0;
        elseif ($isHoliday) {
            if ($isHalfDayHoliday) {
                if ($posts['txtToDate'] == $posts['txtFromDate']) {
                    if ($posts['txtEmpWorkShift'] / 2 <= $posts['txtLeaveTotalTime'])
                        $dayDeference = 0.5;
                    else
                        $dayDeference = number_format($posts['txtLeaveTotalTime'] / $posts['txtEmpWorkShift'], 3);
                }else
                    $dayDeference = 0.5;
            }else
                $dayDeference = 0;
        }elseif ($isHalfday) {

            if ($posts['txtToDate'] == $posts['txtFromDate']) {
                if ($posts['txtEmpWorkShift'] / 2 <= $posts['txtLeaveTotalTime'])
                    $dayDeference = 0.5;
                else
                    $dayDeference = number_format($posts['txtLeaveTotalTime'] / $posts['txtEmpWorkShift'], 3);
            }else
                $dayDeference = 0.5;
        }else {
            if ($posts['txtToDate'] == $posts['txtFromDate'])
                $dayDeference = number_format($posts['txtLeaveTotalTime'] / $posts['txtEmpWorkShift'], 3);
            else
            //$dayDeference	=	floor((strtotime($posts['txtToDate'])-strtotime($posts['txtFromDate']))/86400)+1;
                $dayDeference = 1;
        }

        return $dayDeference;
    }

    /**
     * Calculate Applied Date range
     * @return int
     */
    public function calculateAppliedDateRange($leaveList) {
        $dateRange = 0;
        foreach ($leaveList as $leave) {
            $dateRange += $leave->getLeaveLengthDays();
        }
        return $dateRange;
    }

    /**
     * Calculate Date deference
     * @return int
     */
    public function calculateTimeDeference($isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {
        $posts = $this->getValues();
        if ($isWeekend) {
            $timeDeference = 0;
        } elseif ($isHoliday) {
            if ($isHalfDayHoliday) {
                if ($posts['txtToDate'] == $posts['txtFromDate']) {
                    if ($posts['txtEmpWorkShift'] / 2 <= $posts['txtLeaveTotalTime'])
                        $timeDeference = number_format($posts['txtEmpWorkShift'] / 2, 3);
                    else
                        $timeDeference = $posts['txtLeaveTotalTime'];
                }else
                    $timeDeference = number_format($posts['txtEmpWorkShift'] / 2, 3);
            }else
                $timeDeference = 0;
        }elseif ($isHalfday) {
            if ($posts['txtToDate'] == $posts['txtFromDate'] && $posts['txtLeaveTotalTime'] > 0) {
                if ($posts['txtEmpWorkShift'] / 2 <= $posts['txtLeaveTotalTime'])
                    $timeDeference = number_format($posts['txtEmpWorkShift'] / 2, 3);
                else
                    $timeDeference = $posts['txtLeaveTotalTime'];
            }else
                $timeDeference = number_format($posts['txtEmpWorkShift'] / 2, 3);
        }else {
            if ($posts['txtToDate'] == $posts['txtFromDate']) {
                $timeDeference = $posts['txtLeaveTotalTime'];
            } else {
                $timeDeference = $this->getWorkSchedule()->getWorkShiftLength();
            }
        }

        return $timeDeference;
    }

    /**
     *
     * @param $isWeekend
     * @return status
     */
    public function getLeaveRequestStatus($isWeekend, $isHoliday) {
        $status = Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;

        if ($isWeekend) {
            $status = Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            $status = Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        return $status;
    }

    /**
     *
     * @param $day
     * @return boolean
     */
    public function isWeekend($day) {
        $workWeekService = new WorkWeekService();
        $workWeekService->setWorkWeekDao(new WorkWeekDao());

        return $workWeekService->isWeekend($day, true);
    }

    /**
     *
     * @param $day
     * @return boolean
     */
    public function isHoliday($day) {
        $holidayService = new HolidayService();
        return $holidayService->isHoliday($day);
    }

    /**
     *
     * @param $day
     * @return boolean
     */
    public function isHalfDay($day) {
        $workWeekService = new WorkWeekService();
        $workWeekService->setWorkWeekDao(new WorkWeekDao());

        $holidayService = new HolidayService();

        //this is to check weekday half days
        $flag = $holidayService->isHalfDay($day);
        if (!$flag) {
            //this checks for weekend half day
            return $workWeekService->isWeekend($day, false);
        }
        return $flag;
    }

    /**
     *
     * @param $day
     * @return boolean
     */
    public function isHalfdayHoliday($day) {
        $holidayService = new HolidayService();
        return $holidayService->isHalfdayHoliday($day);
    }

    /**
     * Date increment
     *
     * @param int $timestamp
     */
    private function incDate($timestamp) {

        return strtotime("+1 day", $timestamp);
    }

    /**
     * Get Employee number
     * @return int
     */
    private function getEmployeeNumber() {
        return $_SESSION['empID'];
    }
    private function getEmployeePhoneNumber(){
         $employeeService = new EmployeeService();
         $employee=$employeeService->getEmployee($_SESSION['empID']);
         return $employee->emp_mobile;
    }

    /**
     * Get Leave Period
     * @param $fromDate
     * @return unknown_type
     */
    private function getLeavePeriod($fromDate) {

        $leavePeriodService = new LeavePeriodService();
        $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());

        $leavePeriod = $leavePeriodService->getLeavePeriod(strtotime($fromDate));

        if ($leavePeriod != null)
            return $leavePeriod->getLeavePeriodId();
        else
            return null;
    }

    /**
     * check overlap leave request
     * @return unknown_type
     */
    public function isOverlapLeaveRequest() {
        $posts = $this->getValues();
        $leavePeriodService = new LeavePeriodService();
        $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());

        $leavePeriod = $leavePeriodService->getLeavePeriod(strtotime($posts['txtFromDate']));

        if ($leavePeriod != null) {
            if ($posts['txtToDate'] > $leavePeriod->getEndDate())
                return true;
        }

        return false;
    }

    /**
     *
     * @return array
     */
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        
        $styleSheets[plugin_web_path('orangehrmLeavePlugin', 'css/applyLeaveSuccess.css')] = 'all';
        
        return $styleSheets;
    }

    /**
     *
     * @return array
     */
    protected function getFormWidgets($previousTasks) {
        $mindate = date('d-m-Y',strtotime('-3 days'));
        $count = count($previousTasks);
        $partialDayChoices = array(
            '' => __('None'), 
            //self::ALL_DAYS => __('All Days'), 
            self::START_DAY_ONLY => __('Start Day Only'), 
            self::END_DAY_ONLY => __('End Day Only'),
            self::START_AND_END_DAY => __('Start and End Day'));
            //self::IN_BETWEEN_DAY=> __('In between days!'));
       // $taskWidgets = array();
       
      /*  foreach ($taskList as $task)
        {
            
        }*/
        $widgets = array(
                     
            'txtFromDate' => new ohrmWidgetDatePicker(array(), array('id' => 'applyleave_txtFromDate','minDate' => $mindate)),
            'txtToDate' => new ohrmWidgetDatePicker(array(), array('id' => 'applyleave_txtToDate','minDate' => $mindate)),
            'duration' => new ohrmWidgetFormLeaveDuration(),
            'partialDays' => new sfWidgetFormChoice(array('choices' => $partialDayChoices)),
            'firstDuration' => new ohrmWidgetFormLeaveDuration(array('enable_full_day' => false)),
            'secondDuration' => new ohrmWidgetFormLeaveDuration(array('enable_full_day' => false)),
             'txtEmpID' => new sfWidgetFormInputHidden(),
            'txtEmpWorkShift' => new sfWidgetFormInputHidden(),
            'txtLeaveType' => new sfWidgetFormChoice(array('choices' => $this->getLeaveTypeList())),
            'leaveBalance' => new ohrmWidgetDiv(), 
            'txtComment' => new sfWidgetFormTextarea(array(), array('rows' => '3', 'cols' => '30','style' => 'height:75px')),
            'txtContact'=>new sfWidgetFormInputText(array(), array('id'=>'applyleave_txtContact'),array()), 
         //   'txtPreviousTask' => new sfWidgetFormInputText(array('id'=>$previousTasks )),
            'txtTask'=> new sfWidgetFormInputText(array(), array('id'=>'applyleave_txtTask')),
            'txtContactPerson'=>new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
            'txtTask1'=>new sfWidgetFormInputText(array(),array()),   
            'txtContactPerson1'=>new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
            
            'txtTask2'=>new sfWidgetFormInputText(array(),array()),   
            'txtContactPerson2'=>new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
            
            'txtTask3'=>new sfWidgetFormInputText(array(),array()),   
            'txtContactPerson3'=>new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
            
            'txtTask4'=>new sfWidgetFormInputText(array(),array()),   
            'txtContactPerson4'=>new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
            
            'txtTask5'=>new sfWidgetFormInputText(array(),array()) ,  
            'txtContactPerson5'=>new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
 
        );
        $i = 0;
        $taskwidgets = array();
        foreach ($previousTasks as $key => $value) {
            $taskwidgets['txtPreviousTask_' . $i] = new sfWidgetFormInputText(array(), array('id' => 'txtPreviousTask_' . $i, 'value' => $value));
            $taskwidgets['txtContactPerson_' . $i] = new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()));
            $i++;
        }
        
        $widgets = array_merge($widgets, $taskwidgets);
//        var_dump($widgets);exit;
        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators($tasks) {
        $count = count($tasks);
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        $validators = array(
            'txtEmpID' => new sfValidatorString(array('required' => true), array('required' => __(ValidationMessages::REQUIRED))),
            'txtEmpWorkShift' => new sfValidatorString(array('required' => false)),
            'txtLeaveType' => new sfValidatorChoice(array('choices' => array_keys($this->getLeaveTypeList()))),
            'txtFromDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'txtToDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'duration' => new sfValidatorPass(),
            'partialDays' => new sfValidatorPass(),
            'firstDuration' => new sfValidatorPass(),
            'secondDuration' => new sfValidatorPass(),               
            'txtComment' => new sfValidatorString(array('required' => true, 'trim' => true, 'max_length' => 1000)),
            'txtContact'=>new sfValidatorNumber(array('required' => true, 'trim' => true)),
            
            'txtTask'=>new sfValidatorString(array('required' => true, 'trim' => true, 'max_length' => 1000)),
            'txtContactPerson'=>new ohrmValidatorEmployeeNameAutoFill(),
            
            'txtTask1'=>new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            'txtContactPerson1'=>new ohrmValidatorEmployeeNameAutoFill(),
            
            'txtTask2'=>new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            'txtContactPerson2'=>new ohrmValidatorEmployeeNameAutoFill(),
            
            'txtTask3'=>new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            'txtContactPerson3'=>new ohrmValidatorEmployeeNameAutoFill(),
            
            'txtTask4'=>new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            'txtContactPerson4'=>new ohrmValidatorEmployeeNameAutoFill(),
            
            'txtTask5'=>new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            'txtContactPerson5'=>new ohrmValidatorEmployeeNameAutoFill()
        );
        $taskvalidators = array();
         for($i=0;$i<$count;$i++)
        {
           /*$taskvalidators[] = array(
               
             'txtPreviousTask_'.$i=> new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
            'txtContactPerson_'.$i=>new ohrmValidatorEmployeeNameAutoFill(),
        );*/
            $taskvalidators['txtPreviousTask_'.$i] = new sfValidatorString(array('required' => false, trim => true, 'max_length' => 1000));
            $taskvalidators['txtContactPerson_'.$i] =new ohrmValidatorEmployeeNameAutoFill();
        }
    $validators= array_merge($validators,$taskvalidators);

        return $validators;
    }
    
    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $requiredMarker = ' <em>*</em>';
        
        $labels = array(
            'txtLeaveType' => __('Leave Type') . $requiredMarker,
            'leaveBalance' => __('Leave Balance'),
            'txtFromDate' => __('From Date') . $requiredMarker,
            'txtToDate' => __('To Date') . $requiredMarker,
            'duration' => __('Duration'),
            'partialDays' => __('Partial Days'),
            'firstDuration' => __('Duration'),
            'secondDuration' => __('Duration'),            
            'txtComment' => __('Reason of leave'). $requiredMarker,
            'txtContact' => __('Contact Details during absence'). $requiredMarker,
            'txtPreviousTask_0' => false,
            'txtContactPerson_0' => false,
             'txtPreviousTask_1' => false,
            'txtContactPerson_1' => false,
             'txtPreviousTask_2' => false,
            'txtContactPerson_2' => false,
             'txtPreviousTask_3' => false,
            'txtContactPerson_3' => false,
             'txtPreviousTask_4' => false,
            'txtContactPerson_4' => false,
             'txtPreviousTask_5' => false,
            'txtContactPerson_5' => false,
            'txtTask' => __( 'Task'),
            'txtContactPerson' => __('Contact person'). $requiredMarker,
            'txtTask1' => false,
            'txtContactPerson1'=>false,
            'txtTask2' =>false,
            'txtContactPerson2'=>false,
            'txtTask3' => false,
            'txtContactPerson3'=>false,
            'txtTask4' => false,
            'txtContactPerson4'=>false,
            'txtTask5' => false,
            'txtContactPerson5'=>false,
            
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
            if (!isset($employeeUnique[$empNumber]) && empty($terminationId) && $empNumber != $this->getEmployeeNumber()) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);

                $employeeUnique[$empNumber] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $empNumber);
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

}

