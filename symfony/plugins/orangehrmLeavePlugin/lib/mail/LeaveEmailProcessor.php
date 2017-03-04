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
 * Description of LeaveEmailProcessor
 *
 */
class LeaveEmailProcessor implements orangehrmMailProcessor {
    
    protected $employeeService;
    protected $userRoleManager;
    protected $logger;
    
    /**
     * Get Logger instance
     * @return Logger
     */
    public function getLogger() {
        if (empty($this->logger)) {
            $this->logger = Logger::getLogger('leave.leavemailer');
        }
        return $this->logger;
    }    
    
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
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
    
    public function getReplacements($data) {

        $replacements = array();
        
        $performer = $this->getEmployeeService()->getEmployee($data['empNumber']);
        
        if ($performer instanceof Employee) {
            $replacements['performerFirstName'] = $performer->getFirstName();
            $replacements['performerFullName'] = $performer->getFullName();
        } else {
            $name = sfContext::getInstance()->getUser()->getAttribute('auth.firstName');
            
            $replacements['performerFirstName'] = $name;
            $replacements['performerFullName'] = $name;
            
        }        

        if ($data['recipient'] instanceof Employee) {
            $replacements['recipientFirstName'] = $data['recipient']->getFirstName();
            $replacements['recipientFullName'] = $data['recipient']->getFullName();
        } else if ($data['recipient'] instanceof EmailSubscriber) {
            $replacements['recipientFirstName'] = $data['recipient']->getName();
            $replacements['recipientFullName'] = $data['recipient']->getName();            
        }

        $applicantNo = $data['days'][0]->getEmpNumber();
        
        $applicant = $this->getEmployeeService()->getEmployee($applicantNo);
        if ($applicant instanceof Employee) {
            $replacements['applicantFirstName'] = $applicant->getFirstName();
            $replacements['applicantFullName'] = $applicant->getFullName();
        }                
        
        $replacements = $this->_populateLeaveReplacements($data, $replacements);
        
        return $replacements;

    }   

    protected function getSubscribersForEvent($event) {
        $recipients = array();
        
        $mailNotificationService = new EmailNotificationService();
        $subscriptions = $mailNotificationService->getSubscribersByNotificationId($event);

        foreach ($subscriptions as $subscription) {

            if ($subscription instanceof EmailSubscriber) {

                if ($subscription->getEmailNotification()->getIsEnable() == EmailNotification::ENABLED) {

                    $recipients[] = $subscription;
                }
            }
        }
        
        return $recipients;        
    }
    
