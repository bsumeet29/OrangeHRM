<?php

/**
 * DataPointTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class DataPointTable extends PluginDataPointTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object DataPointTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DataPoint');
    }
}