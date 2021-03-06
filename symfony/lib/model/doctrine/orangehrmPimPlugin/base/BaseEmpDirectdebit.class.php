<?php

/**
 * BaseEmpDirectdebit
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $salary_id
 * @property string $routing_num
 * @property string $account
 * @property string $nominee_name
 * @property string $pf_number
 * @property string $nominee_name_pf
 * @property string $branch_name
 * @property decimal $amount
 * @property string $account_type
 * @property string $transaction_type
 * @property EmployeeSalary $salary
 * 
 * @method integer        getId()               Returns the current record's "id" value
 * @method integer        getSalaryId()         Returns the current record's "salary_id" value
 * @method string         getRoutingNum()       Returns the current record's "routing_num" value
 * @method string         getAccount()          Returns the current record's "account" value
 * @method string         getNomineeName()      Returns the current record's "nominee_name" value
 * @method string         getPfNumber()         Returns the current record's "pf_number" value
 * @method string         getNomineeNamePf()    Returns the current record's "nominee_name_pf" value
 * @method string         getBranchName()       Returns the current record's "branch_name" value
 * @method decimal        getAmount()           Returns the current record's "amount" value
 * @method string         getAccountType()      Returns the current record's "account_type" value
 * @method string         getTransactionType()  Returns the current record's "transaction_type" value
 * @method EmployeeSalary getSalary()           Returns the current record's "salary" value
 * @method EmpDirectdebit setId()               Sets the current record's "id" value
 * @method EmpDirectdebit setSalaryId()         Sets the current record's "salary_id" value
 * @method EmpDirectdebit setRoutingNum()       Sets the current record's "routing_num" value
 * @method EmpDirectdebit setAccount()          Sets the current record's "account" value
 * @method EmpDirectdebit setNomineeName()      Sets the current record's "nominee_name" value
 * @method EmpDirectdebit setPfNumber()         Sets the current record's "pf_number" value
 * @method EmpDirectdebit setNomineeNamePf()    Sets the current record's "nominee_name_pf" value
 * @method EmpDirectdebit setBranchName()       Sets the current record's "branch_name" value
 * @method EmpDirectdebit setAmount()           Sets the current record's "amount" value
 * @method EmpDirectdebit setAccountType()      Sets the current record's "account_type" value
 * @method EmpDirectdebit setTransactionType()  Sets the current record's "transaction_type" value
 * @method EmpDirectdebit setSalary()           Sets the current record's "salary" value
 * 
 * @package    orangehrm
 * @subpackage model\pim\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEmpDirectdebit extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('hs_hr_emp_directdebit');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('salary_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('dd_routing_num as routing_num', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             ));
        $this->hasColumn('dd_account as account', 'string', 100, array(
             'type' => 'string',
             'default' => '',
             'notnull' => true,
             'length' => 100,
             ));
        $this->hasColumn('nominee_name', 'string', 500, array(
             'type' => 'string',
             'default' => '',
             'length' => 500,
             ));
        $this->hasColumn('pf_number', 'string', 100, array(
             'type' => 'string',
             'default' => '',
             'length' => 100,
             ));
        $this->hasColumn('nominee_name_pf', 'string', 500, array(
             'type' => 'string',
             'default' => '',
             'length' => 500,
             ));
        $this->hasColumn('branch_name', 'string', 500, array(
             'type' => 'string',
             'default' => '',
             'length' => 500,
             ));
        $this->hasColumn('dd_amount as amount', 'decimal', 11, array(
             'type' => 'decimal',
             'notnull' => false,
             'scale' => false,
             'length' => 11,
             ));
        $this->hasColumn('dd_account_type as account_type', 'string', 20, array(
             'type' => 'string',
             'default' => '',
             'notnull' => false,
             'length' => 20,
             ));
        $this->hasColumn('dd_transaction_type as transaction_type', 'string', 20, array(
             'type' => 'string',
             'default' => '',
             'notnull' => false,
             'length' => 20,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('EmployeeSalary as salary', array(
             'local' => 'salary_id',
             'foreign' => 'id',
             'onDelete' => 'Cascade'));
    }
}