<?php
// Call UniqueIDGeneratorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "UniqueIDGeneratorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once 'UniqueIDGenerator.php';

/**
 * Test class for UniqueIDGenerator.
 * Generated by PHPUnit_Util_Skeleton on 2007-07-20 at 11:23:47.
 */
class UniqueIDGeneratorTest extends PHPUnit_Framework_TestCase {

	private $oldValues;
	private $connection;

	private $tableInfo = array(array("hs_hr_compstructtree", "id", null),
							array("ohrm_customer", "customer_id", null),
							array("hs_hr_education", "edu_code", "EDU"),
							array("ohrm_job_category", "eec_code", "EEC"),
							array("hs_hr_employee", "emp_number", null),
							array("hs_hr_ethnic_race", "ethnic_race_code", "ETH"),
							array("hs_hr_job_title", "jobtit_code", "JOB"),
							array("hs_hr_leave", "leave_id", null),
							array("hs_hr_leave_requests", "leave_request_id", null),
							array("hs_hr_leavetype", "leave_type_id", null),
							array("hs_hr_licenses", "licenses_code", "LIC"),
							array("hs_hr_language", "lang_code", "LAN"),
							array("hs_hr_membership", "membship_code", "MME"),
							array("hs_hr_membership_type", "membtype_code", "MEM"),
							array("hs_hr_module", "mod_id", "MOD"),
							array("hs_hr_nationality", "nat_code", "NAT"),
							array("ohrm_project", "project_id", null),
							array("ohrm_project_activity", "activity_id", null),
							array("hs_hr_skill", "skill_code", "SKI"),
							array("hs_hr_time_event", "time_event_id", null),
							array("hs_hr_timesheet", "timesheet_id", null),
							array("ohrm_pay_grade", "sal_grd_code", "SAL"),
							array("hs_hr_users", "id", null),
							array("hs_hr_user_group", "userg_id", null),
							array("hs_hr_workshift", "workshift_id", null),
							array("hs_hr_custom_export", "export_id", null),
							array("hs_hr_custom_import", "import_id", null),
							array("hs_hr_empreport", "rep_code", "REP"),
							array("hs_hr_workshift", "workshift_id", null),
							array("hs_hr_job_spec", "jobspec_id", null),
							array("hs_hr_job_vacancy", "vacancy_id", null),
							array("hs_hr_job_application", "application_id", null),
                            array("hs_hr_job_application_events", "id", null));

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("UniqueIDGeneratorTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {

    	$conf = new Conf();
    	$this->connection = mysql_connect($conf->dbhost.":".$conf->dbport, $conf->dbuser, $conf->dbpass);

    	$this->assertTrue($this->connection !== false);
        $this->assertTrue(mysql_select_db($conf->dbname));

		$result = mysql_query("SELECT `last_id`, `table_name`, `field_name` FROM `hs_hr_unique_id`;");
		while($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$this->oldValues['AUTO_INC_PK_TABLE']['hs_hr_unique_id'][] = $row;
		}
		mysql_free_result($result);

		$tableList = array('hs_hr_language', 'ohrm_project', 'ohrm_customer');
		$this->_backupTables($tableList);
		

        $this->assertTrue(mysql_query("TRUNCATE TABLE `hs_hr_unique_id`"));
        $this->assertTrue(mysql_query("TRUNCATE TABLE `hs_hr_language`"));
        $this->assertTrue(mysql_query("TRUNCATE TABLE `ohrm_project`"));
        $this->assertTrue(mysql_query("TRUNCATE TABLE `ohrm_customer`"));
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
        $this->assertTrue(mysql_query("TRUNCATE TABLE `hs_hr_unique_id`"));
        $this->assertTrue(mysql_query("TRUNCATE TABLE `hs_hr_language`"));
        $this->assertTrue(mysql_query("TRUNCATE TABLE `ohrm_project`"));
        $this->assertTrue(mysql_query("TRUNCATE TABLE `ohrm_customer`"));

		foreach($this->oldValues['AUTO_INC_PK_TABLE']['hs_hr_unique_id'] as $row) {
			$this->assertTrue(mysql_query("INSERT INTO `hs_hr_unique_id` VALUES (NULL, '" . implode("', '", $row) . "')"), mysql_error());
		}

		$this->_restoreTables();

		/* Restore the unique id table */
        UniqueIDGenerator::getInstance()->initTable();
    }

