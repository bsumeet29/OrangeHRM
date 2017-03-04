<?php

/**
 * Leave Application Service
 * 
 * Functionalities related to leave applying.
 * 
 * @package leave
 * @todo Add license 
 */
class CompoffApplicationService extends AbstractCompoffAllocationService {

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
    public function applyCompoff(CompOffParameterObject $compoffAssignmentData) {


        if ($this->hasOverlapCompoff($compoffAssignmentData)) {
            return false;
        }


        return $this->saveCompoffRequest($compoffAssignmentData);
    }

    protected function saveCompoffRequest(CompOffParameterObject $compoffAssignmentData) {

        $compoffRequest = $this->generateCompoffRequest($compoffAssignmentData);


        $compoffs = $this->createCompoffObjectListForAppliedRange($compoffAssignmentData);

        if (1) {
  $CompoffDays = array();
/*            foreach ($compoffs as $k => $compoff) {
                $CompoffDays[] = $compoff;
            }*/
              $nonHolidayLeaveDays = array();
            $nonHolidayCount = 0;
            $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
            foreach ($compoffs as $k => $compoff) {
             if ($compoff->getStatus() == 0) {
                 $compoff->setLengthHours(8.00);
                 $compoff->setLengthDays(1);
                   $CompoffDays[] = $compoff;
                } else {
                     $nonHolidayCount++;
                }
            }  
            if (count($CompoffDays) > 0) {
                try {
                    $user = sfContext::getInstance()->getUser();
                    $loggedInUserId = $user->getAttribute('auth.userId');
                    $loggedInEmpNumber = $user->getAttribute('auth.empNumber');

                    $compoffRequest = $this->getCompoffRequestService()->saveCompoffRequest($compoffRequest, $compoffs);
                    $compoffComment = trim($compoffRequest->getComments());
                    $compoffTasks = $compoffAssignmentData->getTasks();
                    if (!empty($compoffTasks)) {

                        $this->getCompoffTasksService()->saveCompoffTasks($compoffRequest->getId(), $compoffTasks);
                    }

                    if (!empty($compoffComment)) {
                        if (!empty($loggedInEmpNumber)) {
                            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
                            $createdBy = $employee->getFullName();
                        } else {
                            $createdBy = $user->getAttribute('auth.firstName');
                        }

                        $this->getCompoffRequestService()->saveCompoffRequestComment($compoffRequest->getId(), $compoffComment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                    }


                    //sending compoff apply notification                   

                    $this->_emailNotificationApplyCompoff($compoffRequest, $compoffs);

                    return $compoffRequest;
                } catch (Exception $e) {
                    $this->getLogger()->error('Exception while saving compoff:' . $e);
                    throw new LeaveAllocationServiceException('saving compoff fail....');
                }
            } else {
                throw new LeaveAllocationServiceException('No Holidays in compoff request');
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
    public function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate, CompOffParameterObject $leaveAssignmentData) {
        $status = null;

        if ($isWeekend) {
            //$status = Leave::LEAVE_STATUS_LEAVE_WEEKEND;
            $status = 0;
        }

        if ($isHoliday) {
            //$status = Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
            $status = 0;
        }

        if (is_null($status)) {

            $status = -1;
          /*  $workFlowItem = $this->getWorkflowItemForApplyAction($leaveAssignmentData);

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

    protected function _emailNotificationApplyCompoff(CompoffRequest $compoffRequest, $compoffs) {

        $supervisorEmail = array();
        $employee = $this->getEmployeeService()->getEmployee($compoffRequest->getEmpNumber());
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
        $callbyemployee = $this->getEmployeeService()->getEmployee($compoffRequest->getCalledEmpNumber());
        $supervisorEmail[$callbyemployee->getFullName()] = $callbyemployee->getEmpWorkEmail();

        $replacements = $this->_populateCompoffReplacements($compoffRequest, $compoffs);
        $table = $this->_generateCompoffDetailsTable($replacements);

        $emailService = new EmailService();
        foreach ($supervisorEmail as $name => $email) {
            $self = ($email == $compoffApplicantWorkEmail) ? true : false;
            if ($self) {
                $subject = 'You have applied for Comp off';
            } else {
                $subject = $compoffApplicantFullName . ' has applied for Comp off ';
            }
            $mailMessage = "Hi &nbsp;" . $name . ",<br/><br/>";
            $mailMessage.=($self) ? ' You have' : $compoffApplicantFullName . ' has';
            $mailMessage.=' applied for compoff . PSB the Compoff ​summary:<br /><br />';
            $mailMessage.=$table;
            $mailMessage.='This is an auto-generated email shared with you, since you are marked as a supervisor for this candidate. ​Hence, we would request you to kindly approve/disapprove based on the work status by logging in to below mentioned link to Perennial HRM.<br/><br/>';
            $mailMessage.='http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . '/auth/login<br /><br />';
            $mailMessage .= '<br/>';
            $mailMessage.='Looking forward for your co-operation.<br />';
            $mailMessage .= '--<br />Thanks<br />Team-Perennial';
//            $mailMessage .= '<br/>Admin';
            $emailService->sendEmailNotificationByAction($email, $subject, $mailMessage);
        }
    }

    private function _populateCompoffReplacements(CompoffRequest $compoffRequest, $compoffs) {
        $replacements = array();
        $compoffDate = array();
        $durationType = array();
        foreach ($compoffs as $compoff) {
            $durationType[] = $compoff->getDurationType();
            $compoffDate[] = $compoff->getDate();
        }

        $dayCount = count($durationType);
        $replacements['compoffFromDate'] = $compoffDate[0];
        $replacements['compoffToDate'] = $compoffDate[$dayCount - 1];
        $replacements['comments'] = $compoffRequest->getComments();

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

        $compoffTasks = $compoffRequest->getCompoffTasks();
        $mentionTask = array();
        foreach ($compoffTasks as $task) {
            $mentionTask[] = $task->getTaskName();
        }

        $replacements['tasks'] = $mentionTask;
        $replacements['workType'] = $compoffRequest->getWorkType();
        $employee = $this->getEmployeeService()->getEmployee($compoffRequest->getCalledEmpNumber());
        $replacements['callby'] = $employee->getFullName();
        return $replacements;
    }

    private function _generateCompoffDetailsTable($replacements) {

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
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Called By</th>
                        <td style="border: 1px solid black">' . $replacements['callby'] . '</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Work Type <br />(Urgent Deliverable / Pending task <br /> / Training)</th>
                        <td style="border: 1px solid black">' . $replacements['workType'] . '</td>
                      </tr>
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
