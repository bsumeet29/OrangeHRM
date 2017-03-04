<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LeaveTasksService
 *
 * @author firoj
 */
class LeaveTasksService extends BaseService{
     private $leaveTasksDao;
     
     public function getLeaveTasksDao() {
        if (!($this->leaveTasksDao instanceof LeaveTasksDao)) {
            $this->leaveTasksDao = new LeaveTasksDao();
        }
        return $this->leaveTasksDao;
    }

    public function setLeaveTasksDao(LeaveTasksDao $leaveTasksDao) {
        $this->leaveTasksDao = $leaveTasksDao;
    }
    
     public function saveLeaveTasks($leaveRequestId,$contactPersons){
        foreach ($contactPersons as $contactPerson) {
               $leaveTasks =new LeaveTasks();
               $leaveTasks->setLeaveRequestId($leaveRequestId);
               $leaveTasks->setTaskName($contactPerson['task']);
               $leaveTasks->setContactEmpNumber($contactPerson['empId']);
               $leaveTasks->setTaskType('requested');
               $leaveTasks->setStatus(0);
               $this->getLeaveTasksDao()->saveLeaveTasks($leaveTasks);
        }
       
    }
    
     public function saveCompOffTasks($compOffId,  CompOffParameterObject $compOffParameter){
       
              $compOffTasks=$compOffParameter->getTasks();
              $contact_emp_number=$compOffParameter->getApply_emp_number();
              if(!empty($compOffTasks) && $compOffId != NULL){
              foreach ($compOffTasks as $task){
               $leaveTasks =new LeaveTasks();
               $leaveTasks->setCompOffId($compOffId);
               $leaveTasks->setTaskName($task);
               $leaveTasks->setContactEmpNumber($contact_emp_number);
               $leaveTasks->setTaskType('Comp Off');
               $leaveTasks->setStatus(1);
               $this->getLeaveTasksDao()->saveLeaveTasks($leaveTasks);
              }
            }
               
     }
}

?>