    /**
     * Test the getInstance() method.
     */
    public function testGetInstance() {

    	$idGen1 = UniqueIDGenerator::getInstance();
    	$this->assertTrue($idGen1 instanceof UniqueIDGenerator);

    	$idGen2 = UniqueIDGenerator::getInstance();
    	$this->assertTrue($idGen1 === $idGen2, "getInstance() should return same object each time it is called");

    }

    /**
     * Test the getNextID() method
     */
    public function testGetNextID() {

		$table1 = "hs_hr_test_table";
		$col1 = "id_col";
		$table2 = "hs_hr_test2_table";
		$col2 = "id";

    	// Initialize the id table.
    	$this->assertTrue(mysql_query('INSERT INTO `hs_hr_unique_id`(table_name, field_name, last_id) VALUES("hs_hr_test_table", "id_col", 0)'));
    	$this->assertTrue(mysql_query('INSERT INTO `hs_hr_unique_id`(table_name, field_name, last_id) VALUES("hs_hr_test2_table", "id", 1002)'));

		$idGen = UniqueIDGenerator::getInstance();

		// invalid table, invalid column
		try {
			$idGen->getNextID("invalid_table", "invalid_column");
		} catch (IDGeneratorException $e) {
			// expected
		}

		// valid table, invalid column
		try {
			$idGen->getNextID($table1, "inv_col");
		} catch (IDGeneratorException $e) {
			// expected
		}

		// invalid table, valid column
		try {
			$idGen->getNextID($table1, $col2);
		} catch (IDGeneratorException $e) {
			// expected
		}

		$this->assertEquals(1, $idGen->getNextId($table1, $col1));
		$this->assertEquals(1, $this->_getLastId($table1, $col1));

		$this->assertEquals(2, $idGen->getNextId($table1, $col1));
		$this->assertEquals(2, $this->_getLastId($table1, $col1));

		$this->assertEquals("TEMP003", $idGen->getNextId($table1, $col1, "TEMP"));
		$this->assertEquals(3, $this->_getLastId($table1, $col1));

		$this->assertEquals(1003, $idGen->getNextId($table2, $col2));
		$this->assertEquals(1003, $this->_getLastId($table2, $col2));

		$this->assertEquals("XYZ1004", $idGen->getNextId($table2, $col2, "XYZ"));
		$this->assertEquals(1004, $this->_getLastId($table2, $col2));

		// try with different min width
		$this->assertEquals("XYZ01005", $idGen->getNextId($table2, $col2, "XYZ", 5));
		$this->assertEquals(1005, $this->_getLastId($table2, $col2));

		$this->assertEquals("XYZ1006", $idGen->getNextId($table2, $col2, "XYZ", 1));
		$this->assertEquals(1006, $this->_getLastId($table2, $col2));

		// Verify that table names, column names are case insensitive
		try {
		$this->assertEquals("XYZ1007", $idGen->getNextId(strtoupper($table2), strtoupper($col2), "XYZ", 1));
		} catch (IDGeneratorException $e) {
			$this->fail("Should accept table names, columns in any case");
		}
		$this->assertEquals(1007, $this->_getLastId($table2, $col2));

    }

    /**
     * Test the getLastID() method
     */
    public function testGetLastID() {

		$table1 = "hs_hr_test_table";
		$col1 = "id_col";
		$table2 = "hs_hr_test2_table";
		$col2 = "id";

    	// Initialize the id table.
    	$this->assertTrue(mysql_query('INSERT INTO `hs_hr_unique_id`(table_name, field_name, last_id) VALUES("hs_hr_test_table", "id_col", 0)'));
    	$this->assertTrue(mysql_query('INSERT INTO `hs_hr_unique_id`(table_name, field_name, last_id) VALUES("hs_hr_test2_table", "id", 1002)'));

		$idGen = UniqueIDGenerator::getInstance();

		// invalid table, invalid column
		try {
			$idGen->getLastID("invalid_table", "invalid_column");
		} catch (IDGeneratorException $e) {
			// expected
		}

		// valid table, invalid column
		try {
			$idGen->getLastID($table1, "inv_col");
		} catch (IDGeneratorException $e) {
			// expected
		}

		// invalid table, valid column
		try {
			$idGen->getLastID($table1, $col2);
		} catch (IDGeneratorException $e) {
			// expected
		}

		$this->assertEquals(0, $idGen->getLastID($table1, $col1));
		$this->assertEquals(0, $this->_getLastId($table1, $col1));

    	$this->assertTrue(mysql_query('UPDATE `hs_hr_unique_id` SET last_id = 3 WHERE table_name = "hs_hr_test_table" AND field_name = "id_col"'));
		$this->assertEquals("TEMP003", $idGen->getLastID($table1, $col1, "TEMP"));
		$this->assertEquals(3, $this->_getLastId($table1, $col1));

		$this->assertEquals(1002, $idGen->getLastID($table2, $col2));
		$this->assertEquals(1002, $this->_getLastId($table2, $col2));

		$this->assertEquals("XYZ1002", $idGen->getLastID($table2, $col2, "XYZ"));
		$this->assertEquals(1002, $this->_getLastId($table2, $col2));

		// try with different min width
		$this->assertEquals("XYZ01002", $idGen->getLastID($table2, $col2, "XYZ", 5));
		$this->assertEquals(1002, $this->_getLastId($table2, $col2));

		$this->assertEquals("XYZ1002", $idGen->getLastID($table2, $col2, "XYZ", 1));
		$this->assertEquals(1002, $this->_getLastId($table2, $col2));

		// Verify that table names, column names are case insensitive
		try {
		$this->assertEquals("XYZ1002", $idGen->getLastID(strtoupper($table2), strtoupper($col2), "XYZ", 1));
		} catch (IDGeneratorException $e) {
			$this->fail("Should accept table names, columns in any case");
		}
		$this->assertEquals(1002, $this->_getLastId($table2, $col2));

    }

