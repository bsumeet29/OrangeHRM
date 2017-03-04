<?php

/**
 * Leave Application Service
 * 
 * Functionalities related to leave applying.
 * 
 * @package leave
 * @todo Add license 
 */
class WfhApplicationService extends AbstractWfhAllocationService {

    protected $leaveEntitlementService;
    protected $dispatcher;
    protected $logger;
    protected $applyWorkflowItem = null;

    /**
     * Get LeaveEntitlementService
     * @return LeaveEntitlementService
     * 
     */
    public function getLeaveEntitlementService() {
        if (!($this->leaveEntitlementService instanceof LeaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     * Set LeaveEntitlementService
     * @param LeaveEntitlementService $service 
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $service) {
        $this->leaveEntitlementService = $service;
    }

    /**
     * Set dispatcher.
     * 
     * @param $dispatcher
     */
    public function setDispatcher($dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher() {
        if (is_null($this->dispatcher)) {
            $this->dispatcher = sfContext::getInstance()->getEventDispatcher();
        }
        return $this->dispatcher;
    }

    /**
     * Creates a new leave application
     * 
     * @param LeaveParameterObject $leaveAssignmentData
     * @return boolean True if leave request is saved else false
     * @throws LeaveAllocationServiceException When leave request length exceeds work shift length. 
     * 
     * @todo Add LeaveParameterObject to the API
     */
    public function applyWfh(WfhParameterObject $wfhAssignmentData) {

        if ($this->hasOverlapWfh($wfhAssignmentData)) {

            return false;
        }

        return $this->saveWfhRequest($wfhAssignmentData);
    }

    protected function saveWfhRequest(WfhParameterObject $wfhAssignmentData) {

        $wfhRequest = $this->generateWfhRequest($wfhAssignmentData);


        $wfhs = $this->createWfhObjectListForAppliedRange($wfhAssignmentData);

        if (1) {
  $wfhDays = array();
              $nonHolidayLeaveDays = array();
            $HolidayCount = 0;
            $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
           /* foreach ($compoffs as $k => $compoff) {
             if (in_array($compoff->getStatus(), $holidays)) {
                   $CompoffDays[] = $compoff;
                } else {
                     $nonHolidayCount++;
                }
            }  */
            foreach ($wfhs as $k => $wfh) {
                if(in_array($wfh->getStatus(), $holidays)){
                     $HolidayCount++;
                }
                else{
                    $wfh->setLengthHours(8.00);
                    $wfh->setLengthDays(1);
                    $wfhDays[] = $wfh;
                }
            }
            if (count($wfhDays) > 0) {
                try {
                    $user = sfContext::getInstance()->getUser();
                    $loggedInUserId = $user->getAttribute('auth.userId');
                    $loggedInEmpNumber = $user->getAttribute('auth.empNumber');
                    $wfhRequest = $this->getWfhRequestService()->saveWfhRequest($wfhRequest, $wfhs);
                    $wfhComment = trim($wfhRequest->getComments());
                    $wfhTasks = $wfhAssignmentData->getTasks();
                    if (!empty($wfhTasks)) {
                        $this->getWfhTasksService()->saveWfhTasks($wfhRequest->getId(), $wfhTasks);
                    }

                    if (!empty($wfhComment)) {
                        if (!empty($loggedInEmpNumber)) {
                            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
                            $createdBy = $employee->getFullName();
                        } else {
                            $createdBy = $user->getAttribute('auth.firstName');
                        }

                        $this->getWfhRequestService()->saveWfhRequestComment($wfhRequest->getId(), $wfhComment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                    }


                    //sending compoff apply notification                   

                    $this->_emailNotificationApplyCompoff($wfhRequest, $wfhs);

                    return $wfhRequest;
                } catch (Exception $e) {
                    $this->getLogger()->error('Exception while saving compoff:' . $e);
                    throw new LeaveAllocationServiceException('saving compoff fail....');
                }
            } else {
                throw new LeaveAllocationServiceException('No Working days in WFH request');
            }
        }

        return false;
    }

    /**
     * Returns leave status based on weekend and holiday
     * 
     * If weekend, returns Leave::LEAVE_STATUS_LEAVE_WEEKEND
     * If holiday, returns Leave::LEAVE_STATUS_LEAVE_HOLIDAY
     * Else, returns LEAVE_STATUS_LEAVE_PENDING_APPROVAL
     * 
     * @param $isWeekend boolean
     * @param $isHoliday boolean
     * @param $leaveDate string 
     * @return status
     * 
     * @todo Check usage of $leaveDate
     * 
     */
    public function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate, WfhParameterObject $leaveAssignmentData) {
        $status = null;

        if ($isWeekend) {
            $status = Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            $status = Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        if (is_null($status)) {

            $status = 0;
/*            $workFlowItem = $this->getWorkflowItemForApplyAction($leaveAssignmentData);

            if (!is_null($workFlowItem)) {
                $status = Leave::getLeaveStatusForText($workFlowItem->getResultingState());
            } else {
                $status = Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;
            }*/
        }

        return $status;
    }

    protected function allowToExceedLeaveBalance() {
        return false;
    }

    protected function getWorkflowItemForApplyAction(LeaveParameterObject $leaveAssignmentData, $action = 'APPLY') {
        if ($action == 'CONFLICT') {
            $this->applyWorkflowItem = null;
        }

        if (is_null($this->applyWorkflowItem)) {

            $empNumber = $leaveAssignmentData->getEmployeeNumber();
            $workFlowItems = $this->getUserRoleManager()->getAllowedActions(WorkflowStateMachine::FLOW_LEAVE, 'INITIAL', array(), array(), array('Employee' => $empNumber));

            // get apply action
            foreach ($workFlowItems as $item) {
                if ($item->getAction() == $action) {
                    $this->applyWorkflowItem = $item;
                    break;
                }
            }
        }

        if (is_null($this->applyWorkflowItem)) {
            $this->getLogger()->error("No workflow item found for APPLY leave action!");
        }

        return $this->applyWorkflowItem;
    }

//    protected function getWorkflowItemForConflictAction(LeaveParameterObject $leaveAssignmentData){
//        
//        if (is_null($this->applyWorkflowItem)) {
//
//            $empNumber = $leaveAssignmentData->getEmployeeNumber();            
//            $workFlowItems = $this->getUserRoleManager()->getAllowedActions(WorkflowStateMachine::FLOW_LEAVE, 
//                    'INITIAL', array(), array(), array('Employee' => $empNumber));
//
//            // get apply action
//            foreach ($workFlowItems as $item) {
//                if ($item->getAction() == 'CONFLICT') {
//                    $this->applyWorkflowItem = $item;
//                    break;
//                }
//            }        
//        }
//        
//        if (is_null($this->applyWorkflowItem)) {
//            $this->getLogger()->error("No workflow item found for CONFLICT leave action!");
//        }
//        
//    }

    /**
     * Is Valid leave request
     * @param LeaveType $leaveType
     * @param array $leaveRecords
     * @returns boolean
     */
    protected function isValidLeaveRequest($leaveRequest, $leaveRecords) {
        $holidayCount = 0;
        $requestedLeaveDays = array();
        $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        foreach ($leaveRecords as $k => $leave) {
            if (in_array($leave->getStatus(), $holidays)) {
                $holidayCount++;
            }
//            $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod(strtotime($leave->getLeaveDate()));
//            if($leavePeriod instanceof LeavePeriod) {
//                $leavePeriodId = $leavePeriod->getLeavePeriodId();
//            } else {
//                $leavePeriodId = null; //todo create leave period?
//            }
//
//            if(key_exists($leavePeriodId, $requestedLeaveDays)) {
//                $requestedLeaveDays[$leavePeriodId] += $leave->getLeaveLengthDays();
//            } else {
//                $requestedLeaveDays[$leavePeriodId] = $leave->getLeaveLengthDays();
//            }
        }

        //if ($this->isLeaveRequestNotExceededLeaveBalance($requestedLeaveDays, $leaveRequest) && $this->hasWorkingDays($holidayCount, $leaveRecords)) {
        return true;
        //}
    }

    /**
     * isLeaveRequestNotExceededLeaveBalance
     * @param array $requestedLeaveDays key => leave period id
     * @param LeaveRequest $leaveRequest
     * @returns boolean
     */
    protected function isLeaveRequestNotExceededLeaveBalance($requestedLeaveDays, $leaveRequest) {

        if (!$this->getLeaveEntitlementService()->isLeaveRequestNotExceededLeaveBalance($requestedLeaveDays, $leaveRequest)) {
            throw new LeaveAllocationServiceException('Failed to Submit: Leave Balance Exceeded');
            return false;
        }
        return true;
    }

    /**
     * hasWorkingDays
     * @param LeaveType $leaveType
     * @returns boolean
     */
    protected function hasWorkingDays($holidayCount, $leaves) {

        if ($holidayCount == count($leaves)) {
            throw new LeaveAllocationServiceException('Failed to Submit: No Working Days Selected');
        }

        return true;
    }

    /**
     *
     * @return Employee
     * @todo Remove the use of session
     */
    public function getLoggedInEmployee() {
        $employee = $this->getEmployeeService()->getEmployee($_SESSION['empNumber']);
        return $employee;
    }

    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected function getLogger() {
        if (is_null($this->logger)) {
            $this->logger = Logger::getLogger('leave.LeaveApplicationService');
        }

        return($this->logger);
    }

    protected function _emailNotificationApplyCompoff(WfhRequest $wfhRequest, $wfhs) {

        $supervisorEmail = array();
        $employee = $this->getEmployeeService()->getEmployee($wfhRequest->getEmpNumber());
        $compoffApplicantWorkEmail = $employee->getEmpWorkEmail();
        $compoffApplicantFullName = $employee->getFullName();
        $supervisors = $employee->getSupervisors();
        //add compoff applicant email
        $supervisorEmail[$employee->getFullName()] = $compoffApplicantWorkEmail;

        //add supervisors email
        foreach ($supervisors as $supervisor) {
            $supervisorEmail[$supervisor->getFullName()] = $supervisor->getEmpWorkEmail();
        }
        //add call by email
       //$callbyemployee = $this->getEmployeeService()->getEmployee($wfhRequest->getCalledEmpNumber());
       // $supervisorEmail[$callbyemployee->getFullName()] = $callbyemployee->getEmpWorkEmail();

        $replacements = $this->_populateWfhReplacements($wfhRequest, $wfhs);
        $table = $this->_generateWfhDetailsTable($replacements);

        $emailService = new EmailService();
        foreach ($supervisorEmail as $name => $email) {
            $self = ($email == $compoffApplicantWorkEmail) ? true : false;
            if ($self) {
                $subject = 'You have applied for WFH';
            } else {
                $subject = $compoffApplicantFullName . ' has applied for WFH';
            }
            $mailMessage = "Hi &nbsp;" . $name . ",<br/><br/>";
            $mailMessage.=($self) ? ' You have' : $compoffApplicantFullName . ' has';
            $mailMessage.=' applied for WFH . The WFH details:<br /><br />';
            $mailMessage.=$table;
            $mailMessage.='Sign in to Perennial Systems HRM with the following link to take action on WFH application<br/><br/>';
            $mailMessage.='http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . '/auth/login<br /><br />';
            $mailMessage .= '<br/>';
            $mailMessage .= '<br/>Best regards,<br/>';
            $mailMessage .= '<br/>Admin';
            $emailService->sendEmailNotificationByAction($email, $subject, $mailMessage);
        }
    }

    private function _populateWfhReplacements(WfhRequest $wfhRequest, $wfhs) {
        $replacements = array();
        $compoffDate = array();
        $durationType = array();
        foreach ($wfhs as $wfh) {
            $durationType[] = $wfh->getDurationType();
            $compoffDate[] = $wfh->getDate();
        }

        $dayCount = count($durationType);
        $replacements['compoffFromDate'] = $compoffDate[0];
        $replacements['compoffToDate'] = $compoffDate[$dayCount - 1];
        $replacements['comments'] = $wfhRequest->getComments();
        $replacements['contact'] = $wfhRequest->getContactNo();

        if ($durationType[0] == 0 || $durationType[0] == 1) {
            $replacements['fromSession'] = "Session 1";
        } else {
            $replacements['fromSession'] = "Session 2";
        }
        if ($durationType[$dayCount - 1] == 0 || $durationType[$dayCount - 1] == 2) {
            $replacements['toSession'] = "Session 2";
        } else {
            $replacements['toSession'] = "Session 1";
        }

        $compoffTasks = $wfhRequest->getWfhTasks();
        $mentionTask = array();
        foreach ($compoffTasks as $task) {
            $mentionTask[] = $task->getTaskName();
        }

        $replacements['tasks'] = $mentionTask;
       // $replacements['workType'] = $wfhRequest->getWorkType();
       // $employee = $this->getEmployeeService()->getEmployee($wfhRequest->getCalledEmpNumber());
       // $replacements['callby'] = $employee->getFullName();
        return $replacements;
    }

    private function _generateWfhDetailsTable($replacements) {

        $table = '<html><body><br /><br /><table style="width:100%;border-collapse: collapse;">
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">From Date</th>
                        <td style="border: 1px solid black">' . $replacements['compoffFromDate'] . '</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">To Date</th>
                        <td style="border: 1px solid black">' . $replacements['compoffToDate'] . '</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">From Session</th>
                        <td style="border: 1px solid black">' . $replacements['fromSession'] . '</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">To Session</th>
                        <td style="border: 1px solid black">' . $replacements['toSession'] . '</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Reason</th>
                        <td style="border: 1px solid black">' . $replacements['comments'] . '</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Contact Details during absence</th>
                        <td style="border: 1px solid black">' . $replacements['contact'] . '</td>
                      </tr>
                     <!--<tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Called By</th>
                        <td style="border: 1px solid black">' . $replacements['callby'] . '</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Work Type <br />(Urgent Deliverable / Pending task <br /> / Training)</th>
                        <td style="border: 1px solid black">' . $replacements['workType'] . '</td>
                      </tr>-->
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Expected tasks to be completed <br />for the above day(s)</th>
                        <td style="border: 1px solid black">';

        foreach ($replacements['tasks'] as $key => $task) {
            $table .='<b>Task ' . ($key + 1) . ': </b>' . $task . '<br /><br />';
        }
        $table .= '</td></tr></table><br /><br /></body></html>';
        return $table;
    }

}
