<?php

/**
 * ProjectActivityTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ProjectActivityTable extends PluginProjectActivityTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object ProjectActivityTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ProjectActivity');
    }
}