<?php

/**
 * JobSpecificationAttachmentTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class JobSpecificationAttachmentTable extends PluginJobSpecificationAttachmentTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object JobSpecificationAttachmentTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('JobSpecificationAttachment');
    }
}