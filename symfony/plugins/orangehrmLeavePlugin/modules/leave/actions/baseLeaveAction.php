<?php

abstract class baseLeaveAction extends orangehrmAction {

    protected $leaveTypeService;
    private $employeeService;

    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
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
     * 
     * @param type $dataGroups
     * @return type
     */
    public function getDataGroupPermissions($dataGroups, $self = false) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, array());
    }
    
    public function preExecute() {
        $sessionVariableManager = new DatabaseSessionManager();
        $sessionVariableManager->setSessionVariables(array(
            'orangehrm_user' => Auth::instance()->getLoggedInUserId(),
        ));
        $sessionVariableManager->registerVariables();
//        $this->setOperationName(OrangeActionHelper::getActionDescriptor($this->getModuleName(), $this->getActionName()));


        /* For highlighting corresponding menu item */
        $request = $this->getRequest();
        $initialActionName = $request->getParameter('initialActionName', '');

        if (empty($initialActionName)) {
            $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
            if (!empty($loggedInEmpNum)) {
                $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNum);
                if (empty($employee)) {
                    $authService = new AuthenticationService();
                    $authService->clearCredentials();
                    $this->redirect('auth/login');
                }
            }
        }
    }
}