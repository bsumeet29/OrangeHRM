<?php

/**
 * BaseWfhRequestComment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $wfh_request_id
 * @property datetime $created
 * @property string $created_by_name
 * @property integer $created_by_id
 * @property integer $created_by_emp_number
 * @property string $comments
 * @property Employee $Employee
 * @property SystemUser $SystemUser
 * @property WfhRequest $WfhRequest
 * 
 * @method integer           getId()                    Returns the current record's "id" value
 * @method integer           getWfhRequestId()          Returns the current record's "wfh_request_id" value
 * @method datetime          getCreated()               Returns the current record's "created" value
 * @method string            getCreatedByName()         Returns the current record's "created_by_name" value
 * @method integer           getCreatedById()           Returns the current record's "created_by_id" value
 * @method integer           getCreatedByEmpNumber()    Returns the current record's "created_by_emp_number" value
 * @method string            getComments()              Returns the current record's "comments" value
 * @method Employee          getEmployee()              Returns the current record's "Employee" value
 * @method SystemUser        getSystemUser()            Returns the current record's "SystemUser" value
 * @method WfhRequest        getWfhRequest()            Returns the current record's "WfhRequest" value
 * @method WfhRequestComment setId()                    Sets the current record's "id" value
 * @method WfhRequestComment setWfhRequestId()          Sets the current record's "wfh_request_id" value
 * @method WfhRequestComment setCreated()               Sets the current record's "created" value
 * @method WfhRequestComment setCreatedByName()         Sets the current record's "created_by_name" value
 * @method WfhRequestComment setCreatedById()           Sets the current record's "created_by_id" value
 * @method WfhRequestComment setCreatedByEmpNumber()    Sets the current record's "created_by_emp_number" value
 * @method WfhRequestComment setComments()              Sets the current record's "comments" value
 * @method WfhRequestComment setEmployee()              Sets the current record's "Employee" value
 * @method WfhRequestComment setSystemUser()            Sets the current record's "SystemUser" value
 * @method WfhRequestComment setWfhRequest()            Sets the current record's "WfhRequest" value
 * 
 * @package    orangehrm
 * @subpackage model\leave\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseWfhRequestComment extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_wfh_request_comment');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('wfh_request_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('created', 'datetime', null, array(
             'type' => 'datetime',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('created_by_name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('created_by_id', 'integer', 10, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => 10,
             ));
        $this->hasColumn('created_by_emp_number', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => 4,
             ));
        $this->hasColumn('comments', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Employee', array(
             'local' => 'created_by_emp_number',
             'foreign' => 'emp_number'));

        $this->hasOne('SystemUser', array(
             'local' => 'created_by_id',
             'foreign' => 'id'));

        $this->hasOne('WfhRequest', array(
             'local' => 'wfh_request_id',
             'foreign' => 'id'));
    }
}