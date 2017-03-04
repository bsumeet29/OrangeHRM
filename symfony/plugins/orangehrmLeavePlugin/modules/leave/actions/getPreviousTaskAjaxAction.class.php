<?php

/**
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
 */

/**
 * Get leave balance for given employee for given leave type
 *
 */
class getPreviousTaskAjaxAction extends sfAction {

    protected $leaveEntitlementService;
    protected $workScheduleService;
    protected $leaveApplicationService;  
    protected $leaveRequestService;
    
    /**
     * Get leave balance for given leave type
     * Request parameters:
     * *) leaveType: Leave Type ID
     * *) empNumber: (optional) employee number. If not present, currently
     *               logged in employee is used.
     * 
     * @param sfWebRequest $request
     */
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $leaveTypeId = $request->getParameter('leaveType');
        $empNumber = $request->getParameter('empNumber');

        $user = $this->getUser();
        $loggedEmpNumber = $user->getAttribute('auth.empNumber');

        $allowed = false;

        if (empty($empNumber)) {
            $empNumber = $loggedEmpNumber;
            $allowed = true;
        } else {

            $manager = $this->getContext()->getUserRoleManager();
            if ($manager->isEntityAccessible('Employee', $empNumber)) {
                $allowed = true;
            } else {
                $allowed = ($loggedEmpNumber == $empNumber);
            }
        }

        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->setHttpHeader("Cache-Control", "private", false);
        
       // $balance = '--';
        if ($allowed) {
            $localizationService = new LocalizationService();
            $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
            $startDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $request->getParameter("startDate"));

            $startDateTimeStamp = strtotime($startDate);
            
            // If not start date, show balance as of today
            if (!$startDateTimeStamp) {
                $startDate = date('Y-m-d');
            }
            
            $endDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $request->getParameter("endDate"));

            $endDateTimeStamp = strtotime($endDate);
             if ($endDateTimeStamp && ($endDateTimeStamp >= $startDateTimeStamp)) {
                  $res= $this->getLeaveRequestService()->getLeaveTasksForDate($empNumber,$startDate,$endDate);
                  //print_r($result);
             }
             $result = array();
             $count = count($res);
             $i =0;
             foreach ($res as $key => $value){
                 $result['taskName'][$i++] = $value['task_name'];
             }
             $result['count'] = $count;
            echo json_encode($result);
            
             }

       return sfView::NONE;
    }
    
    /**
     * @param array $formValues
     * @return LeaveParameterObject
     */
    protected function getLeaveParameterObject($empNumber, $leaveTypeId, $fromDate, $toDate) {
        
        $formValues = array();
        
        $formValues['txtEmpID'] = $empNumber;
        $formValues['txtFromDate'] = $fromDate;
        $formValues['txtToDate'] = $toDate;        
        $formValues['txtLeaveType'] = $leaveTypeId;
        
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);        
        $formValues['txtEmpWorkShift'] = $workSchedule->getWorkShiftLength();   
        
        return new LeaveParameterObject($formValues);
    }
    
    protected function getLeavePeriod($date, $empNumber, $leaveTypeId) {
        
        $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();
        
        return $strategy->getLeavePeriod($date, $empNumber, $leaveTypeId);
    }

    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        if (is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     *
     * @param LeaveEntitlementService $leaveEntitlementService
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
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
     * Get leave application service instance
     * 
     * @return LeaveApplicationService
     */
    public function getLeaveApplicationService() {
        if (!($this->leaveApplicationService instanceof LeaveApplicationService)) {
            $this->leaveApplicationService = new LeaveApplicationService();
        }
        return $this->leaveApplicationService;
    }

    /**
     * Set leave application service instance
     * @param LeaveApplicationService $service 
     */
    public function setLeaveApplicationService(LeaveApplicationService $service) {
        $this->leaveApplicationService = $service;
    }
    public function getLeaveRequestService(){
        if(!($this->leaveRequestService instanceof LeaveRequestService)){
            $this->leaveRequestService = new LeaveRequestService();
        }
        return $this->leaveRequestService;
    }
    public function setLeaveRequestService(LeaveRequestService $service){
        $this->leaveRequestService = $service;
    }
        
}

