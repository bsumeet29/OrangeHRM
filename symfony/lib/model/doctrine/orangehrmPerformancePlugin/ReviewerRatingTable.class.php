<?php

/**
 * ReviewerRatingTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ReviewerRatingTable extends PluginReviewerRatingTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object ReviewerRatingTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ReviewerRating');
    }
}