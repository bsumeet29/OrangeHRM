<?php

/**
 * EmploymentStatusTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class EmploymentStatusTable extends PluginEmploymentStatusTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object EmploymentStatusTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('EmploymentStatus');
    }
}