	/**
	 * Test that all tables that need unique ID's have entries created in hs_hr_unique_id
	 * after initTable() is run.
	 */
	public function testAllTablesHaveEntries() {

		$idGen = UniqueIDGenerator::getInstance();
		$idGen->initTable();

		foreach ($this->tableInfo as $table) {

			$tableName = $table[0];
			$fieldName = $table[1];
			$prefix = $table[2];

			$newId = $idGen->getNextID($tableName, $fieldName, $prefix);
			$msg = "Invalid ID for table=$tableName, field=$fieldName. Got: $newId";

			if (!empty($prefix)) {

				// Check that newId has the correct prefix
				$this->assertTrue(strpos($newId, $prefix) === 0, $msg);
				$newId = str_replace($prefix, "", $newId);
			}

			// Check that newId is a valid integer.
			$this->assertTrue( ((preg_match('/^[0-9]+$/', $newId)) && (intval($newId) >= 0)), $msg);
		}

	}

    /**
     * Test the initTable method
     */
    public function testInitTable() {

		// A table with string ID's
		$langTable = "hs_hr_language";
		$langId = "lang_code";

		// A table with int ID's
		$cusTable = "ohrm_customer";
		$cusId = "customer_id";

		// Try with empty hs_hr_language and hs_hr_nationality tables
		$idGen = UniqueIDGenerator::getInstance();

		$idGen->initTable();
		$this->assertEquals(0, $this->_getLastId($langTable, $langId));
		$this->assertEquals(0, $this->_getLastId($cusTable, $cusId));

		$this->assertTrue(mysql_query('INSERT INTO hs_hr_language(lang_code, lang_name) VALUES("LAN019", "Japanese")'));
		$this->assertTrue(mysql_query('INSERT INTO ohrm_customer(customer_id, name, description, is_deleted) VALUES(29, "Test customer", "desc", 0)'));

		$idGen->initTable($this->connection);
		$this->assertEquals(19, $this->_getLastId($langTable, $langId));
		$this->assertEquals(29, $this->_getLastId($cusTable, $cusId));

		// Second init table doesn't change anything
		$idGen->initTable();
		$this->assertEquals(19, $this->_getLastId($langTable, $langId));
		$this->assertEquals(29, $this->_getLastId($cusTable, $cusId));

		$this->assertTrue(mysql_query('INSERT INTO hs_hr_language(lang_code, lang_name) VALUES("LAN1119", "Japanese")'));
		$idGen->initTable();
		$this->assertEquals(1119, $this->_getLastId($langTable, $langId));

		// Verify that an expception is thrown if an invalid format ID is found
		$this->assertTrue(mysql_query('INSERT INTO hs_hr_language(lang_code, lang_name) VALUES("LAX11", "Japanese")'));
		try {
			$idGen->initTable();
		} catch (IDGeneratorException $e) {
			// expected. Verify last id was not incremented.
			$this->assertEquals(1119, $this->_getLastId($langTable, $langId));
		}

		// Delete some entries and verify that last id is not changed.
		$this->assertTrue(mysql_query('DELETE FROM hs_hr_language WHERE lang_code = "LAX11"'));
		$this->assertEquals(1, mysql_affected_rows());
		$this->assertTrue(mysql_query('DELETE FROM hs_hr_language WHERE lang_code = "LAN1119"'));
		$this->assertEquals(1, mysql_affected_rows());
		$this->assertEquals(1119, $this->_getLastId($langTable, $langId));
		$this->assertTrue(mysql_query('DELETE FROM hs_hr_language'));
		$this->assertEquals(1, mysql_affected_rows());
		$this->assertEquals(1119, $this->_getLastId($langTable, $langId));

		// Init Table and verify that last id is still not changed
		$idGen->initTable();
		$this->assertEquals(1119, $this->_getLastId($langTable, $langId));
    }