    protected function _populateLeaveReplacements($data, $replacements) {

        
        if ($data['request'] instanceof LeaveRequest) {
            $leavesDate=array();
            $durationType=array();
            foreach ($data['days'] as $leave) {
                $durationType[]=$leave->getDurationType();
                $leavesDate[]=$leave->getDate();
            
            }
            $dayCount=  count($durationType);
            $replacements['leaveType'] = $data['request']->getLeaveType()->getName();
//            $leaveDates=$data['request']->getLeaveStartAndEndDate();
            $replacements['leaveFromDate']=$leavesDate[0];
            $replacements['leaveToDate']=$leavesDate[$dayCount-1];
            $replacements['comments']=$data['request']->getComments();
//            $leaves=$data['request']->getLeave();
//            $durationType=array();
//            foreach ($leaves as $leave) {
//                 $durationType[]=$leave->getDurationType();
//             }
//             $dayCount=  count($durationType);
             if($durationType[0]==0 ||$durationType[0]==1){
                 $replacements['fromSession']="Session 1";
             }else{
                 $replacements['fromSession']="Session 2";
             }
            if($durationType[$dayCount-1]==0 ||$durationType[$dayCount-1]==2){
                $replacements['toSession']="Session 2"; 
            }else{
                $replacements['toSession']="Session 1"; 
            }
            $leaveTasks=$data['request']->getLeaveTasks();
            $mentionTask=array();
            foreach ($leaveTasks as $task) {
               $mentionTask[$task->getTaskName()]=$task->getEmployee()->getFirstAndLastNames();;
            }
            $replacements['contactPerson']=$mentionTask;
            $replacements['contactDetails']=$data['request']->getContactNo();
            $replacements['assigneeFullName'] = $data['request']->getEmployee()->getFirstAndLastNames();
         } else {
             
             $leavesDate=array();
             $durationType=array();
            foreach ($data['days'] as $leave) {
                $durationType[]=$leave->getDurationType();
                $leavesDate[]=$leave->getDate();
            
            }
            $dayCount=  count($durationType);
            $replacements['leaveType'] = $data['days'][0]->getLeaveType()->getName();
            $replacements['assigneeFullName'] = $data['days'][0]->getLeaveRequest()->getEmployee()->getFirstAndLastNames();
            $replacements['leaveFromDate']=$leavesDate[0];;
            $replacements['leaveToDate']=$leavesDate[$dayCount-1];
            $replacements['comments']=$data['days'][0]->getLeaveRequest()->getComments();
             if($durationType[0]==0 ||$durationType[0]==1){
                 $replacements['fromSession']="Session 1";
             }else{
                 $replacements['fromSession']="Session 2";
             }
            if($durationType[$dayCount-1]==0 ||$durationType[$dayCount-1]==2){
                $replacements['toSession']="Session 2"; 
            }else{
                $replacements['toSession']="Session 1"; 
            }
            $leaveTasks=$data['days'][0]->getLeaveRequest()->getLeaveTasks();
            $mentionTask=array();
            foreach ($leaveTasks as $task) {
               $mentionTask[$task->getTaskName()]=$task->getEmployee()->getFirstAndLastNames();
            }
            $replacements['contactPerson']=$mentionTask;
            $replacements['contactDetails']=$data['days'][0]->getLeaveRequest()->getContactNo();
        }

        $numberOfDays = 0;

        foreach ($data['days'] as $leave) {
            $numberOfDays += $leave->getLengthDays();
        }

        $replacements['numberOfDays'] = round($numberOfDays, 2);

        $replacements['leaveDetails'] = $this->_generateLeaveDetailsTable($data, $replacements);
        $replacements['link'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login <br /><br />';
        if(isset($data['conflictLeaveData'])){
            $replacements['conflictLeaveData']=$this->_generateLeaveConflictTable($data);
           
        }
        
        return $replacements;
    }

    protected function _generateLeaveDetailsTable($data, $replacements) {
          $table='<br /><br /><table style="width:100%;border-collapse: collapse;">
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">From Date</th>
                        <td style="border: 1px solid black">'.$replacements['leaveFromDate'].'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">To Date</th>
                        <td style="border: 1px solid black">'.$replacements['leaveToDate'].'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">From Session</th>
                        <td style="border: 1px solid black">'.$replacements['fromSession'].'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">To Session</th>
                        <td style="border: 1px solid black">'.$replacements['toSession'].'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Leave Type (PL / <br /> NPL / WFH / CO / <br /> EL)</th>
                        <td style="border: 1px solid black">'.$replacements['leaveType'].'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Reason of leave</th>
                        <td style="border: 1px solid black">'.$replacements['comments'].'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Contact Details <br /> during absence</th>
                        <td style="border: 1px solid black">'.$replacements['contactDetails'].'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">In my absence,<br /> following are the <br />alternative <br />contact points for<br /> mentioned tasks</th>
                        <td style="border: 1px solid black">';
                         foreach ($replacements['contactPerson'] as $task=>$name) {
                          $table .='<b>Task:</b>'.$task.'<br /><b>Contact person:</b>'.$name.'<br /><br />';
                         } 
                     $table .= '</td></tr></table><br /><br />';
//                     $table .='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login <br /><br />';
           return $table;

    }
    protected function _generateLeaveConflictTable($data){
        $details='
                    <br /><br />
                    <table style="width:100%;border-collapse: collapse;">
                     <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;padding: 5px;">Date(s)</th>
                        <th style="border: 1px solid black;padding: 5px;">Employee Name</th>
                        <th style="border: 1px solid black;padding: 5px;">Task</th>
                      </tr>';
          foreach ($data['conflictLeaveData'] as $data){
            $details .='<tr style="border: 1px solid black">
                            <td style="border: 1px solid black;padding: 5px;">';
                             foreach ($data['conflictedDate'] as $date){
                                 $details .=$date.'<br />';
                            }
                            $details.='</td>
                            <td style="border: 1px solid black;padding: 5px;">'.$data['empName'].'</td>
                            <td style="border: 1px solid black;padding: 5px;">'.$data['task'].'</td>
                        </tr>';
          }
        
    $details .='</table><br /><br />';
//    $details .='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login';
    $details .="<br />";   
        
        return $details;
    }

    protected function _fromatDuration($duration) {

        $formattedDuration = number_format($duration, 2);

        return $formattedDuration;

    }
    
    protected function trimComment($comment) {
        if (strlen($comment) > 30) {
            $comment = substr($comment, 0, 30) . '...';
        }
        return $comment;
    }

    public function getRecipients($emailName, $role, $data) {
        $recipients = array();
        
        switch ($role) {
            case 'subscriber' :
                $recipients = $this->getSubscribers($emailName, $data);
                break;
            case 'supervisor':                
                if (isset($data['days'][0])) {
                    $recipients = $this->getSupervisors($data['days'][0]->getEmpNumber(), $data);
                }
                break;
            case 'ess':                
                if (isset($data['days'][0])) {
                    $recipients = $this->getSelf($data['days'][0]->getEmpNumber());
                }
                break;     
           case 'subordinate':                
                if (isset($data['days'][0])) {
                    $recipients = $this->getSubordinate($data['days'][0]->getEmpNumber(),$data);
                }
                break;  
            default:
                if (isset($data['days'][0])) {
                    $recipients = $this->getEmployeesWithRole($role, $data['days'][0]->getEmpNumber());
                }
                break;
        }

        return $recipients;
    }
    
    protected function getEmployeesWithRole($role, $empNumber) {
        
        $entities = array('Employee' => $empNumber);
        $employees = $this->getUserRoleManager()->getEmployeesWithRole($role, $entities);
        
        return $employees;
            
    }
    
    protected function getSelf($empNumber) {
        $recipients = array();
        $performer = $this->getEmployeeService()->getEmployee($empNumber); 
        
        $to = $performer->getEmpWorkEmail();

        if (!empty($to)) {
            $recipients[] = $performer;
        }            
        
        return $recipients;
    }
    
    protected function getSubscribers($emailName, $data) {
        $subscribers = array();
        
        $notification = NULL;
        
        switch ($emailName) {
            case 'leave.apply':
                $notification = EmailNotification::LEAVE_APPLICATION;
                break;
            case 'leave.assign':
                $notification = EmailNotification::LEAVE_ASSIGNMENT;
                break;
            case 'leave.approve':
                $notification = EmailNotification::LEAVE_APPROVAL;
                break;
            case 'leave.cancel':
                $notification = EmailNotification::LEAVE_CANCELLATION;
                break;
            case 'leave.reject':
                $notification = EmailNotification::LEAVE_REJECTION;
                break;                
        }
        
        if (!is_null($notification)) {
            $subscribers = $this->getSubscribersForEvent($notification);
        }
        
        return $subscribers;
    }  
    
    public function getSupervisors($empNumber) {
        
        $recipients = array();
        
        $performer = $this->getEmployeeService()->getEmployee($empNumber);    
        
        // TODO: Do we need to send to supervisor chain?
        $supervisors = $performer->getSupervisors();

        if (count($supervisors) > 0) {

            foreach ($supervisors as $supervisor) {

                $to = $supervisor->getEmpWorkEmail();

                if (!empty($to)) {
                    $recipients[] = $supervisor;
                }
            }
        }
        
        return $recipients;
    }    
    
    public function getSubordinate($empNumber,$data) {
        
        $recipients = array();
       // TODO: Do we need to send to supervisor chain?
        
        $contactPersons=null;
        if(isset($data['contactPersons']) && $data['contactPersons'] !=null ){
           $contactPersons=$data['contactPersons'];  
        } elseif($data['conflictLeaveData'] && $data['conflictLeaveData'] !=null ){
            $contactPersons=$data['conflictLeaveData'];
        }else{
            if(isset($data['employeeIds']) && !empty($data['employeeIds'])){
             $employeeIds=$data['employeeIds'];
            }
        }
        if(isset($contactPersons) && $contactPersons !=null){
        $employeeIds=array();
        foreach ($contactPersons as $contactPerson) {
            if(!in_array($contactPerson['empId'], $employeeIds)){
                $employeeIds[]=$contactPerson['empId'];
            }
        }
      }
      
  
            if(!empty ($employeeIds)) {
                 foreach ($employeeIds as $Id) {
                   $person = $this->getEmployeeService()->getEmployee($Id);   
                   $to = $person->getEmpWorkEmail();
                   if (!empty($to)) { $recipients[] = $person;}
                   }
              }
            
        
        
        return $recipients;
    }    
    
}
