<?php

/**
 * CompoffRequestCommentTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CompoffRequestCommentTable extends PluginCompoffRequestCommentTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object CompoffRequestCommentTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('CompoffRequestComment');
    }
}