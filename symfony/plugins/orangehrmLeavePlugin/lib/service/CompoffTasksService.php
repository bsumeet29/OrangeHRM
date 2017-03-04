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
class CompoffTasksService extends BaseService {

    private $compoffTasksDao;

    public function getCompoffTasksDao() {
        if (!($this->compoffTasksDao instanceof CompoffTasksDao)) {
            $this->compoffTasksDao = new CompoffTasksDao();
        }
        return $this->compoffTasksDao;
    }

    public function setCompoffTasksDao(CompoffTasksDao $compoffTasksDao) {
        $this->compoffTasksDao = $compoffTasksDao;
    }

    public function saveCompoffTasks($compoffRequestId, $compoffTasks) {
        foreach ($compoffTasks as $task) {
            $compoffTasks = new CompoffTasks();
            $compoffTasks->setCompoffRequestId($compoffRequestId);
            $compoffTasks->setTaskName($task);
            $this->getCompoffTasksDao()->saveCompoffTasks($compoffTasks);
        }
    }

}

?>
