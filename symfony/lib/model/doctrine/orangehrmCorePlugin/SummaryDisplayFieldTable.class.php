<?php

/**
 * SummaryDisplayFieldTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SummaryDisplayFieldTable extends PluginSummaryDisplayFieldTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object SummaryDisplayFieldTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('SummaryDisplayField');
    }
}