<?php

/**
 * EmailTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class EmailTable extends PluginEmailTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object EmailTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Email');
    }
}