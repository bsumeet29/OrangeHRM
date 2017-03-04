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
class WfhRequestService extends BaseService {

    private $wfhRequestDao ;
    private $leaveRequestDao ;
    private $leaveTypeService;
    private $leaveEntitlementService;
    private $leavePeriodService;
    private $holidayService;
    private $accessFlowStateMachineService;        
    private $leaveStateManager;
    private $userRoleManager;
    


    private $dispatcher;

    const LEAVE_CHANGE_TYPE_LEAVE = 'change_leave';
    const LEAVE_CHANGE_TYPE_LEAVE_REQUEST = 'change_leave_request';
    
    

    
    /**
     *
     * @return WfhRequestDao
     */
    public function getWfhRequestDao() {
            $this->wfhRequestDao = new WfhRequestDao();
        return $this->wfhRequestDao;
    }
    

    /**
     *
     * @param LeaveRequestDao $leaveRequestDao
     * @return void
     */

      public function getLeaveRequestDao() {
        if (!($this->leaveRequestDao instanceof LeaveRequestDao)) {
            $this->leaveRequestDao = new LeaveRequestDao();
        }
        return $this->leaveRequestDao;
    }
    public function setWfhRequestDao(WfhRequestDao $wfhRequestDao) {
        $this->wfhRequestDao = $wfhRequestDao;
    }

        
    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        if(is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     * @return LeaveTypeService
     */
    public function getLeaveTypeService() {
        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     * Sets LeaveEntitlementService
     * @param LeaveEntitlementService $leaveEntitlementService
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    /**
     * Sets LeaveTypeService
     * @param LeaveTypeService $leaveTypeService
     */
    public function setLeaveTypeService(LeaveTypeService $leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }

    /**
     * Returns LeavePeriodService
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        if(is_null($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
            $this->leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
        }
        return $this->leavePeriodService;
    }

    /**
     * Sets LeavePeriodService
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * Returns HolidayService
     * @return HolidayService
     */
    public function getHolidayService() {
        if(is_null($this->holidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * Sets HolidayService
     * @param HolidayService $holidayService
     */
    public function setHolidayService(HolidayService $holidayService) {
        $this->holidayService = $holidayService;
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
    
    public function getAccessFlowStateMachineService() {
        if (is_null($this->accessFlowStateMachineService)) {
            $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        }
        return $this->accessFlowStateMachineService;
    }

    public function setAccessFlowStateMachineService($accessFlowStateMachineService) {
        $this->accessFlowStateMachineService = $accessFlowStateMachineService;
    }
     public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
     }
    /**
     *
     * @param CompoffRequest $compoffRequest
     * @param Compoff $compoffs
     * @return boolean
     */
    public function saveWfhRequest(WfhRequest $wfhRequest , $wfhs) {
        return $this->getWfhRequestDao()->saveWfhRequest($wfhRequest, $wfhs);
    }
    
    public function saveWfhRequestComment($wfhRequestId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber) {
        return $this->getWfhRequestDao()->saveWfhRequestComment($wfhRequestId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
    }
    public function searchSubordinateLeave($leaveRequest,$contactPersons){
        return $this->getLeaveRequestDao()->searchSubordinateLeave($leaveRequest,$contactPersons);
    }

    public function saveWfhComment($wfhId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber) {
        return $this->getWfhRequestDao()->saveWfhComment($wfhId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
    }
    
    public function getWfhRequestComments($wfhRequestId) {
        return $this->getWfhRequestDao()->getWfhRequestComments($wfhRequestId);
    }

    public function getWfhComments($leaveId) {
        return $this->getWfhRequestDao()->getWfhComments($wfhId);
    }    
    
    /**
     *
     * @param Employee $employee
     * @return LeaveType Collection
     */
    public function getEmployeeAllowedToApplyLeaveTypes(Employee $employee) {

        try {
            $leaveEntitlementService    = $this->getLeaveEntitlementService();                $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();     

            $leaveTypeService           = $this->getLeaveTypeService();	             

            $leaveTypes     = $leaveTypeService->getLeaveTypeList();
            $nplLeave = $this->getLeaveTypeService()->readLeaveType(3);
            
            $leaveTypeList  = array();

            foreach($leaveTypes as $leaveType) {
                $balance = $leaveEntitlementService->getLeaveBalance($employee->getEmpNumber(), $leaveType->getId());                     
               
                if($balance->getEntitled() > 0 ) {                
                     if($leaveType->getId() == '4' ) {
                        if( $balance->getBalance() > 0 ) {
                            unset($leaveTypeList);
                            $leaveTypeList[0] = $leaveType;
                            $leaveTypeList[1] = $nplLeave;
                            return $leaveTypeList;
                        } 
                    } else { 
                        if( $balance->getBalance() > 0 ) {
                            array_push($leaveTypeList, $leaveType);
                        }
                    }
                    
                }
            }
            // NPL will always be available to apply
            array_push($leaveTypeList, $nplLeave);     
            return $leaveTypeList;
        } catch(Exception $e) {
            throw new LeaveServiceException($e->getMessage());
        }
    }
    public function getDetails(){
        
        return 11;
    }

    /**
     *
     * @param date $leaveStartDate
     * @param date $leaveEndDate
     * @param int $empId
     * @return Leave List
     * @todo Parameter list is too long. Refactor to use LeaveParameterObject
     */
    public function getOverlappingWfh($wfhStartDate, $wfhEndDate ,$empId, $startTime = '00:00', $endTime='59:00', $hoursPerday = '8') {

        return $this->getWfhRequestDao()->getOverlappingWfh($wfhStartDate, $wfhEndDate ,$empId,  $startTime, $endTime, $hoursPerday);

    }

    /**
     *
     * @param LeaveType $leaveType
     * @return boolean
     */
    public function isApplyToMoreThanCurrent(LeaveType $leaveType){
		try{
			$leaveRuleEligibilityProcessor	=	new LeaveRuleEligibilityProcessor();
			return $leaveRuleEligibilityProcessor->allowApplyToMoreThanCurrent($leaveType);

		}catch( Exception $e){
			throw new LeaveServiceException($e->getMessage());
		}
	}

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return int
     */
    public function getNumOfLeave($empId, $leaveTypeId) {

        return $this->getLeaveRequestDao()->getNumOfLeave($empId, $leaveTypeId);

    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId 
     * @param $$leavePeriodId
     * @return int
     */
    public function getNumOfAvaliableLeave($empId, $leaveTypeId, $leavePeriodId = null) {
        
        return $this->getLeaveRequestDao()->getNumOfAvaliableLeave($empId, $leaveTypeId, $leavePeriodId);
        
    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return bool
     */
    public function isEmployeeHavingLeaveBalance( $empId, $leaveTypeId ,$leaveRequest,$applyDays) {
        try {
            $leaveEntitlementService = $this->getLeaveEntitlementService();
            $entitledDays	=	$leaveEntitlementService->getEmployeeLeaveEntitlementDays($empId, $leaveTypeId,$leaveRequest->getLeavePeriodId());
            $leaveDays		=	$this->getLeaveRequestDao()->getNumOfAvaliableLeave($empId, $leaveTypeId);

            $leaveEntitlement = $leaveEntitlementService->readEmployeeLeaveEntitlement($empId, $leaveTypeId, $leaveRequest->getLeavePeriodId());
            $leaveBoughtForward = 0;
            if($leaveEntitlement instanceof EmployeeLeaveEntitlement) {
                $leaveBoughtForward = $leaveEntitlement->getLeaveBroughtForward();
            }

            $leaveBalance = $leaveEntitlementService->getLeaveBalance(
                    $empId, $leaveTypeId,
                    $leaveRequest->getLeavePeriodId());

            $entitledDays += $leaveBoughtForward;

            if($entitledDays == 0)
                throw new Exception('Leave Entitlements Not Allocated',102);

            //this is for border period leave apply - days splitting
            $leavePeriodService = $this->getLeavePeriodService();

            //this would either create or returns the next leave period
            $currentLeavePeriod     = $leavePeriodService->getLeavePeriod(strtotime($leaveRequest->getDateApplied()));
            $leaveAppliedEndDateTimeStamp = strtotime("+" . $applyDays . " day", strtotime($leaveRequest->getDateApplied()));
            $nextLeavePeriod        = $leavePeriodService->createNextLeavePeriod(date("Y-m-d", $leaveAppliedEndDateTimeStamp));
            $currentPeriodStartDate = explode("-", $currentLeavePeriod->getStartDate());
            $nextYearLeaveBalance   = 0;

            if($nextLeavePeriod instanceof LeavePeriod) {
                $nextYearLeaveBalance = $leaveEntitlementService->getLeaveBalance(
                        $empId, $leaveTypeId,
                        $nextLeavePeriod->getLeavePeriodId());
                //this is to notify users are applying to the same leave period
                $nextPeriodStartDate    = explode("-", $nextLeavePeriod->getStartDate());
                if($nextPeriodStartDate[0] == $currentPeriodStartDate[0]) {
                    $nextLeavePeriod        = null;
                    $nextYearLeaveBalance   = 0;
                }
            }

            //this is only applicable if user applies leave during current leave period
            if(strtotime($currentLeavePeriod->getStartDate()) < strtotime($leaveRequest->getDateApplied()) &&
                    strtotime($currentLeavePeriod->getEndDate()) > $leaveAppliedEndDateTimeStamp) {
                if($leaveBalance < $applyDays) {
                    throw new Exception('Leave Balance Exceeded',102);
                }
            }

            //this is to verify whether leave applied within border period
            if($nextLeavePeriod instanceof LeavePeriod && strtotime($currentLeavePeriod->getStartDate()) < strtotime($leaveRequest->getDateApplied()) &&
                    strtotime($nextLeavePeriod->getEndDate()) > $leaveAppliedEndDateTimeStamp) {

                $endDateTimeStamp = strtotime($leavePeriodService->getCurrentLeavePeriod()->getEndDate());
                $borderDays = date("d", ($endDateTimeStamp - strtotime($leaveRequest->getDateApplied())));
                if($borderDays > $leaveBalance || $nextYearLeaveBalance < ($applyDays - $borderDays)) {
                    throw new Exception("Leave Balance Exceeded", 102);
                }
            }

            return true ;

        }catch( Exception $e) {
            throw new LeaveServiceException($e->getMessage());
        }
    }

    public function isLeaveRequestWithinLeaveBalance($employeeId, $leaveTypeId, $leaveList) {

        $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
        $currentLeavePeriodEndDate = $currentLeavePeriod->getEndDate();
        $currentLeavePeriodEndDateTimeStamp = strtotime($currentLeavePeriodEndDate);

        $leaveEntitlementService = $this->getLeaveEntitlementService();

        $leaveLengthOnCurrentLeavePeriod = 0;
        $leaveLengthOnNextLeavePeriod = 0;

        $canApplyForCurrentLeavePeriod = true;
        $canApplyForNextLeavePeriod = true;

        foreach ($leaveList as $leave) {

            if (strtotime($leave->getLeaveDate()) <= $currentLeavePeriodEndDateTimeStamp) {

                $leaveLengthOnCurrentLeavePeriod += $leave->getLeaveLengthDays();

            } else {

                $leaveLengthOnNextLeavePeriod += $leave->getLeaveLengthDays();

            }

        }

        if ($leaveLengthOnCurrentLeavePeriod > 0) {

            $currentLeaveBalance = $leaveEntitlementService->getLeaveBalance($employeeId, $leaveTypeId, $currentLeavePeriod->getLeavePeriodId());

            if ($leaveLengthOnCurrentLeavePeriod > $currentLeaveBalance) {

                $canApplyForCurrentLeavePeriod = false;

            }

        }

        if ($leaveLengthOnNextLeavePeriod > 0) {

            $nextLeavePeriod = $this->getLeavePeriodService()->getNextLeavePeriodByCurrentEndDate($currentLeavePeriodEndDate);

            if ($nextLeavePeriod instanceof LeavePeriod) {

                $nextLeaveBalance = $leaveEntitlementService->getLeaveBalance($employeeId, $leaveTypeId, $nextLeavePeriod->getLeavePeriodId());

                if ($leaveLengthOnNextLeavePeriod > $nextLeaveBalance) {

                    $canApplyForNextLeavePeriod = false;

                }

            } else {

                $canApplyForNextLeavePeriod = false;

            }

        }

        if ($canApplyForCurrentLeavePeriod && $canApplyForNextLeavePeriod) {
            return true;
        } else {
            return false;
        }

    }

    /**
     *
     * @param ParameterObject $searchParameters
     * @param array $statuses
     * @return array
     */
    public function searchWfhRequests($searchParameters, $page = 1, $isCSVPDFExport = false, $isMyLeaveList = false, 
            $prefetchLeave = false, $prefetchComments = false) {
        $result = $this->getWfhRequestDao()->searchWfhRequests($searchParameters, $page, $isCSVPDFExport, $isMyLeaveList, $prefetchLeave, $prefetchComments);
        return $result;

    }

    /**
     * Get Leave Request Status
     * @param $day
     * @return unknown_type
     */
    public function getLeaveRequestStatus( $day ) {
        try {
            $holidayService = $this->getHolidayService();
            $holiday = $holidayService->readHolidayByDate($day);
            if ($holiday != null) {
                return Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
            }

            return Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;

        } catch (Exception $e) {
            throw new LeaveServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param int $compOffRequestId
     * @return array
     */
    public function searchWfh($wfhRequestId) {

        return $this->getWfhRequestDao()->fetchWfh($wfhRequestId);

    }

    /**
     *
     * @param int $leaveId
     * @return array
     */
    public function readCompOff($leaveId) {

         return $this->getCompOffRequestDao()->readCompOff($leaveId);

    }

    public function saveLeave(Leave $leave) {
        return $this->getLeaveRequestDao()->saveLeave($leave);
    }

    /**
     * @param int $leaveRequestId
     */
    public function fetchWfhRequest($wfhRequestId) {
    $request=$this->getWfhRequestDao()->fetchWfhRequest($wfhRequestId);
    return $request;
       

    }

    /**
     * Modify Over lap leaves
     * @param LeaveRequest $leaveRequest
     * @param $leaveList
     * @return unknown_type
     */
    public function modifyOverlapLeaveRequest(LeaveRequest $leaveRequest , $leaveList ) {

        return $this->getLeaveRequestDao()->modifyOverlapLeaveRequest($leaveRequest , $leaveList);

    }

    /**
     *
     * @param LeavePeriod $leavePeriod
     * @return boolean
     */
    public function adjustLeavePeriodOverlapLeaves(LeavePeriod $leavePeriod) {

        $overlapleaveList =	$this->getLeaveRequestDao()->getLeavePeriodOverlapLeaves($leavePeriod);

        if (count($overlapleaveList) > 0) {

            foreach($overlapleaveList as $leave) {

                $leaveRequest	=	$leave->getLeaveRequest();
                $leaveList		=	$this->getLeaveRequestDao()->fetchLeave($leaveRequest->getLeaveRequestId());
                $this->getLeaveRequestDao()->modifyOverlapLeaveRequest($leaveRequest,$leaveList,$leavePeriod);

            }

        }

    }

    function groupChanges($changes) {
        $groupedChanges = array();
        
        foreach ($changes as $id => $value) {
            if (strpos($value, 'WF') === 0) {
                $workFlowId = substr($value, 2);
                if (isset($groupedChanges[$workFlowId])) {
                    $groupedChanges[$workFlowId][] = $id;
                } else {
                    $groupedChanges[$workFlowId] = array($id);
                }
            }
        }
        
        return $groupedChanges;
    }           
        
    
    public function deleteWfhHistory($ids){
       $this->getWfhRequestDao()->deleteWfhHistory($ids);
    }
    /**
     *
     * @param array $changes
     * @param string $changeType
     * @return boolean
     */
    public function changeWfhStatus($changes, $changeType, $changeComments = null, $changedByUserType = null, $changedUserId = null) {
        if (is_array($changes)) {   
            $groupedChanges = $this->groupChanges($changes);

            if ($changeType == 'change_leave_request') {
                foreach ($groupedChanges as $workFlowId => $changedItems) {

                    foreach ($changedItems as $wfhRequestId) {
                        $changedWfh = $this->searchWfh($wfhRequestId);
                        $this->_changeWfhStatus($changedWfh, $workFlowId, $changeComments[$wfhRequestId],$changeType,$changedByUserType,$changedUserId);
                      
              
                    }
                }

            } elseif ($changeType == 'change_leave') {
 
                $actionTypes = count($groupedChanges);
                
                $workFlowItems = array();
                $changes = array();
                $allDays = array();
                
                foreach ($groupedChanges as $workFlowId => $changedItems) {

                 $changedWfh = array();
                    foreach ($changedItems as $wfhId) {

                        $changedWfh[] = $this->getWfhRequestDao()->getWfhById($wfhId);

                         $this->_changeWfhStatus($changedWfh, $workFlowId, $changeComments,$changeType,$changedByUserType,$changedUserId);                         


                    }
                    
                    
                  /*  if ($actionTypes == 1) {
                        $this->_notifyLeaveStatusChange($event, $workFlow, $changedCompOff, 
                                $changedByUserType, $changedUserId, 'multiple');                           
                    } else {
                        
                        $changes[$workFlow->getId()] = $changedCompOff;
                        $allDays = array_merge($allDays, $changedCompOff);
                    }*/
                }                       
                
               /* if ($actionTypes > 1) {
                    $this->_notifyLeaveMultiStatusChange($allDays, $changes, $workFlowItems,
                                $changedByUserType, $changedUserId, 'multiple');                        
                }*/
            } else {
                throw new LeaveServiceException('Wrong change type passed');
            }
        }else {
            throw new LeaveServiceException('Empty changes list');
        }

    }

    protected function _changeWfhStatus($wfhList, $newState, $comments = null,$changeType,$changedByUserType,$loggedInEmpNumber) {
        $dao = $this->getWfhRequestDao();
        foreach ($wfhList as $wfh) {
         if($changeType == 'change_leave_request'){
             $changeType='WFH request';
            $currentState = $wfh->getStatus();   
         }
         else {
               $changeType= 'WFH';
            $currentState = $wfh->getStatus(); 
           
         }
          $result= $dao->changeWfhStatus($wfh,$currentState, $newState);

            //}
        }    
                       $request=$this->fetchWfhRequest($wfh->getWfhRequestId());
                      $this->getWfhRequestUpdateDataForEmail($request,$result,$changeType,$changedByUserType,$loggedInEmpNumber);

    }
    
   

    public function getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId) {

        return $this->getLeaveRequestDao()->getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId);

    }

    public function getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId) {

        return $this->getLeaveRequestDao()->getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId);

    }
    
    public function getWfhRequestActions($request, $loggedInEmpNumber) {
        $actions = array();
               if ($request->getCompOffStatusId()==0) {
            
            $includeRoles = array();
            $excludeRoles = array();
            
            $userRoleManager = $this->getUserRoleManager();
            
            $empNumber = $request->getEmpNumber();
            // If looking at own comp-off request, only consider ESS role
            if ($empNumber == $loggedInEmpNumber && ($userRoleManager->essRightsToOwnWorkflow() || !$userRoleManager->isEntityAccessible('Employee', $empNumber))) {
                $actions[1]='Cancel';
                return $actions;
            }            

            $actions[1]='Cancel';
            $actions[2]='Approve';
        }
        elseif ($request->getCompOffStatusId()==2 )//&&($empNumber == $loggedInEmpNumber && ($userRoleManager->essRightsToOwnWorkflow() || !$userRoleManager->isEntityAccessible('Employee', $empNumber)))) {
        {
        $actions[1]='Cancel';
        }
    //}
        return $actions;
    }
    
    public function getWfhActions($wfh, $loggedInEmpNumber) {
        $actions = array();
   if ($wfh->getStatus()==0) {
            
            $includeRoles = array();
            $excludeRoles = array();
            
            $userRoleManager = $this->getUserRoleManager();
            
            $empNumber = $wfh->getEmpNumber();
      if ($empNumber == $loggedInEmpNumber && ($userRoleManager->essRightsToOwnWorkflow() || !$userRoleManager->isEntityAccessible('Employee', $empNumber))) {
                  $actions[1]='Cancel';
                return $actions;
            }            
             $actions[1]='Cancel';
            $actions[2]='Approve';
        }
        elseif ($wfh->getStatus()==2)//&&($empNumber == $loggedInEmpNumber && ($userRoleManager->essRightsToOwnWorkflow() || !$userRoleManager->isEntityAccessible('Employee', $empNumber)))) {
        {
            $actions[1]='Cancel';
   }
        return $actions;
    }    

    /**
     *
     * @param type $employeeId
     * @param type $date
     * @return double
     */
    public function getTotalLeaveDuration($employeeId, $date){
        return $this->getLeaveRequestDao()->getTotalLeaveDuration($employeeId, $date);
    }

    public function getWfhById($wfhId) {
        return $this->getWfhRequestDao()->getWfhById($wfhId);
    }
     /**
     *
     * @param ParameterObject $searchParameters
     * @param array $statuses
     * @return array
     */
    public function getLeaveRequestSearchResultAsArray($searchParameters) {
        return $this->getLeaveRequestDao()->getLeaveRequestSearchResultAsArray($searchParameters);
    }
    
     /**
     *
     * @param ParameterObject $searchParameters
     * @param array $statuses
     * @return array
     */
    public function getDetailedLeaveRequestSearchResultAsArray($searchParameters) {
        return $this->getLeaveRequestDao()->getDetailedLeaveRequestSearchResultAsArray($searchParameters);
    }

    public function markApprovedLeaveAsTaken() {
        return $this->getCompOffRequestDao()->markApprovedLeaveAsTaken();
    }
     public function markExpiredCompoff(){
        return $this->getCompoffRequestDao()->markExpiredCompoff();
    }
     private function getWfhRequestUpdateDataForEmail(WfhRequest $request,$result,$type,$changedByUserType,$loggedInEmpNumber){
         $status=$result['newstate']==2?'approved':'cancelled';
         $wfhRequestDates=array();
         $wfhObject=$request->getWfh();
         $count=count($wfhObject);
         $durationtype=array();
        // $tasks=array();
         foreach($wfhObject as $wfh)
             {
               $wfhRequestDates[]=$wfh->getDate();
               $durationtype[]=$wfh->getDurationType();
              
         }
         
        if($durationtype==0)
        {
            $session1='Session 1';
            $session2='Session 2';
        }
        elseif($durationtype==1)
        {
            $session1='Session 1';
            $session2='Session 1';
        }
   else {
            $session1='Session 1';
            $session2='Session 2';
        }
        if(!empty($loggedInEmpNumber)){
             $supervisor = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
             $supervisorName = $supervisor->getFullName();
        }
//      $calledBy = $this->getEmployeeService()->getEmployee($request->getCalledEmpNumber());
//       $calledByName = $calledBy->getFullName();
   //    $workType=$request['work_type'];
       $reason=$request['comments'];
       $tasks=$request->getWfhTasks();
        $self=($request->getEmpNumber()==$loggedInEmpNumber)?true:false;
        $supervisorEmail=array();
        if($self){
            //get work email address of superwiser
             $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
             $supervisors=$employee->getSupervisors();
             foreach ($supervisors as $supervisor) {
                $supervisorEmail[$supervisor->getFirstName()]=$supervisor->getEmpWorkEmail();
             }
            
        }else{
            //get work email address of superwiser and leave applicant
            $employee = $this->getEmployeeService()->getEmployee($request->getEmpNumber());
            $to=$employee->getEmpWorkEmail();
            $leaveApplicantFullName=$employee->getFullName();
            $supervisors=$employee->getSupervisors();
          
            foreach ($supervisors as $supervisor) {
                if($loggedInEmpNumber != $supervisor->getEmpNumber()){
                    $supervisorEmail[$supervisor->getFirstName()]=$supervisor->getEmpWorkEmail();
                }
            } 
            $supervisorEmail[$employee->getFirstName()]=$to;
            
        }
         $emailService = new EmailService();
         foreach ($supervisorEmail as $name => $email) {
             if($self){
                 $subject=$employee->getFullName().' has '.$status.' his '.$type; 
             }else{
               $subject=(!empty($loggedInEmpNumber))? $supervisorName.' has '. $status:$changedByUserType.' has '. $status;
               $subject.=($to==$email)?' your ':' '.$employee->getFullName()."'s";
               $subject.=' '.$type;  
             }
              $mailMessage = "Hi &nbsp;".$name.",<br/><br/>";
              $mailMessage.= "<br /><br />Greetings...!!!!!!<br /><br />";
              $mailMessage.="Below mentioned WFH request application ";
              $mailMessage.= ($to==$email)?"has been $status by ":"for $leaveApplicantFullName has been $status by ";
              $mailMessage.=($self)?$employee->getFullName():(!empty($loggedInEmpNumber))? $supervisorName:'the '.$changedByUserType;           

              $mailMessage.='<html><body>
                    
                    <br />
                    <table style="width:100%;border-collapse: collapse;">
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black">From Date(s)</th>
                        <th style="border: 1px solid black">To Date</th>
                        <th style="border: 1px solid black">From Session</th>
                        <th style="border: 1px solid black">To Sesion</th>
                        <th style="border: 1px solid black">Reason</th>
                       
                       
                        
                      </tr>';
                if($type=='wfh request'){
                       
                     $leaveRequest=$request;
                     $mailMessage.='<tr style="border: 1px solid black">
                                <td style="border: 1px solid black"><center>'.$wfhRequestDates[0].'</center></td>
                                <td style="border: 1px solid black"><center>'.$wfhRequestDates[$count-1].'</center></td>
                                <td style="border: 1px solid black"><center>'.$session1.'</center></td>
                                <td style="border: 1px solid black"><center>'.$session2.'</center></td>
                                <td style="border: 1px solid black"><center>'.$reason.'</center></td>
                                
                               
                              </tr>';
                 }else{
                      $leave=$request; 
                       $mailMessage.='<tr style="border: 1px solid black">
                                <td style="border: 1px solid black"><center>'.$wfhRequestDates[0].'</center></td>
                                <td style="border: 1px solid black"><center>'.$wfhRequestDates[$count-1].'</center></td>
                                <td style="border: 1px solid black"><center>'.$session1.'</center></td>
                                <td style="border: 1px solid black"><center>'.$session2.'</center></td>
                                <td style="border: 1px solid black"><center>'.$reason.'</center></td>
                               
                            
                              </tr>';
                   }
              $mailMessage .='</table><br /><br /></body></html>';
              $mailMessage.='This is an auto-generated email, request not to revert to the same. Feel free to get on touch with the Admin, in case of any conflict in the approval. <br/><br/>';           
              $mailMessage.='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login<br /><br />';
              $mailMessage.='Looking forward for your co-operation.';
              $mailMessage .= '<br/>';
              $mailMessage.='--<br />Thanks<br />Team-Perennial';
              $emailService->sendEmailNotificationByAction($email,$subject,$mailMessage);


         }
}
}
