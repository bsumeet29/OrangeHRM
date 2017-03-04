<?php

/**
 * viewCompOffRequestAction
 *
 * @author Rahul
 */
class viewCompOffRequestAction extends baseLeaveAction {

    const MODE_ADMIN_DETAILED_LIST = 'detailed_hr_admin_list';
    const MODE_MY_COMP_OFF_DETAILED_LIST = 'my_comp_off_detailed_list';

    private $compOffRequestService;

    /**
     *
     * @return CompOffRequestService
     */
    public function getCompOffRequestService() {
        if (is_null($this->compOffRequestService)) {
            $compOffRequestService = new CompOffRequestService();
            $compOffRequestService->setCompOffRequestDao(new CompOffRequestDao());
            $this->compOffRequestService = $compOffRequestService;
        }

        return $this->compOffRequestService;
    }

    /**
     *
     * @param CompOffRequestService $compOffRequestService
     * @return void
     */
    public function setCompOffRequestService(CompOffRequestService $compOffRequestService) {
        $this->compOffRequestService = $compOffRequestService;
    }

    protected function getMode($requesterEmpNumber) {

        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');

        if ($loggedInEmpNumber === $requesterEmpNumber) {
            $mode = self::MODE_MY_COMP_OFF_DETAILED_LIST;
        } else {
            $manager = $this->getContext()->getUserRoleManager();
            $accessible = $manager->isEntityAccessible('Employee', $requesterEmpNumber);
            if ($accessible) {
                $mode = self::MODE_ADMIN_DETAILED_LIST;
            } else {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }
        }

        return $mode;
    }

    protected function isEssMode($requesterEmpNumber) {
        $userMode = 'ESS';

        if ($_SESSION['isSupervisor']) {
            if ($this->getMode($requesterEmpNumber) == self::MODE_MY_COMP_OFF_DETAILED_LIST) {
                $userMode = 'ESS';
            } else {
                $userMode = 'Supervisor';
            }
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            if ($this->getMode($requesterEmpNumber) == self::MODE_MY_COMP_OFF_DETAILED_LIST) {
                $userMode = 'ESS';
            } else {
                $userMode = 'Admin';
            }
        }

        return ($userMode == 'ESS');
    }

    protected function getTitle($mode, $employee, $leaveList) {

        if ($mode === self::MODE_ADMIN_DETAILED_LIST) {
            $range = $this->getDateRangeString($leaveList);
            $title = __('Comp-off Request (%date_range%) %name%', array('%date_range%' => $range, '%name%' => $employee->getFullName()));
        } elseif ($mode === self::MODE_MY_COMP_OFF_DETAILED_LIST) {
            // Do this for 
            $title = __('My Comp-off Details');
        }

        return $title;
    }

    /**
     * 
     * @return string
     */
    protected function getDateRangeString($compOffList) {
        $range = '';
        $count = count($compOffList);
        if ($count == 1) {
            $range = set_datepicker_date_format($compOffList[0]->getDate());
        } else if ($count > 1) {
            $range = set_datepicker_date_format($compOffList[0]->getDate());
            $range .= " " . __('to') . " ";
            $range .= set_datepicker_date_format($compOffList[$count - 1]->getDate());
        }

        return $range;
    }

    public function execute($request) {
        $this->backUrl = stripos($request->getReferer(), 'viewMyCompOffList') === FALSE ?
                'leave/viewCompOffList' : 'leave/viewMyCompOffList';

        if ($this->getUser()->hasFlash('myLeave')) { 

            $myLeave = $this->getUser()->getFlash('myLeave');
            if ($myLeave) {
                $this->backUrl = 'leave/viewMyCompOffList';
            }
        }

        if ($this->backUrl === 'leave/viewMyCompOffList') {
            $request->setParameter('initialActionName', 'viewMyCompOffList');
            $this->getUser()->setFlash('myLeave', true);
        } else {
            $request->setParameter('initialActionName', 'viewCompOffList');
        }

        $this->compOffRequestId = $request->getParameter('id');
         //  echo"id :".$request->getParameter('id');exit;

        $compOffRequest = $this->getCompOffRequestService()->fetchCompOffRequest($this->compOffRequestId);
       $employee = $compOffRequest->getEmployee();
        //$empNumber = $employee->getEmpNumber();
        $empNumber = $compOffRequest['emp_number'];
       // $called = $employee->getCalledEmpNumber();
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
         //echo"Emp :".$empNumber;exit;
        $self = false;
        if ($loggedInEmpNumber == $empNumber) {
            $self = true;

        }

        $this->compOffListPermissions = $this->getDataGroupPermissions('leave_list', $self);
        
        $this->commentPermissions = $this->getDataGroupPermissions('leave_list_comments', $self);
      
       // $this->requestComments = $compOffRequest->getComments();
        
        $this->mode = $this->getMode($empNumber);
        $this->essMode = $this->isEssMode($empNumber);
        $this->compOffcommentForm = new LeaveCommentForm(array(),array(),true);
        $list = $this->getCompOffRequestService()->searchCompOff($this->compOffRequestId);
        $this->title = $this->getTitle($this->mode, $employee, $list);
        $this->baseUrl = 'leave/viewCompOffRequest';
        if ($this->compOffListPermissions->canRead()) {
                      

            $this->setListComponent($list);
        }
        $this->setTemplate('viewCompOffRequest');
    }

    protected function setListComponent($compOffList) {
        ohrmListComponent::setHeaderPartial("leave/compoff_request_comments");
        ohrmListComponent::setConfigurationFactory($this->getListConfigurationFactory());
        ohrmListComponent::setActivePlugin('orangehrmLeavePlugin');
        ohrmListComponent::setListData($compOffList);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords(count($compOffList));
    }

    protected function getListConfigurationFactory() {
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
        DetailedCompOffListConfigurationFactory::setListMode($this->mode);
        DetailedCompOffListConfigurationFactory::setLoggedInEmpNumber($loggedInEmpNumber);
        $configurationFactory = new DetailedCompOffListConfigurationFactory();
        $configurationFactory->setRuntimeDefinitions(array(
            'title' => $this->title
        ));
        return $configurationFactory;
    }
    
}     
