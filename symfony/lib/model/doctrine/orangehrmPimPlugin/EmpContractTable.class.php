<?php

/**
 * EmpContractTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class EmpContractTable extends PluginEmpContractTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object EmpContractTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('EmpContract');
    }
}