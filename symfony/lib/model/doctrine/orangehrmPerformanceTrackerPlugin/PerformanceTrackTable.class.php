<?php

/**
 * PerformanceTrackTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PerformanceTrackTable extends PluginPerformanceTrackTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object PerformanceTrackTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PerformanceTrack');
    }
}