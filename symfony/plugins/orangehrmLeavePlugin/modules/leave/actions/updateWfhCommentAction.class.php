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
 * updateComment code by rahul
 */
class updateWfhCommentAction extends BaseLeaveAction {
    
    protected $employeeService;
    protected $wfhRequestService;
    
    /**
     *
     * @return EmployeeService
     */
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     *
     * @param EmployeeService $service 
     */
    public function setEmployeeService(EmployeeService $service) {
        $this->employeeService = $service;
    }
    
    /**
     *
     * @return LeaveRequestService
     */
   
   /* public function getCompOffRequestService() {
        if (is_null($this->compOffRequestService)) {
            $compOffRequestService = new CompOffRequestService();
            $compOffRequestService->setCompOffRequestDao(new CompoffRequestDao());
            $this->compOffRequestService = $compOffRequestService;
        }

        return $this->compOffRequestService;
    }*/
    public function getWfhRequestService(){
        if(is_null($this->wfhRequestService)){
            $wfhRequestService = new WfhRequestService();
            $wfhRequestService->setWfhRequestDao(new WfhRequestDao());
            $this->wfhRequestService = $wfhRequestService;
        }
        return $this->wfhRequestService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setWfhRequestService(WfhRequestService $wfhRequestService) {
        $this->wfhRequestService = $wfhRequestService;
    }    
    
    public function execute($request) {
        $wfhRequestService = $this->getWfhRequestService();
        $wfhRequestId = trim($request->getParameter("leaveRequestId"));
        $wfhId = trim($request->getParameter("leaveId"));
        $comment = trim($request->getParameter("leaveComment"));
        $user = $this->getUser();
        $loggedInUserId = $user->getAttribute('auth.userId');
        $loggedInEmpNumber = $user->getAttribute('auth.empNumber');
                if (!empty($loggedInEmpNumber)) {
            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
            $createdBy = $employee->getFullName();
        } else {
            $createdBy = $user->getAttribute('auth.firstName');
        }
        
        $savedComment = NULL;
     
        if ($wfhRequestId != "") {
            $wfhRequest = $this->getWfhRequestService()->fetchWfhRequest($wfhRequestId);
            $permissions = $this->getCommentPermissions($loggedInEmpNumber == $wfhRequest->getEmpNumber());
            if ($permissions->canCreate()) {
                $form = new LeaveCommentForm( array(),array(),true);
                if ($form->getCSRFToken() ) {//== $request->getParameter("token")
                    $savedComment = $wfhRequestService->saveWfhRequestComment($wfhRequestId, 
                        $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                    $this->getWfhCommentUpdateDataForEmail($savedComment,$wfhRequest,'WFH request');
                }
            }
        }
        if ($wfhId != "") {
            $compOff = $this->getCompOffRequestService()->readCompOff($wfhId);
            $permissions = $this->getCommentPermissions($loggedInEmpNumber == $compOff->getEmpNumber());
            if ($permissions->canCreate()) {
                $form = new LeaveCommentForm( array(),array(),true);
                if ($form->getCSRFToken()) {
                $savedComment = $wfhRequestService->saveCompoffComment($wfhId, 
                    $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                $this->getWfhCommentUpdateDataForEmail($savedComment,$compOff,'WFH');
                }
            }
        }
        
        if (!empty($savedComment)) {
                $created = new DateTime($savedComment->getCreated());
                $createdAt = set_datepicker_date_format($created->format('Y-m-d')) . ' ' . $created->format('H:i');
                
                $returnText = $createdAt . ' ' . $savedComment->getCreatedByName() . "\n\n" .
                        $savedComment->getComments();            
        } else {
           
            $returnText = 0;
        }

        if ($this->getUser()->hasFlash('myLeave')) {
            $this->getUser()->setFlash('myLeave', true);
        }
        
        return $this->renderText($returnText);
    }

    protected function isEssMode() {
        $userMode = 'ESS';

        if ($_SESSION['isSupervisor']) {
            $userMode = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userMode = 'Admin';
        }

        return ($userMode == 'ESS');
    }
    
    protected function getCommentPermissions($self){
        return $this->getDataGroupPermissions('leave_list_comments', $self);
    }    

    private function getWfhCommentUpdateDataForEmail($savedComment,$request,$type){
        $user = $this->getUser();
        $loggedInEmpNumber = $user->getAttribute('auth.empNumber');
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
                 $subject=$savedComment->getCreatedByName().' commented on his '.$type; 
             }else{
               $subject=$savedComment->getCreatedByName().' commented on ';
               $subject.=($to==$email)?' your':$employee->getFullName();
               $subject.=' '.$type;  
             }
              $mailMessage = "Hi &nbsp;".$name.",<br/><br/>";
              $mailMessage.=$savedComment->getCreatedByName().' has commented on ';           
              $mailMessage.=($self)?' his':(($to==$email)?' your ':$leaveApplicantFullName);
              $mailMessage.=" ".$type."<br /><br />Comment details are:<br />";
              $mailMessage.='<html><body>
                    
                    <br />
                    <table style="width:100%;border-collapse: collapse;">
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black">Comp-off Date(s)</th>
                        <th style="border: 1px solid black">Comment</th>

                      </tr>';
                if($type=='WFH request'){
                     $leaveRequest=$request;
                     $mailMessage.='<tr style="border: 1px solid black">
                                <td style="border: 1px solid black"><center>'.$leaveRequest->getWfhDateRange().'</center></td>
                                <td style="border: 1px solid black"><center>'. $savedComment->getComments().'</center></td>
                              </tr>';
                 }else{
                      $leave=$request; 
                       $mailMessage.='<tr style="border: 1px solid black">
                                <td style="border: 1px solid black"><center>'.$leave->getWfhDateRange().'</center></td>
                                <td style="border: 1px solid black"><center>'. $savedComment->getComments().'</center></td>
                              </tr>';
                   }
              $mailMessage .='</table><br /><br /></body></html>';
              $mailMessage.='Sign in to Perennial Systems HRM with the following link to take action on comp-off application<br/><br/>';           
              $mailMessage.='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login<br /><br />';
              $mailMessage .= '<br/>';
              $mailMessage .= '<br/>Best regards,<br/>';
              $mailMessage .= '<br/>Admin';
              $emailService->sendEmailNotificationByAction($email,$subject,$mailMessage);
         }
          
    }
}