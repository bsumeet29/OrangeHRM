<?php

/**
 * EmployeeAttachmentTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class EmployeeAttachmentTable extends PluginEmployeeAttachmentTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object EmployeeAttachmentTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('EmployeeAttachment');
    }
}