    /**
     * Test the resetIDs method
     */
    public function testResetIDs() {

		// A table with string ID's
		$langTable = "hs_hr_language";
		$langId = "lang_code";

		// A table with int ID's
		$cusTable = "ohrm_customer";
		$cusId = "customer_id";

		// Try with empty hs_hr_language and hs_hr_nationality tables
		$idGen = UniqueIDGenerator::getInstance();

		$idGen->resetIDs();
		$this->assertEquals(0, $this->_getLastId($langTable, $langId));
		$this->assertEquals(0, $this->_getLastId($cusTable, $cusId));

		$this->assertTrue(mysql_query('INSERT INTO hs_hr_language(lang_code, lang_name) VALUES("LAN019", "Japanese")'));
		$this->assertTrue(mysql_query('INSERT INTO ohrm_customer(customer_id, name, description, is_deleted) VALUES(29, "Test customer", "desc", 0)'));

		$idGen->resetIDs();
		$this->assertEquals(19, $this->_getLastId($langTable, $langId));
		$this->assertEquals(29, $this->_getLastId($cusTable, $cusId));

		// Second reset table doesn't change anything
		$idGen->resetIDs();
		$this->assertEquals(19, $this->_getLastId($langTable, $langId));
		$this->assertEquals(29, $this->_getLastId($cusTable, $cusId));

		$this->assertTrue(mysql_query('INSERT INTO hs_hr_language(lang_code, lang_name) VALUES("LAN1119", "Japanese")'));
		$idGen->resetIDs();
		$this->assertEquals(1119, $this->_getLastId($langTable, $langId));

		// Verify that deleting entries and calling resetIDs reduces the last ID
		$this->assertTrue(mysql_query('DELETE FROM hs_hr_language WHERE lang_code = "LAN1119"'));
		$this->assertEquals(1, mysql_affected_rows());
		$idGen->resetIDs();
		$this->assertEquals(19, $this->_getLastId($langTable, $langId));

		$this->assertTrue(mysql_query('DELETE FROM hs_hr_language'));
		$idGen->resetIDs();
		$this->assertEquals(0, $this->_getLastId($langTable, $langId));
    }

	/**
	 * Get the last ID for the given table and field by directly querying the table.
	 */
	private function _getLastId($table, $field) {
		return $this->_getSingleFieldValue("hs_hr_unique_id", "last_id", "table_name = \"$table\" AND field_name = \"$field\"");
	}

	/**
	 * Convenience method that gets a single field value with the given conditions
	 *
	 * @param string $table Table name
	 * @param string $field Field name
	 * @param string $where Where clause
	 *
	 * @return mixed value from the database
	 */
    private function _getSingleFieldValue($table, $field, $where) {

    	$sql = "SELECT $field FROM $table WHERE $where";

    	$result = mysql_query($sql);

    	$row = mysql_fetch_assoc($result);
    	$this->assertTrue(is_array($row));
    	$this->assertEquals(1, count($row));
    	$this->assertTrue(isset($row[$field]));

    	return $row[$field];
    }
    
    private function _backupTables($arrTableList) {
    	
    	foreach ($arrTableList as $table) {
	    	$result = mysql_query("SELECT * FROM `$table`");
			while($row = mysql_fetch_array($result, MYSQL_NUM)) {
				$this->oldValues["$table"][] = $row;
			}
			mysql_free_result($result);
    	}
    }
    
    private function _restoreTables() {
    	
    	$arrTableList = array_keys($this->oldValues);
    	
    	foreach ($arrTableList as $table) {
    		if ($table == 'AUTO_INC_PK_TABLE') {
    			continue;
    		}
    		$this->assertTrue(mysql_query("INSERT INTO `$table` VALUES ('" . implode("', '", $this->oldValues["$table"]) . "')"), mysql_error());
    	}
    }
}

// Call UniqueIDGeneratorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "UniqueIDGeneratorTest::main") {
    UniqueIDGeneratorTest::main();
}
?>
