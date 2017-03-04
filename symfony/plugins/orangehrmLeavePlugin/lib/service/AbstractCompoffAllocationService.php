<?php

abstract class AbstractCompoffAllocationService extends BaseService {

    protected $leaveRequestService;
    protected $compoffRequestService;
    protected $compoffTasksService;
    protected $overlapCompoff;
    protected $leaveTypeService;
    protected $leavePeriodService;
    protected $employeeService;
    protected $workWeekService;
    protected $holidayService;
    protected $overlapLeave;
    private $workScheduleService;
    protected $workflowService;
    protected $userRoleManager;
    protected $leaveTasksService;

    public function getLeaveTasksService() {
        if (empty($this->leaveTasksService)) {
            $this->leaveTasksService = new LeaveTasksService();
        }
        return $this->leaveTasksService;
    }

    public function setLeaveTasksService(LeaveTasksService $leaveTasksService) {
        $this->leaveTasksService = $leaveTasksService;
    }
    
    public function getCompoffTasksService() {
        
       if (empty($this->compoffTasksService)) {
            $this->compoffTasksService = new CompoffTasksService();
        }
        return $this->compoffTasksService;
        
    }

    public function setCompoffTasksService(CompoffTasksService $compoffTasksService) {
        $this->compoffTasksService = $compoffTasksService;
    }

    public function getWorkflowService() {
        if (empty($this->workflowService)) {
            $this->workflowService = new AccessFlowStateMachineService();
        }
        return $this->workflowService;
    }

    public function setWorkflowService(AccessFlowStateMachineService $workflowService) {
        $this->workflowService = $workflowService;
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

    /**
     * 
     * Saves Compoff Request and Sends Notification
     * @param CompOffParameterObject $compoffAssignmentData 
     */
    protected abstract function saveCompoffRequest(CompOffParameterObject $compoffAssignmentData);

    /**
     *
     * @param bool $isWeekend
     * @return int
     */
    protected abstract function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate, CompOffParameterObject $leaveAssignmentData);

    protected abstract function allowToExceedLeaveBalance();

    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected abstract function getLogger();

    /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (!($this->leaveRequestService instanceof LeaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
        }
        return $this->leaveRequestService;
    }

    /**
     *
     * @param LeaveRequestService $service 
     */
    public function setLeaveRequestService(LeaveRequestService $service) {
        $this->leaveRequestService = $service;
    }
    public function getCompoffRequestService() {
        if (!($this->compoffRequestService instanceof CompoffRequestService)) {
            $this->compoffRequestService = new CompoffRequestService();
        }
        return $this->compoffRequestService;
    }

    public function setCompoffRequestService(CompoffRequestService $compoffRequestService) {
        $this->compoffRequestService = $compoffRequestService;
    }
    

        
        
