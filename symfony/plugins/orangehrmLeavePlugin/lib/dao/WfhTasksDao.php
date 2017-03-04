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
class WfhTasksDao extends BaseDao {
    
     /**
     * Add and Update CompoffTasks
     * @param compoffTasks $compofftasks
     * @return boolean
     */
    public function saveWfhTasks(WfhTasks $wfhtasks){
        try {
            $wfhtasks->save();
            return $wfhtasks;
         } catch (Exception $e) {
            throw new DaoException($e->getMessage());
            }
      }
      
    /**
     * Read CompoffTasks by given compoffTasks id
     * @param $compoffTaskId
     * @return CompoffTasks
     */   
   public function readWfhTasks($wfhTaskId) {
        try {
            $wfhtasks = Doctrine::getTable('WfhTasks')
                       ->find($wfhTaskId);

            return $wfhtasks;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    } 
      
   public function fetchWfhTasksList($wfhRequestId) {
            $q = Doctrine_Query::create()
                ->select('*')
                ->from('WfhTasks c')
                ->where('c.wfh_request_id = ?', $wfhRequestId);

        return $q->execute();
    }
    
}

?>
