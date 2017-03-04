<?php

/**
 * BaseSystemUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $user_role_id
 * @property integer $emp_number
 * @property string $user_name
 * @property string $user_password
 * @property integer $deleted
 * @property integer $status
 * @property timestamp $date_entered
 * @property timestamp $date_modified
 * @property integer $modified_user_id
 * @property integer $created_by
 * @property Employee $Employee
 * @property UserRole $UserRole
 * @property Doctrine_Collection $Login
 * @property Doctrine_Collection $LeaveEntitlement
 * @property Doctrine_Collection $LeaveAdjustment
 * @property Doctrine_Collection $LeaveRequestComment
 * @property Doctrine_Collection $LeaveComment
 * @property Doctrine_Collection $CompoffComment
 * @property Doctrine_Collection $CompoffRequestComment
 * @property Doctrine_Collection $WfhComment
 * @property Doctrine_Collection $WfhRequestComment
 * @property Doctrine_Collection $PerformanceTrackerLog
 * @property Doctrine_Collection $TimesheetActionLog
 * 
 * @method integer             getId()                    Returns the current record's "id" value
 * @method integer             getUserRoleId()            Returns the current record's "user_role_id" value
 * @method integer             getEmpNumber()             Returns the current record's "emp_number" value
 * @method string              getUserName()              Returns the current record's "user_name" value
 * @method string              getUserPassword()          Returns the current record's "user_password" value
 * @method integer             getDeleted()               Returns the current record's "deleted" value
 * @method integer             getStatus()                Returns the current record's "status" value
 * @method timestamp           getDateEntered()           Returns the current record's "date_entered" value
 * @method timestamp           getDateModified()          Returns the current record's "date_modified" value
 * @method integer             getModifiedUserId()        Returns the current record's "modified_user_id" value
 * @method integer             getCreatedBy()             Returns the current record's "created_by" value
 * @method Employee            getEmployee()              Returns the current record's "Employee" value
 * @method UserRole            getUserRole()              Returns the current record's "UserRole" value
 * @method Doctrine_Collection getLogin()                 Returns the current record's "Login" collection
 * @method Doctrine_Collection getLeaveEntitlement()      Returns the current record's "LeaveEntitlement" collection
 * @method Doctrine_Collection getLeaveAdjustment()       Returns the current record's "LeaveAdjustment" collection
 * @method Doctrine_Collection getLeaveRequestComment()   Returns the current record's "LeaveRequestComment" collection
 * @method Doctrine_Collection getLeaveComment()          Returns the current record's "LeaveComment" collection
 * @method Doctrine_Collection getCompoffComment()        Returns the current record's "CompoffComment" collection
 * @method Doctrine_Collection getCompoffRequestComment() Returns the current record's "CompoffRequestComment" collection
 * @method Doctrine_Collection getWfhComment()            Returns the current record's "WfhComment" collection
 * @method Doctrine_Collection getWfhRequestComment()     Returns the current record's "WfhRequestComment" collection
 * @method Doctrine_Collection getPerformanceTrackerLog() Returns the current record's "PerformanceTrackerLog" collection
 * @method Doctrine_Collection getTimesheetActionLog()    Returns the current record's "TimesheetActionLog" collection
 * @method SystemUser          setId()                    Sets the current record's "id" value
 * @method SystemUser          setUserRoleId()            Sets the current record's "user_role_id" value
 * @method SystemUser          setEmpNumber()             Sets the current record's "emp_number" value
 * @method SystemUser          setUserName()              Sets the current record's "user_name" value
 * @method SystemUser          setUserPassword()          Sets the current record's "user_password" value
 * @method SystemUser          setDeleted()               Sets the current record's "deleted" value
 * @method SystemUser          setStatus()                Sets the current record's "status" value
 * @method SystemUser          setDateEntered()           Sets the current record's "date_entered" value
 * @method SystemUser          setDateModified()          Sets the current record's "date_modified" value
 * @method SystemUser          setModifiedUserId()        Sets the current record's "modified_user_id" value
 * @method SystemUser          setCreatedBy()             Sets the current record's "created_by" value
 * @method SystemUser          setEmployee()              Sets the current record's "Employee" value
 * @method SystemUser          setUserRole()              Sets the current record's "UserRole" value
 * @method SystemUser          setLogin()                 Sets the current record's "Login" collection
 * @method SystemUser          setLeaveEntitlement()      Sets the current record's "LeaveEntitlement" collection
 * @method SystemUser          setLeaveAdjustment()       Sets the current record's "LeaveAdjustment" collection
 * @method SystemUser          setLeaveRequestComment()   Sets the current record's "LeaveRequestComment" collection
 * @method SystemUser          setLeaveComment()          Sets the current record's "LeaveComment" collection
 * @method SystemUser          setCompoffComment()        Sets the current record's "CompoffComment" collection
 * @method SystemUser          setCompoffRequestComment() Sets the current record's "CompoffRequestComment" collection
 * @method SystemUser          setWfhComment()            Sets the current record's "WfhComment" collection
 * @method SystemUser          setWfhRequestComment()     Sets the current record's "WfhRequestComment" collection
 * @method SystemUser          setPerformanceTrackerLog() Sets the current record's "PerformanceTrackerLog" collection
 * @method SystemUser          setTimesheetActionLog()    Sets the current record's "TimesheetActionLog" collection
 * 
 * @package    orangehrm
 * @subpackage model\core\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSystemUser extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_user');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user_role_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('emp_number', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('user_name', 'string', 40, array(
             'type' => 'string',
             'length' => 40,
             ));
        $this->hasColumn('user_password', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('deleted', 'integer', 1, array(
             'type' => 'integer',
             'default' => '0',
             'length' => 1,
             ));
        $this->hasColumn('status', 'integer', 1, array(
             'type' => 'integer',
             'default' => '1',
             'length' => 1,
             ));
        $this->hasColumn('date_entered', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => 25,
             ));
        $this->hasColumn('date_modified', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => 25,
             ));
        $this->hasColumn('modified_user_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('created_by', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Employee', array(
             'local' => 'emp_number',
             'foreign' => 'emp_number'));

        $this->hasOne('UserRole', array(
             'local' => 'user_role_id',
             'foreign' => 'id'));

        $this->hasMany('Login', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('LeaveEntitlement', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('LeaveAdjustment', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('LeaveRequestComment', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('LeaveComment', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('CompoffComment', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('CompoffRequestComment', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('WfhComment', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('WfhRequestComment', array(
             'local' => 'id',
             'foreign' => 'created_by_id'));

        $this->hasMany('PerformanceTrackerLog', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('TimesheetActionLog', array(
             'local' => 'id',
             'foreign' => 'performed_by'));
    }
}