    /**
     *
     * @return LeaveTypeService
     */
    public function getLeaveTypeService() {
        if (!($this->leaveTypeService instanceof LeaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     *
     * @param LeaveTypeService $service 
     */
    public function setLeaveTypeService(LeaveTypeService $service) {
        $this->leaveTypeService = $service;
    }

    /**
     *
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        if (!($this->leavePeriodService instanceof LeavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
        }
        return $this->leavePeriodService;
    }

    /**
     *
     * @param LeavePeriodService $service 
     */
    public function setLeavePeriodService(LeavePeriodService $service) {
        $this->leavePeriodService = $service;
    }

    /**
     *
     * @return EmployeeService
     */
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     *
     * @param EmployeeService $service 
     */
    public function setEmployeeService(EmployeeService $service) {
        $this->employeeService = $service;
    }

    /**
     *
     * @return WorkWeekService
     */
    public function getWorkWeekService() {
        if (!($this->workWeekService instanceof WorkWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }

    /**
     *
     * @param WorkWeekService $service 
     */
    public function setWorkWeekService(WorkWeekService $service) {
        $this->workWeekService = $service;
    }

    /**
     *
     * @return HolidayService
     */
    public function getHolidayService() {
        if (!($this->holidayService instanceof HolidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     *
     * @param HolidayService $service 
     */
    public function setHolidayService(HolidayService $service) {
        $this->holidayService = $service;
    }

    /**
     * Get User role manager instance
     * @return AbstractUserRoleManager
     */
    public function getUserRoleManager() {
        if (!($this->userRoleManager instanceof AbstractUserRoleManager)) {
            $this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();
        }
        return $this->userRoleManager;
    }

    /**
     * Set user role manager instance
     * @param AbstractUserRoleManager $userRoleManager
     */
    public function setUserRoleManager(AbstractUserRoleManager $userRoleManager) {
        $this->userRoleManager = $userRoleManager;
    }

    /**
     *
     * @return mixed 
     */
    public function getOverlapLeave() {
        return $this->overlapLeave;
    }

    /**
     *
     * @param mixed $overlapLeaveRecords 
     */
    public function setOverlapLeave($overlapLeaveRecords) {
        $this->overlapLeave = $overlapLeaveRecords;
    }
    public function getOverlapCompoff() {
        return $this->overlapCompoff;
    }

    public function setOverlapCompoff($overlapCompoff) {
        $this->overlapCompoff = $overlapCompoff;
    }

        
    /**
     * Checking for leave overlaps
     * @return bool
     */
    public function hasOverlapCompoff(CompOffParameterObject $compoffAssignmentData) {

        $startDayStartTime = null;
        $startDayEndTime = null;
        $endDayStartTime = null;
        $endDayEndTime = null;

        $startDuration = null;
        $endDuration = null;

        if ($compoffAssignmentData->getMultiDayLeave()) {
            $partialDayOption = $compoffAssignmentData->getMultiDayPartialOption();

            if ($partialDayOption == 'all') {
                $startDuration = $compoffAssignmentData->getFirstMultiDayDuration();
            } else if ($partialDayOption == 'start') {
                $startDuration = $compoffAssignmentData->getFirstMultiDayDuration();
            } else if ($partialDayOption == 'end') {
                $endDuration = $compoffAssignmentData->getSecondMultiDayDuration();
            } else if ($partialDayOption == 'start_end') {
                $startDuration = $compoffAssignmentData->getFirstMultiDayDuration();
                $endDuration = $compoffAssignmentData->getSecondMultiDayDuration();
            }

            $allPartialDays = ($partialDayOption == 'all');
        } else {
            $allPartialDays = false;

            $startDuration = $compoffAssignmentData->getSingleDayDuration();
        }

        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($compoffAssignmentData->getEmp_number());
        $workScheduleStartEndTime = $workSchedule->getWorkShiftStartEndTime();
        $workScheduleDuration = $workSchedule->getWorkShiftLength();
        $midDay = $this->addHoursDuration($workScheduleStartEndTime['start_time'], $workScheduleDuration / 2);

        // set start times
        if (!is_null($startDuration)) {
            if ($startDuration->getType() == LeaveDuration::HALF_DAY) {
                if ($startDuration->getAmPm() == LeaveDuration::HALF_DAY_AM) {
                    $startDayStartTime = $workScheduleStartEndTime['start_time'];
                    $startDayEndTime = $midDay;
                } else {
                    $startDayStartTime = $midDay;
                    $startDayEndTime = $workScheduleStartEndTime['end_time'];
                }
            } else if ($startDuration->getType() == LeaveDuration::SPECIFY_TIME) {
                $startDayStartTime = $startDuration->getFromTime();
                $startDayEndTime = $startDuration->getToTime();
            }
        }

        // set end times
        if (!is_null($endDuration)) {
            if ($endDuration->getType() == LeaveDuration::HALF_DAY) {
                if ($endDuration->getAmPm() == LeaveDuration::HALF_DAY_AM) {
                    $endDayStartTime = $workScheduleStartEndTime['start_time'];
                    $endDayEndTime = $midDay;
                } else {
                    $endDayStartTime = $midDay;
                    $endDayEndTime = $workScheduleStartEndTime['end_time'];
                }
            } else if ($endDuration->getType() == LeaveDuration::SPECIFY_TIME) {
                $endDayStartTime = $endDuration->getFromTime();
                $endDayEndTime = $endDuration->getToTime();
            }
        }

        $overlapCompoff = $this->getCompoffRequestService()->getOverlappingCompoff($compoffAssignmentData->getFromDate(), $compoffAssignmentData->getToDate(), $compoffAssignmentData->getEmp_number(), $startDayStartTime, $startDayEndTime, $allPartialDays, $endDayStartTime, $endDayEndTime);

        $this->setOverlapCompoff($overlapCompoff);

        return (count($overlapCompoff) !== 0);
    }

    /**
     * isEmployeeAllowedToApply
     * @param LeaveType $leaveType
     * @returns boolean
     */
    public function isEmployeeAllowedToApply(LeaveType $leaveType) {
        return true;
    }

    /**
     * Check if user has exceeded the allowed hours per day in existing and current leave request.
     * 
     * @param LeaveParameterObject $leaveAssignmentData Leave Parameters
     * @return boolean True if user has exceeded limit, false if not
     */
    public function applyMoreThanAllowedForADay(LeaveParameterObject $leaveAssignmentData) {

        $logger = $this->getLogger();

        $workshiftExceeded = false;

        $overlapLeave = array();

        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        $fromDate = $leaveAssignmentData->getFromDate();
        $toDate = $leaveAssignmentData->getToDate();

        $workShiftLength = $this->getWorkShiftDurationForEmployee($empNumber);

        $from = strtotime($fromDate);
        $to = strtotime($toDate);

        $firstDay = true;

        for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {
            $date = date('Y-m-d', $timeStamp);

            $existingDuration = $this->getLeaveRequestService()->getTotalLeaveDuration($empNumber, $date);

            $lastDay = ($timeStamp == $to);
            $duration = $this->getApplicableLeaveDuration($leaveAssignmentData, $firstDay, $lastDay);
            $firstDay = false;

            $workingDayLength = $workShiftLength;

            if ($this->isHoliday($date, $leaveAssignmentData) || $this->isWeekend($date, $leaveAssignmentData)) {
                if ($logger->isDebugEnabled()) {
                    $logger->debug("Skipping $date since it is a weekend/holiday");
                }
                continue;
            }

            // Reduce workshiftLength for half days
            $halfDay = $this->isHalfDay($date, $leaveAssignmentData);
            if ($halfDay) {
                $workingDayLength = $workShiftLength / 2;
            }

            if ($duration->getType() == LeaveDuration::FULL_DAY) {
                $leaveHours = $workingDayLength;
            } else if ($duration->getType() == LeaveDuration::HALF_DAY) {
                $leaveHours = $workShiftLength / 2;
            } else if ($duration->getType() == LeaveDuration::SPECIFY_TIME) {
                $leaveHours = $this->getDurationInHours($duration->getFromTime(), $duration->getToTime());
            } else {
                $logger->error("Unexpected duration type in applyMoreThanAllowedForADay(): " . print_r($duration->getType(), true));
                $leaveHours = 0;
            }

            if ($logger->isDebugEnabled()) {
                $logger->debug("date=$date, existing leave duration=$existingDuration, " .
                        "workShiftLength=$workShiftLength, totalLeaveTime=$leaveHours,workDayLength=$workingDayLength");
            }

            // We only show workshift exceeded warning for partial leave days (length < workshift)            
            if (($existingDuration + $leaveHours) > $workingDayLength) {

                if ($logger->isDebugEnabled()) {
                    $logger->debug('Workshift length exceeded!');
                }

                $parameter = new ParameterObject(array('dateRange' => new DateRange($date, $date), 'employeeFilter' => $empNumber));
                $leaveRequests = $this->getLeaveRequestService()->searchLeaveRequests($parameter);

                if (count($leaveRequests['list']) > 0) {

                    foreach ($leaveRequests['list'] as $leaveRequest) {
                        $leaveList = $leaveRequest->getLeave();
                        foreach ($leaveList as $leave) {
                            if ($leave->getDate() == $date) {
                                $overlapLeave[] = $leave;
                            }
                        }
                    }
                }

                $workshiftExceeded = true;
            }
        }

        if (!empty($overlapLeave)) {
            $this->setOverlapLeave($overlapLeave);
        }

        return $workshiftExceeded;
    }

    /**
     * 
     * Checks overlapping leave request
     * @param LeaveParameterObject $leaveAssignmentData
     * @return bool
     */
    public function isOverlapLeaveRequest(LeaveParameterObject $leaveAssignmentData) {

//        $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod(strtotime($leaveAssignmentData->getFromDate()));
//
//        if (!is_null($leavePeriod) && ($leavePeriod instanceof LeavePeriod)) {
//            if ($leaveAssignmentData->getToDate() > $leavePeriod->getEndDate()) {
//                return true;
//            }
//        }

        return false;
    }

    /**
     *
     * @param CompOffParameterObject $compoffAssignmentData
     * @return CompoffRequest 
     */
    protected function generateCompoffRequest(CompOffParameterObject $compoffAssignmentData) {
        $compoffRequest = new CompoffRequest();
        $compoffRequest->setDateApplied($compoffAssignmentData->getDate_applied());
        $compoffRequest->setEmpNumber($compoffAssignmentData->getEmp_number());
        $compoffRequest->setCalledEmpNumber($compoffAssignmentData->getCalled_emp_number());
        $compoffRequest->setComments($compoffAssignmentData->getComments());
        $compoffRequest->setWorkType($compoffAssignmentData->getWorkType());
        return $compoffRequest;
    }

    /**
     *
     * @param int $employeeNumber
     * @return int 
     */
    protected function getWorkShiftDurationForEmployee($empNumber) {

        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);
        return $workSchedule->getWorkShiftLength();
    }

    protected function getApplicableCompoffDuration($compoffAssignmentData, $firstDay, $lastDay) {

        // Default to full day
        $duration = new LeaveDuration();
        $duration->setType(LeaveDuration::FULL_DAY);

        if ($compoffAssignmentData->getMultiDayLeave()) {
            $partialDayOption = $compoffAssignmentData->getMultiDayPartialOption();

            if (($partialDayOption == 'all') ||
                    ($firstDay && ($partialDayOption == 'start' || $partialDayOption == 'start_end'))) {
                $duration = $compoffAssignmentData->getFirstMultiDayDuration();
            } else if ($lastDay && ($partialDayOption == 'end' || $partialDayOption == 'start_end')) {
                $duration = $compoffAssignmentData->getSecondMultiDayDuration();
            }
        } else {
            // Single day leave:
            $duration = $compoffAssignmentData->getSingleDayDuration();
        }

        return $duration;
    }

    protected function updateCompoffDurationParameters(&$compoff, $empNumber, LeaveDuration $duration, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {

        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);
        $workScheduleStartEndTime = $workSchedule->getWorkShiftStartEndTime();
        $workScheduleDuration = $workSchedule->getWorkShiftLength();

        $midDay = $this->addHoursDuration($workScheduleStartEndTime['start_time'], $workScheduleDuration / 2);

        // set status

        switch ($duration->getType()) {
            case LeaveDuration::FULL_DAY:
                $compoff->setDurationType(Leave::DURATION_TYPE_FULL_DAY);
                // For backwards compatibility, set to 00:00
                $compoff->setStartTime('00:00');
                $compoff->setEndTime('00:00');
                break;
            case LeaveDuration::HALF_DAY:

                if ($duration->getAmPm() == LeaveDuration::HALF_DAY_AM) {
                    $compoff->setDurationType(Leave::DURATION_TYPE_HALF_DAY_AM);
                    $compoff->setStartTime($workScheduleStartEndTime['start_time']);
                    $compoff->setEndTime($midDay);
                } else {
                    $compoff->setDurationType(Leave::DURATION_TYPE_HALF_DAY_PM);
                    $compoff->setStartTime($midDay);
                    $compoff->setEndTime($workScheduleStartEndTime['end_time']);
                }
                break;
            case LeaveDuration::SPECIFY_TIME:
                $compoff->setDurationType(Leave::DURATION_TYPE_SPECIFY_TIME);
                $compoff->setStartTime($duration->getFromTime());
                $compoff->setEndTime($duration->getToTime());
                break;
        }

        if ($isWeekend || $isHoliday) {
            // Full Day Off
            $durationInHours = 0;
        } else if ($isHalfday || $isHalfDayHoliday) {

            if ($duration->getType() == LeaveDuration::FULL_DAY) {
                $durationInHours = $workScheduleDuration;
            } else if ($duration->getType() == LeaveDuration::HALF_DAY) {
                $durationInHours = $workScheduleDuration / 2;
            } else {
                $durationInHours = $this->getDurationInHours($duration->getFromTime(), $duration->getToTime());
            }

            $halfDayHours = ($workScheduleDuration / 2);
            if ($durationInHours > $halfDayHours) {
                $durationInHours = $halfDayHours;
            }
            // Half Day Off
        } else {
            // Full Working Day
            if ($duration->getType() == LeaveDuration::FULL_DAY) {
                $durationInHours = $workScheduleDuration;
            } else if ($duration->getType() == LeaveDuration::HALF_DAY) {
                $durationInHours = $workScheduleDuration / 2;
            } else {
                $durationInHours = $this->getDurationInHours($duration->getFromTime(), $duration->getToTime());
            }
        }

        $compoff->setLengthHours(number_format($durationInHours, 2));
        $compoff->setLengthDays(number_format($durationInHours / $workScheduleDuration, 3));
    }

    protected function addHoursDuration($time, $hoursToAdd) {
        list($hours, $minutes) = explode(':', $time);
        $timeInMinutes = (intVal($hours) * 60) + intval($minutes);
        $minutesToAdd = 60 * floatval($hoursToAdd);

        $newMinutes = $timeInMinutes + $minutesToAdd;
        $hoursPart = intval(floor($newMinutes / 60));
        $minutesPart = round($newMinutes) % 60;

        return sprintf("%02d:%02d", $hoursPart, $minutesPart);
    }

    protected function getDurationInHours($fromTime, $toTime) {
        list($startHour, $startMin) = explode(':', $fromTime);
        list($endHour, $endMin) = explode(':', $toTime);

        $durationMinutes = (intVal($endHour) - intVal($startHour)) * 60 + (intVal($endMin) - intVal($startMin));

        $hours = $durationMinutes / 60;

        return $hours;
    }

    /**
     * 
     * Get Compoff array
     * @param CompOffParameterObject $compoffAssignmentData
     * @return array
     */
    public function createCompoffObjectListForAppliedRange(CompOffParameterObject $compoffAssignmentData) {

        $compoffList = array();
        $from = strtotime($compoffAssignmentData->getFromDate());
        $to = strtotime($compoffAssignmentData->getToDate());

        $firstDay = true;

        for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {
            $compoff = new CompOff();

            $compoffDate = date('Y-m-d', $timeStamp);
            $isWeekend = $this->isWeekend($compoffDate, $compoffAssignmentData);
            $isHoliday = $this->isHoliday($compoffDate, $compoffAssignmentData);
            $isHalfday = $this->isHalfDay($compoffDate, $compoffAssignmentData);
            $isHalfDayHoliday = $this->isHalfdayHoliday($compoffDate, $compoffAssignmentData);
            $compoff->setDate($compoffDate);

            $lastDay = ($timeStamp == $to);
            $compoffDuration = $this->getApplicableCompoffDuration($compoffAssignmentData, $firstDay, $lastDay);

            $firstDay = false;

            $this->updateCompoffDurationParameters($compoff, $compoffAssignmentData->getEmp_number(), $compoffDuration, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday);
            $compoff->setStatus($this->getLeaveRequestStatus($isWeekend, $isHoliday, $compoffDate, $compoffAssignmentData));
            $compoff->setEmpNumber($compoffAssignmentData->getEmp_number());
            $compoff->setCompoffRequestDate(date('Y-m-d'));
            array_push($compoffList, $compoff);
        }
        return $compoffList;
    }

    /**
     * Date increment
     * @param int $timestamp
     */
    protected final function incDate($timestamp) {
        return strtotime("+1 day", $timestamp);
    }

    /**
     *
     * @param $day
     * @return boolean
     */
    public function isHalfDay($day, CompOffParameterObject $leaveAssignmentData) {

        $empNumber = $leaveAssignmentData->getEmp_number();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);

        /* This is to check weekday half days */
        $isHalfDay = $workSchedule->isHalfDay($day);

        if (!$isHalfDay) {
            /* This checks for weekend half day */
            $isHalfDay = $workSchedule->isWeekend($day, false);
        }

        return $isHalfDay;
    }

    protected function isWeekend($day, CompOffParameterObject $leaveAssignmentData) {
        $empNumber = $leaveAssignmentData->getEmp_number();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);
        $isWeekend = $workSchedule->isWeekend($day, true);
        return $isWeekend;
    }

    protected function isHoliday($day, CompOffParameterObject $leaveAssignmentData) {
        $empNumber = $leaveAssignmentData->getEmp_number();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);
        $isHoliday = $workSchedule->isHoliday($day);
        return $isHoliday;
    }

    protected function isHalfdayHoliday($day, CompOffParameterObject $leaveAssignmentData) {
        $empNumber = $leaveAssignmentData->getEmp_number();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);
        $isHalfDayHoliday = $workSchedule->isHalfdayHoliday($day);
        return $isHalfDayHoliday;
    }

    /**
     * Calculate Date deference
     * 
     * @param LeaveParameterObject $leaveAssignmentData
     * @param bool $isWeekend
     * @param bool $isHoliday
     * @param bool $isHalfday
     * @param bool $isHalfDayHoliday
     * @return int 
     */
    public function calculateDateDeference(LeaveParameterObject $leaveAssignmentData, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {

        if ($isWeekend) {
            $dayDeference = 0;
        } elseif ($isHoliday) {
            if ($isHalfDayHoliday) {
                if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                    if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                        $dayDeference = 0.5;
                    } else {
                        $dayDeference = number_format($leaveAssignmentData->getLeaveTotalTime() / $leaveAssignmentData->getWorkShiftLength(), 3);
                    }
                } else {
                    $dayDeference = 0.5;
                }
            } else {
                $dayDeference = 0;
            }
        } elseif ($isHalfday) {

            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                    $dayDeference = 0.5;
                } else {
                    $dayDeference = number_format($leaveAssignmentData->getLeaveTotalTime() / $leaveAssignmentData->getWorkShiftLength(), 3);
                }
            } else {
                $dayDeference = 0.5;
            }
        } else {
            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                $dayDeference = number_format($leaveAssignmentData->getLeaveTotalTime() / $leaveAssignmentData->getWorkShiftLength(), 3);
            } else {
                //$dayDeference	=	floor((strtotime($posts['txtToDate'])-strtotime($posts['txtFromDate']))/86400)+1;
                $dayDeference = 1;
            }
        }

