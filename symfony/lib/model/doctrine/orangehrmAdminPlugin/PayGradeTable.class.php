<?php

/**
 * PayGradeTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PayGradeTable extends PluginPayGradeTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object PayGradeTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PayGrade');
    }
}