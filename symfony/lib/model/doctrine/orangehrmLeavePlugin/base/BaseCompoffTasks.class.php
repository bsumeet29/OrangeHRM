<?php

/**
 * BaseCompoffTasks
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $compoff_request_id
 * @property string $task_name
 * @property CompoffRequest $CompoffRequest
 * 
 * @method integer        getId()                 Returns the current record's "id" value
 * @method integer        getCompoffRequestId()   Returns the current record's "compoff_request_id" value
 * @method string         getTaskName()           Returns the current record's "task_name" value
 * @method CompoffRequest getCompoffRequest()     Returns the current record's "CompoffRequest" value
 * @method CompoffTasks   setId()                 Sets the current record's "id" value
 * @method CompoffTasks   setCompoffRequestId()   Sets the current record's "compoff_request_id" value
 * @method CompoffTasks   setTaskName()           Sets the current record's "task_name" value
 * @method CompoffTasks   setCompoffRequest()     Sets the current record's "CompoffRequest" value
 * 
 * @package    orangehrm
 * @subpackage model\leave\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCompoffTasks extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_compoff_tasks');
        $this->hasColumn('id', 'integer', 10, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 10,
             ));
        $this->hasColumn('compoff_request_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('task_name', 'string', 100, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 100,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('CompoffRequest', array(
             'local' => 'compoff_request_id',
             'foreign' => 'id'));
    }
}