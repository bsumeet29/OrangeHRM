<?php

/**
 * EmployeeLanguageTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class EmployeeLanguageTable extends PluginEmployeeLanguageTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object EmployeeLanguageTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('EmployeeLanguage');
    }
}