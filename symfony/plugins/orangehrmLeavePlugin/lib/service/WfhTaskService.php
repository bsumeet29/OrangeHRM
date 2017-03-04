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
class WfhTasksService extends BaseService {

    private $wfhTasksDao;

    public function getWfhTasksDao() {
        if (!($this->wfhTasksDao instanceof WfhTasksDao)) {
            $this->wfhTasksDao = new WfhTasksDao();
        }
        return $this->wfhTasksDao;
    }

    public function setWfhTasksDao(WfhTasksDao $wfhTasksDao) {
        $this->wfhTasksDao = $wfhTasksDao;
    }

    public function saveWfhTasks($wfhRequestId, $wfhTasks) {
        foreach ($wfhTasks as $task) {
            $wfhTasks = new WfhTasks();
            $wfhTasks->setWfhRequestId($wfhRequestId);
            $wfhTasks->setTaskName($task);
            $this->getWfhTasksDao()->saveWfhTasks($wfhTasks);
        }
    }

}

?>
