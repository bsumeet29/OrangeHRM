<?php

/**
 * Leave Application Service
 * 
 * Functionalities related to leave applying.
 * 
 * @package leave
 * @todo Add license 
 */

class LeaveApplicationService extends AbstractLeaveAllocationService {

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
        if(is_null($this->dispatcher)) {
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
    public function applyLeave(LeaveParameterObject $leaveAssignmentData) {
	
        if ($this->hasOverlapLeave($leaveAssignmentData)) {
            return false;
        }
    	         
        if ($this->applyMoreThanAllowedForADay($leaveAssignmentData)) {
            throw new LeaveAllocationServiceException('Failed to Submit: Work Shift Length Exceeded');
        }

        return $this->saveLeaveRequest($leaveAssignmentData);        
    }

    /**
     * Saves Leave Request and Sends Email Notification
     * 
     * @param LeaveParameterObject $leaveAssignmentData 
     * @return boolean True if leave request is saved else false
     * @throws LeaveAllocationServiceException
     * 
     * @todo Don't catch general Exception. Catch specific one.
     */
    protected function saveLeaveRequest(LeaveParameterObject $leaveAssignmentData) {
       $leaveRequest = $this->generateLeaveRequest($leaveAssignmentData);
     
        $leaveType = $this->getLeaveTypeService()->readLeaveType($leaveAssignmentData->getLeaveType());
        // TODO: Move into if block
        $leaves = $this->createLeaveObjectListForAppliedRange($leaveAssignmentData);
        if ($this->isEmployeeAllowedToApply($leaveType)) {
            
            $nonHolidayLeaveDays = array();
            $startDay = reset($leaves);
            $endDay = end($leaves);
            $holidayCount = 0;
            $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
          foreach ($leaves as $k => $leave) {
              if(($startDay->getStatus() == 1 && $endDay->getStatus() == 1) && ($leaveType->getId()=='3')&&(in_array($leave->getStatus(), $holidays))){
                    //echo"hii";exit;
                    $leave->setStatus(1);
                    $leave->setLengthHours(8.00);
                    $leave->setLengthDays(1);
                }
                if (in_array($leave->getStatus(), $holidays)) {
                    $holidayCount++;
                } else {
                    $nonHolidayLeaveDays[] = $leave;
                }
            }
            if (count($nonHolidayLeaveDays) > 0) {
                $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy(); 
                $employee = $this->getLoggedInEmployee();
                $empNumber = $employee->getEmpNumber();
                $entitlements = $strategy->handleLeaveCreate($empNumber, $leaveType->getId(), $nonHolidayLeaveDays, false);
                if (!$this->allowToExceedLeaveBalance() && $entitlements == false) {
                     if($leaveType->getId()!= '3') { 
                        // if it is not a NPL                        
                        throw new LeaveAllocationServiceException('Leave Balance Exceeded');
                    }
                }
            }            
        
            if ($holidayCount != count($leaves)) {
                try {
                    $user = sfContext::getInstance()->getUser();
                    $loggedInUserId = $user->getAttribute('auth.userId');
                    $loggedInEmpNumber = $user->getAttribute('auth.empNumber');
        
                    $leaveRequest = $this->getLeaveRequestService()->saveLeaveRequest($leaveRequest, $leaves, $entitlements);
                    $leaveComment = trim($leaveRequest->getComments());
                   
                    $contactPersons=$leaveRequest->getContactPersons();
                   
//                    if(!(empty($contactPersons))){
//                        print_r($contactPersons);
//                    }
                    
                   
                     if(!empty($contactPersons)){
                      $this->getLeaveTasksService()->saveLeaveTasks($leaveRequest->getId(), $contactPersons);
                      $subordinateLeaveData = $this->getLeaveRequestService()->searchSubordinateLeave($leaveRequest,$contactPersons);
                    }
           
                    if (!empty($leaveComment)) {                                                       
                        if (!empty($loggedInEmpNumber)) {
                            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
                            $createdBy = $employee->getFullName();
                        } else {
                            $createdBy = $user->getAttribute('auth.firstName');
                        }
                        
                        $this->getLeaveRequestService()->saveLeaveRequestComment($leaveRequest->getId(), 
                                $leaveComment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                    }
                    
                    
//                    if ($this->isOverlapLeaveRequest($leaveAssignmentData)) {
//                        $this->getLeaveRequestService()->modifyOverlapLeaveRequest($leaveRequest, $leaves);
//                    }
                        
                    //sending leave apply notification                   
                    $workFlow = $this->getWorkflowItemForApplyAction($leaveAssignmentData ,'APPLY');
                    $employee = $this->getLoggedInEmployee();
                    $eventData = array('request' => $leaveRequest, 'days' => $leaves, 'empNumber' => $employee->getEmpNumber(),
                        'workFlow' => $workFlow,'contactPersons'=>$contactPersons);
                    $this->getDispatcher()->notify(new sfEvent($this, LeaveEvents::LEAVE_CHANGE, $eventData));
                   
                    if($subordinateLeaveData != null){
                        $workFlow1= $this->getWorkflowItemForApplyAction($leaveAssignmentData,'CONFLICT');
                        $eventData = array('request' => $leaveRequest, 'days' => $leaves, 'empNumber' => $employee->getEmpNumber(),
                            'workFlow' => $workFlow1,'conflictLeaveData'=>$subordinateLeaveData);
                        $this->getDispatcher()->notify(new sfEvent($this, LeaveEvents::LEAVE_CHANGE, $eventData));
                    }
                    return $leaveRequest;
                } catch (Exception $e) {
                    
                    $this->getLogger()->error('Exception while saving leave:' . $e);
                    throw new LeaveAllocationServiceException('Leave Quota will Exceed....');
                }
            } else {
                throw new LeaveAllocationServiceException('No working days in leave request');
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
    public function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate, LeaveParameterObject $leaveAssignmentData) {

        $status = null;
        
        if ($isWeekend) {
            $status = Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            $status = Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        if (is_null($status)) {
            
            $workFlowItem = $this->getWorkflowItemForApplyAction($leaveAssignmentData);
            
            if (!is_null($workFlowItem)) {
                $status = Leave::getLeaveStatusForText($workFlowItem->getResultingState());
            } else {                
                $status = Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;     
            }
        }

        return $status;
    }
    
    protected function allowToExceedLeaveBalance() {
        return false;
    }
    
    protected function getWorkflowItemForApplyAction(LeaveParameterObject $leaveAssignmentData,$action='APPLY') {
        if($action == 'CONFLICT'){
            $this->applyWorkflowItem=null;
        }
        
        if (is_null($this->applyWorkflowItem)) {

            $empNumber = $leaveAssignmentData->getEmployeeNumber();            
            $workFlowItems = $this->getUserRoleManager()->getAllowedActions(WorkflowStateMachine::FLOW_LEAVE, 
                    'INITIAL', array(), array(), array('Employee' => $empNumber));

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

}