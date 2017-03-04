<?php

class LeaveParameterObject {

    protected $employeeNumber;
    protected $fromDate;
    protected $toDate;
    protected $fromTime = '';
    protected $toTime = '';
    protected $leaveType;
    protected $leaveTotalTime;
    protected $comment;
    protected $contactNo;
    protected $workShiftLength;
    protected $multiDayLeave;
    protected $multiDayPartialOption;
    protected $singleDayDuration;
    protected $firstMultiDayDuration;
    protected $secondMultiDayDuration;
    protected $contactPersons;

    public function getContactPersons() {
        return $this->contactPersons;
    }

    public function setContactPersons($contactPersons) {
        $this->contactPersons = $contactPersons;
    }

    public function __construct(array $formParameters) {
        $contactPersons = array();
        $this->employeeNumber = $formParameters['txtEmpID']; // TODO: Make this employee number
        $this->fromDate = $formParameters['txtFromDate'];
        $this->toDate = $formParameters['txtToDate'];
        $this->fromTime = $formParameters['txtFromTime'];
        $this->toTime = $formParameters['txtToTime'];
        $this->leaveType = $formParameters['txtLeaveType'];
        $this->leaveTotalTime = $formParameters['txtLeaveTotalTime'];
        $this->comment = $formParameters['txtComment'];
        $this->contactNo=$formParameters['txtContact'];
        $this->workShiftLength = $formParameters['txtEmpWorkShift'];
        $this->multiDayLeave = $this->fromDate != $this->toDate;
        $this->multiDayPartialOption = $formParameters['partialDays'];
        $this->singleDayDuration = $this->getLeaveDurationObject($formParameters['duration']);
        $this->firstMultiDayDuration = $this->getLeaveDurationObject($formParameters['firstDuration']);
        $this->secondMultiDayDuration = $this->getLeaveDurationObject($formParameters['secondDuration']);
        if (isset($formParameters['txtTask'])) {
            $this->task = $formParameters['txtTask'];
        }
        if (isset($formParameters['txtContactPerson']["empId"])) {
            $this->alternateContactPerson = $formParameters['txtContactPerson']["empId"];
        }
        for($i = 0;$i <= 6;$i++){
            if(!(empty($formParameters['txtPreviousTask_' .$i])) && !(empty($formParameters['txtContactPerson_' . $i]['empId']))){
                $contactPersons[] = array('empId' => $formParameters['txtContactPerson_' . $i]["empId"],
                    'empName' => $formParameters['txtContactPerson_'.$i]["empName"],
                    'task' => $formParameters['txtPreviousTask_' . $i]);
            }
        }
        if (!(empty($formParameters['txtTask'])) && !(empty($formParameters['txtContactPerson']['empId']))) {
            $contactPersons[] = array('empId' => $formParameters['txtContactPerson']["empId"],
                'empName' => $formParameters['txtContactPerson']["empName"],
                'task' => $formParameters['txtTask']
            );
        }

        
        for ($i = 1; $i <= 5; $i++) {

            if (!(empty($formParameters['txtTask' . $i])) && !(empty($formParameters['txtContactPerson' . $i]['empId']))) {
                $contactPersons[] = array('empId' => $formParameters['txtContactPerson' . $i]['empId'],
                    'empName' => $formParameters['txtContactPerson' . $i]['empName'],
                    'task' => $formParameters['txtTask' . $i]);
            }
        }
        if (!empty($contactPersons)) {
            $this->contactPersons = $contactPersons;
        }
    }

    protected function getLeaveDurationObject($parameters) {
        $durationObj = new LeaveDuration();
        $type = $parameters['duration'];
        $durationObj->setType($type);
        if ($type == LeaveDuration::HALF_DAY) {
            $durationObj->setAmPm($parameters['ampm']);
        } else if ($type == LeaveDuration::SPECIFY_TIME) {
            $durationObj->setFromTime($parameters['time']['from']);
            $durationObj->setToTime($parameters['time']['to']);
        }
        return $durationObj;
    }

    public function isMultiDayLeave() {
        return $this->multiDayLeave;
    }

    public function getMultiDayPartialOption() {
        return $this->multiDayPartialOption;
    }

    public function getSingleDayDuration() {
        return $this->singleDayDuration;
    }

    public function getFirstMultiDayDuration() {
        return $this->firstMultiDayDuration;
    }

    public function getSecondMultiDayDuration() {
        return $this->secondMultiDayDuration;
    }

    public function getEmployeeNumber() {
        return $this->employeeNumber;
    }

    public function setEmployeeNumber($employeeNumber) {
        $this->employeeNumber = $employeeNumber;
    }

    public function getFromDate() {
        return $this->fromDate;
    }

    public function setFromDate($fromDate) {
        $this->fromDate = $fromDate;
    }

    public function getToDate() {
        return $this->toDate;
    }

    public function setToDate($toDate) {
        $this->toDate = $toDate;
    }

    public function getFromTime() {
        return $this->fromTime;
    }

    public function setFromTime($fromTime) {
        $this->fromTime = $fromTime;
    }

    public function getToTime() {
        return $this->toTime;
    }

    public function setToTime($toTime) {
        $this->toTime = $toTime;
    }

    public function getLeaveType() {
        return $this->leaveType;
    }

    public function setLeaveType($leaveType) {
        $this->leaveType = $leaveType;
    }

    public function getLeaveTotalTime() {
        return $this->leaveTotalTime;
    }

    public function setLeaveTotalTime($leaveTotalTime) {
        $this->leaveTotalTime = $leaveTotalTime;
    }

    public function getComment() {
        return $this->comment;
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public function getWorkShiftLength() {
        return $this->workShiftLength;
    }

    public function setWorkShiftLength($workShiftLength) {
        $this->workShiftLength = $workShiftLength;
    }

    public function setTask($task) {
        $this->task = $task;
    }

    public function getTask() {
        return $this->task;
    }

    public function setAlternateContactPerson($alternateContactPerson) {
        $this->alternateContactPerson = $alternateContactPerson[empId];
    }

    public function getAlternateContactPerson() {
        return $this->alternateContactPerson;
    }

    public function getContactNo() {
        return $this->contactNo;
    }

    public function setContactNo($contactNo) {
        $this->contactNo = $contactNo;
    }

}
