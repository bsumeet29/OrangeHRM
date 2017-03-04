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
class CompoffTasksDao extends BaseDao {
    
     /**
     * Add and Update CompoffTasks
     * @param compoffTasks $compofftasks
     * @return boolean
     */
    public function saveCompoffTasks(CompoffTasks $compofftasks){
        try {
            $compofftasks->save();
            return $compofftasks;
         } catch (Exception $e) {
            throw new DaoException($e->getMessage());
            }
      }
      
    /**
     * Read CompoffTasks by given compoffTasks id
     * @param $compoffTaskId
     * @return CompoffTasks
     */   
   public function readCompoffTasks($compoffTaskId) {
        try {
            $leavetasks = Doctrine::getTable('CompoffTasks')
                       ->find($compoffTaskId);

            return $leavetasks;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    } 
      
   public function fetchCompoffTasksList($compoffRequestId) {
            $q = Doctrine_Query::create()
                ->select('*')
                ->from('CompoffTasks c')
                ->where('c.compoff_request_id = ?', $compoffRequestId);

        return $q->execute();
    }
    
}

?>
