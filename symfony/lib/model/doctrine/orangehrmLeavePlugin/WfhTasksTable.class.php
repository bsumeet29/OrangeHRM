<?php

/**
 * WfhTasksTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class WfhTasksTable extends PluginWfhTasksTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object WfhTasksTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('WfhTasks');
    }
}