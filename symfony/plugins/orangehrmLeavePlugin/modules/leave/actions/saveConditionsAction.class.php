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
 * updateComment
 */
class saveConditionsAction extends baseLeaveAction {
    
    protected $employeeService;
    protected $leaveRequestService;
    
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
    
    public function execute($request) {
        $leaveRequestService = $this->getLeaveRequestService();
        $leaveRequestId = trim($request->getParameter("leaveRequestId"));
        $leaveId = trim($request->getParameter("leaveId"));
        $condition = trim($request->getParameter("leaveComment"));
        $reviewDate = trim($request->getParameter("reviewvalDate"));
        $user = $this->getUser();
        $loggedInUserId = $user->getAttribute('auth.userId');
        $loggedInEmpNumber = $user->getAttribute('auth.empNumber');
       // echo"logged :".$loggedInEmpNumber;exit;
        if (!empty($loggedInEmpNumber)) {
            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
            $createdBy = $employee->getFullName();
        } else {
            $createdBy = $user->getAttribute('auth.firstName');
        }
        $savedComment = NULL;

                    
        if ($leaveRequestId != "") {
            
            $leaveRequest = $this->getLeaveRequestService()->fetchLeaveRequest($leaveRequestId);  
            //$permissions = $this->getCommentPermissions($loggedInEmpNumber == $leaveRequest->getEmpNumber());

            //if ($permissions->canCreate()) {
                $form = new LeaveConditionsForm( array(),array(),true);
                //if ($form->getCSRFToken() == $request->getParameter("token")) {
                $reviewDate = strtotime($reviewDate);
                $reviewDate = date('Y-m-d',$reviewDate);
                    $savedComment = $leaveRequestService->saveLeaveRequestCondition($leaveRequest, 
                        $condition, $reviewDate);
                    $this->getCommentUpdateDataForEmail($savedComment,$leaveRequest,$createdBy,'leave request');
               //}
            //}
        }

        if ($leaveId != "") {
            $leave = $this->getLeaveRequestService()->readLeave($leaveId);
            
           // $permissions = $this->getCommentPermissions($loggedInEmpNumber == $leave->getEmpNumber());
           // if ($permissions->canCreate()) {
            
                $form = new LeaveConditionsForm( array(),array(),true);
                $reviewDate = strtotime($reviewDate);
                $reviewDate = date('Y-m-d',$reviewDate);
             //   if ($form->getCSRFToken() == $request->getParameter("token")) {
                $savedComment = $leaveRequestService->saveLeaveCondition($leave,$leaveId, 
                   $condition, $reviewDate);
                $this->getCommentUpdateDataForEmail($savedComment,$leave,$createdBy,'leave');
               // }
            //}
        }
        
        /*if (!empty($savedComment)) {
                /*$created = new DateTime($savedComment->getCreated());
                $createdAt = set_datepicker_date_format($created->format('Y-m-d')) . ' ' . $created->format('H:i');
                
                $returnText = $createdAt . ' ' . $savedComment->getCreatedByName() . "\n\n" .
                        $savedComment->getComments();    
            $returnText = $savedComment;
        } else {
            $returnText = 0;
        }*/

        if ($this->getUser()->hasFlash('myLeave')) {
            $this->getUser()->setFlash('myLeave', true);
        }
        
        return $this->renderText($savedComment);
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

    private function getCommentUpdateDataForEmail($savedComment,$request,$createdBy,$type){
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
               $subject=$createdBy.' has given conditions for ';
               $subject.=($to==$email)?' your':$employee->getFullName();
               $subject.=' '.$type;  
             }
              $mailMessage = "Hi &nbsp;".$name.",<br/><br/>";
              $mailMessage.=$createdBy.'  has given conditions for';           
              $mailMessage.=($self)?' his':(($to==$email)?' your ':$leaveApplicantFullName);
              $mailMessage.=" ".$type."<br /><br />Condition details are:<br />";
              $mailMessage.='<html><body>
                    
                    <br />
                    <table style="width:100%;border-collapse: collapse;">
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black">Leave Date(s)</th>
                        <th style="border: 1px solid black">Leave Type</th>
                        <th style="border: 1px solid black">Conditions</th>
                        <th style="border: 1px solid black">Reviewval Date</th>
                      </tr>';
                if($type=='leave request'){
                     $leaveRequest=$request;
                     $mailMessage.='<tr style="border: 1px solid black">
                                <td style="border: 1px solid black"><center>'.$leaveRequest->getLeaveDateRange().'</center></td>
                                <td style="border: 1px solid black"><center>'.$leaveRequest->getLeaveType()->getName().'</center></td>
                                <td style="border: 1px solid black"><center>'. $savedComment->getConditions().'</center></td>
                                <td style="border: 1px solid black"><center>'. $savedComment->getReviewvalDate().'</center></td>
                              </tr>';
                 }else{
                      $leave=$request; 
                       $mailMessage.='<tr style="border: 1px solid black">
                                <td style="border: 1px solid black"><center>'.$leave->getDate().'</center></td>
                                <td style="border: 1px solid black"><center>'.$leave->getLeaveType()->getName().'</center></td>
                                <td style="border: 1px solid black"><center>'. $savedComment->getConditions().'</center></td>
                                <td style="border: 1px solid black"><center>'. $savedComment->getReviewvalDate().'</center></td>

                              </tr>';
                   }
              $mailMessage .='</table><br /><br /></body></html>';
              $mailMessage.='Sign in to Perennial Systems HRM with the following link to take action on leave application<br/><br/>';           
              $mailMessage.='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login<br /><br />';
              $mailMessage .= '<br/>';
              $mailMessage .= '<br/>Best regards,<br/>';
              $mailMessage .= '<br/>Admin';
              $emailService->sendEmailNotificationByAction($email,$subject,$mailMessage);
         }
          
    }
}