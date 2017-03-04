<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CompOffParameterObject
 *
 * @author firoj
 */
class WfhParameterObject {

    const URGENT_WORK = 'Urgent Deliverable';
    const PENGIND_WORK = 'Pending task';
    const TRAINING_WORK = 'Training';

   // protected $called_emp_number;
    protected $fromDate;
    protected $toDate;
    protected $fromTime = '';
    protected $toTime = '';
    protected $leaveTotalTime;
    protected $comments;
    protected $contact;
    protected $workShiftLength;
    protected $multiDayLeave;
    protected $multiDayPartialOption;
    protected $singleDayDuration;
    protected $firstMultiDayDuration;
    protected $secondMultiDayDuration;
    protected $tasks;
  //  protected $workType;
    protected $date_applied;
    protected $emp_number;

    public function __construct(array $formParameters) {
       // $this->called_emp_number = $formParameters['txtEmpID']; // TODO: Make this employee number
        $this->fromDate = $formParameters['txtFromDate'];
        $this->toDate = $formParameters['txtToDate'];
        $this->fromTime = $formParameters['txtFromTime'];
        $this->toTime = $formParameters['txtToTime'];
        $this->leaveTotalTime = $formParameters['txtLeaveTotalTime'];
        $this->comments = $formParameters['txtComment'];
        $this->contact = $formParameters['txtContact'];
        $this->workShiftLength = $formParameters['txtEmpWorkShift'];
        $this->multiDayLeave = $this->fromDate != $this->toDate;
        $this->multiDayPartialOption = $formParameters['partialDays'];
        $this->singleDayDuration = $this->getLeaveDurationObject($formParameters['duration']);
        $this->firstMultiDayDuration = $this->getLeaveDurationObject($formParameters['firstDuration']);
        $this->secondMultiDayDuration = $this->getLeaveDurationObject($formParameters['secondDuration']);
       /* $workType = $formParameters['txtWorkType'];
        if ($workType != 0) {
            if ($workType == 1) {
                $this->workType = CompOffParameterObject::URGENT_WORK;
            } elseif ($workType == 2) {
                $this->workType = CompOffParameterObject::PENGIND_WORK;
            } else {
                $this->workType = CompOffParameterObject::TRAINING_WORK;
            }
        }*/
        $this->date_applied = date("Y-m-d");

        // $loggedInUserId = $user->getAttribute('auth.userId');
        $loggedInEmpNumber = $this->getEmployeeNumber();
        $this->emp_number = $loggedInEmpNumber;

        $mensionTask = array();
        foreach ($formParameters['tasks']as $task) {
            if (!empty($task) && $task != '') {
                $mensionTask[] = $task;
            }
        }
        $this->tasks = $mensionTask;
    }

    private function getEmployeeNumber() {
        return $_SESSION['empNumber'];
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

    public function getCalled_emp_number() {
        return $this->called_emp_number;
    }

    public function getFromDate() {
        return $this->fromDate;
    }

    public function getToDate() {
        return $this->toDate;
    }

    public function getFromTime() {
        return $this->fromTime;
    }

    public function getToTime() {
        return $this->toTime;
    }

    public function getLeaveTotalTime() {
        return $this->leaveTotalTime;
    }

    public function getComments() {
        return $this->comments;
    }

    public function getWorkShiftLength() {
        return $this->workShiftLength;
    }

    public function getMultiDayLeave() {
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

    public function getTasks() {
        return $this->tasks;
    }

    public function getWorkType() {
        return $this->workType;
    }

    public function getDate_applied() {
        return $this->date_applied;
    }
    public function getContact(){
        return $this->contact;
    }

    public function getEmp_number() {
        return $this->emp_number;
    }
    public function setContact($contact){
        $this->contact = $contact;
    }
    public function setCalled_emp_number($called_emp_number) {
        $this->called_emp_number = $called_emp_number;
    }

    public function setFromDate($fromDate) {
        $this->fromDate = $fromDate;
    }

    public function setToDate($toDate) {
        $this->toDate = $toDate;
    }

    public function setFromTime($fromTime) {
        $this->fromTime = $fromTime;
    }

    public function setToTime($toTime) {
        $this->toTime = $toTime;
    }

    public function setLeaveTotalTime($leaveTotalTime) {
        $this->leaveTotalTime = $leaveTotalTime;
    }

    public function setComments($comments) {
        $this->comments = $comments;
    }

    public function setWorkShiftLength($workShiftLength) {
        $this->workShiftLength = $workShiftLength;
    }

    public function setMultiDayLeave($multiDayLeave) {
        $this->multiDayLeave = $multiDayLeave;
    }

    public function setMultiDayPartialOption($multiDayPartialOption) {
        $this->multiDayPartialOption = $multiDayPartialOption;
    }

    public function setSingleDayDuration($singleDayDuration) {
        $this->singleDayDuration = $singleDayDuration;
    }

    public function setFirstMultiDayDuration($firstMultiDayDuration) {
        $this->firstMultiDayDuration = $firstMultiDayDuration;
    }

    public function setSecondMultiDayDuration($secondMultiDayDuration) {
        $this->secondMultiDayDuration = $secondMultiDayDuration;
    }

    public function setTasks($tasks) {
        $this->tasks = $tasks;
    }

    public function setWorkType($workType) {
        $this->workType = $workType;
    }

    public function setDate_applied($date_applied) {
        $this->date_applied = $date_applied;
    }

    public function setEmp_number($emp_number) {
        $this->emp_number = $emp_number;
    }



}

?>
