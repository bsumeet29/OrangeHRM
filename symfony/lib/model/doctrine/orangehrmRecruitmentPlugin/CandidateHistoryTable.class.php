<?php

/**
 * CandidateHistoryTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CandidateHistoryTable extends PluginCandidateHistoryTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object CandidateHistoryTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('CandidateHistory');
    }
}