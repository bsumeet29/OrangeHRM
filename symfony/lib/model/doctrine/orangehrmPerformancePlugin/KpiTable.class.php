<?php

/**
 * KpiTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class KpiTable extends PluginKpiTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object KpiTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Kpi');
    }
}