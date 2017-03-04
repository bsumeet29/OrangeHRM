<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LeaveTasksDao
 *
 * @author firoj
 */
class LeaveTasksDao extends BaseDao {
    
     /**
     * Add and Update LeaveTasks
     * @param LeaveTasks $leavetasks
     * @return boolean
     */
    public function saveLeaveTasks(LeaveTasks $leavetasks){
        try {
            $leavetasks->save();
            return $leavetasks;
         } catch (Exception $e) {
            throw new DaoException($e->getMessage());
            }
      }
      
    /**
     * Read LeaveTasks by given LeaveTasks id
     * @param $leaveTaskId
     * @return LeaveTasks
     */   
   public function readLeaveTasks($leaveTaskId) {
        try {
            $leavetasks = Doctrine::getTable('LeaveTasks')
                       ->find($leaveTaskId);

            return $leavetasks;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    } 
      
   public function fetchLeaveTasksList($leaveRequestId) {
            $q = Doctrine_Query::create()
                ->select('*')
                ->from('LeaveTasks l')
                ->where('l.leave_request_id = ?', $leaveRequestId);

        return $q->execute();
    }
    
}

?>
