<?php

abstract class baseCoreLeaveAction extends sfAction {

    public $form;
    protected $workWeekService;
    protected $leaveEntitlementService;
    protected $leaveTypeService;
    private $leaveRequestService;
    private $leavePeriodService;
    private $compOffRequestService;
    private $wfhRequestService;

    /**
     *
     * @return WorkWeekService
     */
    protected function getWorkWeekService() {
        if (!($this->workWeekService instanceof WorkWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }

    /**
     *
     * @param WorkWeekService $service 
     */
    protected function setWorkWeekService(WorkWeekService $service) {
        $this->workWeekService = $service;
    }

    /**
     *
     * @return OldLeaveEntitlementService
     */
    protected function getLeaveEntitlementService() {
        if (!($this->leaveEntitlementService instanceof OldLeaveEntitlementService)) {
            $this->leaveEntitlementService = new OldLeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     *
     * @param OldLeaveEntitlementService $service 
     */
    protected function setLeaveEntitlementService(OldLeaveEntitlementService $service) {
        $this->leaveEntitlementService = $service;
    }

    /**
     *
     * @return LeaveTypeService
     */
    protected function getLeaveTypeService() {
        if (!($this->leaveTypeService instanceof LeaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     *
     * @param LeaveTypeService $service 
     */
    protected function setLeaveTypeService(LeaveTypeService $service) {
        $this->leaveTypeService = $service;
    }

    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
        }

        return $this->leaveRequestService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     *
     * @param sfForm $form
     * @return
     */
    protected function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     * Returns Logged in user details
     * @return array
     * @todo Refactor this method to use auth classes instead of directly accessing the session
     */
    protected function getLoggedInUserDetails() {
        $userDetails['userType'] = 'ESS';

        /* Value 0 is assigned for default admin */
        $userDetails['loggedUserId'] = (empty($_SESSION['empNumber'])) ? 0 : $_SESSION['empNumber'];
        $userDetails['empId'] = (empty($_SESSION['empID'])) ? 0 : $_SESSION['empID'];
        $loggedInEmpNum = $userDetails['loggedUserId'];
        if (!empty($loggedInEmpNum)||$loggedInEmpNum != 0) {
            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNum);
            if (empty($employee)) {
                echo "<pre>";
                print_r($_SESSION);
                exit;
                $authService = new AuthenticationService();
                $authService->clearCredentials();
                $this->redirect('auth/login');
            }
        }

        if ($_SESSION['isSupervisor']) {
            $userDetails['userType'] = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userDetails['userType'] = 'Admin';
        }

        return $userDetails;
    }
    
    
     /**
     * Get data group permissions
     *
     * @param type $dataGroups
     * @return type
     */
    public function getDataGroupPermissions($dataGroups) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), false, array());
    }
     
    public function getCompOffRequestService() {
        if (is_null($this->compOffRequestService)) {
            $compOffRequestService = new CompOffRequestService();
            $compOffRequestService->setCompoffRequestDao(new CompoffRequestDao());
            $this->compOffRequestService = $compOffRequestService;
        }

        return $this->compOffRequestService;
    }
    public function getWfhRequestService() {
        if (is_null($this->wfhRequestService)) {
            $wfhRequestService = new WfhRequestService();
            $wfhRequestService->setWfhRequestDao(new WfhRequestDao());
            $this->wfhRequestService = $wfhRequestService;
        }

        return $this->wfhRequestService;
    }

}