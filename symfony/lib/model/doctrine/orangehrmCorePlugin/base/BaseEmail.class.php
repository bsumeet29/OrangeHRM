<?php

/**
 * BaseEmail
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property Doctrine_Collection $EmailProcessor
 * @property Doctrine_Collection $EmailTemplate
 * 
 * @method integer             getId()             Returns the current record's "id" value
 * @method string              getName()           Returns the current record's "name" value
 * @method Doctrine_Collection getEmailProcessor() Returns the current record's "EmailProcessor" collection
 * @method Doctrine_Collection getEmailTemplate()  Returns the current record's "EmailTemplate" collection
 * @method Email               setId()             Sets the current record's "id" value
 * @method Email               setName()           Sets the current record's "name" value
 * @method Email               setEmailProcessor() Sets the current record's "EmailProcessor" collection
 * @method Email               setEmailTemplate()  Sets the current record's "EmailTemplate" collection
 * 
 * @package    orangehrm
 * @subpackage model\core\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEmail extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_email');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 100,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('EmailProcessor', array(
             'local' => 'id',
             'foreign' => 'email_id'));

        $this->hasMany('EmailTemplate', array(
             'local' => 'id',
             'foreign' => 'email_id'));
    }
}