        return $dayDeference;
    }

    /**
     *
     * @param LeaveParameterObject $leaveAssignmentData
     * @param bool $isWeekend
     * @param bool $isHoliday
     * @param bool $isHalfday
     * @param bool $isHalfDayHoliday
     * @return int 
     */
    public function calculateTimeDeference(LeaveParameterObject $leaveAssignmentData, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {

        if ($isWeekend) {
            $timeDeference = 0;
        } elseif ($isHoliday) {
            if ($isHalfDayHoliday) {
                if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                    if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                        $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
                    } else {
                        $timeDeference = $leaveAssignmentData->getLeaveTotalTime();
                    }
                } else {
                    $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
                }
            } else {
                $timeDeference = 0;
            }
        } elseif ($isHalfday) {
            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate() && $leaveAssignmentData->getLeaveTotalTime() > 0) {
                if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                    $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
                } else {
                    $timeDeference = $leaveAssignmentData->getLeaveTotalTime();
                }
            } else {
                $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
            }
        } else {
            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                $timeDeference = $leaveAssignmentData->getLeaveTotalTime();
            } else {
                $timeDeference = $this->getWorkShiftLengthOfEmployee($leaveAssignmentData->getEmployeeNumber());
            }
        }

        return $timeDeference;
    }

    /**
     * Get work shift length
     * @return int
     */
    protected function getWorkShiftLengthOfEmployee($employeeNumber) {

        $employeeWorkShift = $this->getEmployeeService()->getEmployeeWorkShift($employeeNumber);

        if (!is_null($employeeWorkShift) && ($employeeWorkShift instanceof EmployeeWorkShift)) {
            return $employeeWorkShift->getWorkShift()->getHoursPerDay();
        } else {
            return WorkShift::DEFAULT_WORK_SHIFT_LENGTH;
        }
    }

}
