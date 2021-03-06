<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

/**
 * changeLeaveStatus
 */
class changeCompOffStatusAction extends baseCoreLeaveAction {

    public function execute($request) {
        
        if ($request->isMethod('post')) {
            $form = new DefaultListForm();
            $form->bind($request->getParameter($form->getName()));
            $changes = $request->getParameter('leaveRequest');
            $changeType = 'change_leave_request';
            $leaveComments = $request->getParameter('leaveComments');
            $changeComments = array();
            $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
            if (empty($changes)) {
                
                $changes = $request->getParameter('leave');
                $changeType = 'change_leave';
            }
            //this is to bypass the approval/rejection comment
            foreach ($changes as $k => $v) {
                if (trim($v) != "") {
                    $changeComments[$k] = $leaveComments[$k];
                }
            }
            $changeIds = array_keys($changes); 
            $empNumbers = $this->_getCompOffChangeEmployeeNumbers($changeIds, $changeType);
            $manager = $this->getContext()->getUserRoleManager();
            $allowed = $manager->areEntitiesAccessible('Employee', $empNumbers);
            $mode = $request->getParameter('hdnMode', null);
            if ($allowed) {
                 
                if ($mode != LeaveListForm::MODE_MY_LEAVE_LIST && $mode != viewLeaveRequestAction::MODE_MY_LEAVE_DETAILED_LIST) {
                   
                    $changedByUserType = SystemUser::USER_TYPE_ADMIN;
                } else {
                    $changedByUserType = SystemUser::USER_TYPE_EMPLOYEE;
                }
            } else {
               
                // Check if changing own leave status
                $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
                if ((count($empNumbers) == 1) && ($empNumbers[0] == $loggedInEmpNumber)) {
                    $changedByUserType = SystemUser::USER_TYPE_EMPLOYEE;
                } else {
                    $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
                }
            }
            if ($form->isValid()) {
                $this->getCompOffRequestService()->changeCompOffStatus($changes, $changeType, $changeComments, $changedByUserType, $_SESSION['empNumber']); 
                
                $this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS)); 
                
            }
           
        }

        if ($changedByUserType == SystemUser::USER_TYPE_EMPLOYEE) {
 
            if ($changeType == 'change_leave') {
                $url = "leave/viewCompOffRequest";
                $this->getUser()->setFlash('myLeave', true);
            } else {
                $url = "leave/viewMyCompOffList";
            }

            $url = ($changeType == 'change_leave') ? "leave/viewCompOffRequest" : "leave/viewMyCompOffList";
           if (trim($request->getParameter("id")) != "") {
                $url = $url . "?id=" . $request->getParameter("id");
            }

            if (trim($request->getParameter("hdnMode")) != "") {
                $url = $url . "?hdnMode=" . $request->getParameter("hdnMode");
            }
        } else {
              
             // $url='leave/viewCompOffList';
            $redirectAction = stripos($request->getReferer(), 'viewMyCompOffList') === FALSE ? 'leave/viewCompOffList' : 'leave/viewMyCompOffList';
            
            $url = ($changeType == 'change_leave') ? "leave/viewCompOffRequest" : $redirectAction;

            $page = $request->getParameter("currentPage");
            if (trim($request->getParameter("id")) != "") {
                $url = $url . "?id=" . $request->getParameter("id") . "&pageNo=" . $page;
            } else {
                $url = $url . "?pageNo=" . $page;
            }
        }
       
        if ($this->getUser()->hasFlash('myLeave')) {
            $this->getUser()->setFlash('myLeave', true);
        }
        $this->redirect($url);
    }

    protected function _getCompOffChangeEmployeeNumbers($changes, $changeType) {
         
        $empNumbers = array();
        $compOffRequestService = $this->getCompOffRequestService();
          
        if ($changeType == 'change_leave_request') {
          
            foreach ($changes as $id) {
                $compOffRequest = $compOffRequestService->fetchCompOffRequest($id);
                $empNumbers[] = $compOffRequest->getEmpNumber();
            }
        } else {
 
            foreach ($changes as $id) {
                $compOff = $compOffRequestService->getCompOffById($id);
                $empNumbers[] = $compOff->getEmpNumber();
            }
        }
         
        return array_unique($empNumbers);
    }
     
}