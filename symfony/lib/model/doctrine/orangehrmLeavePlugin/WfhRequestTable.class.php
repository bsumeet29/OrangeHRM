<?php

/**
 * WfhRequestTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class WfhRequestTable extends PluginWfhRequestTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object WfhRequestTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('WfhRequest');
    }
}