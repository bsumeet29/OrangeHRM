<?php

/**
 * BaseLogins
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $userId
 * @property string $userName
 * @property string $userRoleName
 * @property boolean $userRolePredefined
 * @property timestamp $loginTime
 * 
 * @method integer   getId()                 Returns the current record's "id" value
 * @method integer   getUserId()             Returns the current record's "userId" value
 * @method string    getUserName()           Returns the current record's "userName" value
 * @method string    getUserRoleName()       Returns the current record's "userRoleName" value
 * @method boolean   getUserRolePredefined() Returns the current record's "userRolePredefined" value
 * @method timestamp getLoginTime()          Returns the current record's "loginTime" value
 * @method Logins    setId()                 Sets the current record's "id" value
 * @method Logins    setUserId()             Sets the current record's "userId" value
 * @method Logins    setUserName()           Sets the current record's "userName" value
 * @method Logins    setUserRoleName()       Sets the current record's "userRoleName" value
 * @method Logins    setUserRolePredefined() Sets the current record's "userRolePredefined" value
 * @method Logins    setLoginTime()          Sets the current record's "loginTime" value
 * @property  $
 * 
 * @package    orangehrm
 * @subpackage model\core\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLogins extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_module_default_page');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user_id as userId', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('user_name as userName', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('userRoleName', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('user_role_predefined as userRolePredefined', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('login_time AS loginTime', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => 25,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('User', array(
             'local' => 'user_id',
             'foreign' => 'id'));
    }
}