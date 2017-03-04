<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CompOffService
 *
 * @author firoj
 */
class CompOffService extends BaseService{
    private $compOffDao;
    private $workScheduleService;
    private $leaveTasksService;
    private $employeeService;
    
     public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }
    
    public function getLeaveTasksService() {
        if (empty($this->leaveTasksService)) {
            $this->leaveTasksService = new LeaveTasksService();
        }
        return $this->leaveTasksService;
    }

    public function setLeaveTasksService(LeaveTasksService $leaveTasksService) {
        $this->leaveTasksService = $leaveTasksService;
    }
    
   public function getCompOffDao() {
            if (!($this->compOffDao instanceof CompOffDao)) {
                        $this->compOffDao = new CompOffDao();
                    }
                    return $this->compOffDao;
        }

        public function setCompOffDao(CompOffDao $compOffDao) {
            $this->compOffDao = $compOffDao;
        }
        
       public function getWorkScheduleService() {
        if (!($this->workScheduleService instanceof WorkScheduleService)) {
            $this->workScheduleService = new WorkScheduleService();
        }
        return $this->workScheduleService;
       }
       
        public function setWorkScheduleService(WorkScheduleService $service) {
        $this->workScheduleService = $service;
        } 

        public function saveCompOff(CompOffParameterObject $compOffParameter){
           $data= $this->getDuration($compOffParameter);
            $compOff=new CompOff();
            $compOff->setCalledEmpNumber($compOffParameter->getCalled_emp_number());
            $compOff->setApplyEmpNumber($compOffParameter->getApply_emp_number());
            $compOff->setWorkType($compOffParameter->getWorkType());
            $compOff->setReason($compOffParameter->getReason());
            $compOff->setFromDate($compOffParameter->getFromDate());
            $compOff->setToDate($compOffParameter->getToDate());
            $compOff->setCreditedDate($compOffParameter->getApply_date());
            $compOff->setApproved($compOffParameter->getApproved());
            $compOff->setNoOfDays($data['dayLength']);
            $compOff->setFromSession($data['fromSession']);
            $compOff->setToSession($data['toSession']);
            $savecompOff=$this->getCompOffDao()->saveCompOff($compOff);
            $this->getLeaveTasksService()->saveCompOffTasks($savecompOff->getId(),$compOffParameter);
            $this->sendEmailForCompOff($savecompOff);
            return $savecompOff;
         }
         
         public function getDuration(CompOffParameterObject $compOffParameter){
             
              $from = strtotime($compOffParameter->getFromDate());
              $to = strtotime($compOffParameter->getToDate());
              $firstDay = true;
              $dayLength=0;
              $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($compOffParameter->getApply_emp_number());
              $workScheduleDuration = $workSchedule->getWorkShiftLength();
              if($compOffParameter->getMultiDayLeave()){
                  $durationInHours=0;
                 for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {
                  $lastDay=($timeStamp == $to) ? true : false;
                  if($compOffParameter->getMultiDayPartialOption() == ''){
                      $durationInHours += $workScheduleDuration;
                  }else{
                      if($firstDay && ($compOffParameter->getMultiDayPartialOption() == 'start' || $compOffParameter->getMultiDayPartialOption()== 'start_end')){
                         $durationInHours += $workScheduleDuration / 2; 
                         $firstDay=false;
                      }elseif ($lastDay && ($compOffParameter->getMultiDayPartialOption() == 'end' || $compOffParameter->getMultiDayPartialOption()== 'start_end')) {
                       $durationInHours += $workScheduleDuration / 2; 
                       $lastDay=false;
                     }else{
                         $durationInHours += $workScheduleDuration; 
                     }
                  }
                }
                 
                if($compOffParameter->getMultiDayPartialOption() == ''){
                   $fromSession="Session 1";
                   $toSession="Session 2";
               }  else {
                   $fromSession="Session 1";
                   $toSession="Session 2";
                   if($compOffParameter->getMultiDayPartialOption() == 'start' || $compOffParameter->getMultiDayPartialOption()== 'start_end'){
                       $fromSession=($compOffParameter->getFirstMultiDayDuration()->getAmPm()== LeaveDuration::HALF_DAY_AM)?'Session 1':'Session 2';
                   }
                   if($compOffParameter->getMultiDayPartialOption() == 'end' || $compOffParameter->getMultiDayPartialOption()== 'start_end') {
                       
                       $toSession=($compOffParameter->getSecondMultiDayDuration()->getAmPm()== LeaveDuration::HALF_DAY_AM)?'Session 1':'Session 2';
                   }
               } 
                
              $dayLength +=number_format($durationInHours / $workScheduleDuration, 3);
              return array('fromSession'=>$fromSession,'toSession'=>$toSession,'dayLength'=>$dayLength);  
                
              }else{
                  // Single day leave:
                  if($compOffParameter->getSingleDayDuration()->getType()==LeaveDuration::FULL_DAY){
                      $fromSession="Session 1";
                      $toSession="Session 2";
                      $durationInHours = $workScheduleDuration;
                  }else{
                      if($compOffParameter->getSingleDayDuration()->getAmPm()== LeaveDuration::HALF_DAY_AM){
                       $fromSession="Session 1";
                       $toSession="Session 1";
                      }else{
                          $fromSession="Session 2";
                          $toSession="Session 2";
                      }
                      $durationInHours = $workScheduleDuration / 2; 
                  }
                  $dayLength +=number_format($durationInHours / $workScheduleDuration, 3);
                  return array('fromSession'=>$fromSession,'toSession'=>$toSession,'dayLength'=>$dayLength); 
              }
         
      }
         
         private function incDate($timestamp) {
          return strtotime("+1 day", $timestamp);
         }
         
         public function sendEmailForCompOff(CompOff $saveCompOff){
             $employee=  $this->getEmployeeService()->getEmployee($saveCompOff->getCalledEmpNumber());
             $calledEmployeeName=$employee->getFullName();
             $tasks=$saveCompOff->getLeaveTasks();
            
             $table='<table style="width:100%;border-collapse: collapse;">
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">From Date</th>
                        <td style="border: 1px solid black">'.$saveCompOff->getFromDate().'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">To Date</th>
                        <td style="border: 1px solid black">'.$saveCompOff->getToDate().'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">From Session</th>
                        <td style="border: 1px solid black">'.$saveCompOff->getFromSession().'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">To Session</th>
                        <td style="border: 1px solid black">'.$saveCompOff->getToSession().'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Reason</th>
                        <td style="border: 1px solid black">'.$saveCompOff->getReason().'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Called By</th>
                        <td style="border: 1px solid black">'.$calledEmployeeName.'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Work Type <br />(Urgent Deliverable / Pending task <br />/ Training)</th>
                        <td style="border: 1px solid black">'.$saveCompOff->getWorkType().'</td>
                      </tr>
                      <tr style="border: 1px solid black">
                        <th style="border: 1px solid black;text-align: left;padding: 5px;">Expected tasks to be <br /> completed for the above day(s)</th>
                        <td style="border: 1px solid black">';
                         foreach ($tasks as $key=> $task) {
                             
                          $table .='<b>Task'.($key+1).':</b>'.$task->getTaskName().'<br /><br />';
                         } 
                     $table .= '</td></tr></table><br /><br />';
                    
         }
         
      
}


?>
