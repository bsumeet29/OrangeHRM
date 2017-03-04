<?php

/**
 * Test class for OperationalCountryDao.
 * Generated by PHPUnit on 2012-01-12 at 12:47:49.
 */
class OperationalCountryDaoTest extends PHPUnit_Framework_TestCase {

    /**
     * @var OperationalCountryDao
     */
    protected $dao;
    protected $fixture;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->dao = new OperationalCountryDao;
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/OperationalCountryDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers OperationalCountryDao::getOperationalCountryList
     */
    public function testGetOperationalCountryList_Successful() {
        $result = $this->dao->getOperationalCountryList();
        
        $this->assertTrue($result instanceof Doctrine_Collection);
        $this->assertEquals(4, $result->count());
        
        $sampleData = sfYaml::load($this->fixture);
        $sampleOperationalCountries = $sampleData['OperationalCountry'];
        $sampleCountries = $sampleData['Country'];

        foreach ($result as $i => $operationalCountry) {
            $index = ((int) $operationalCountry->getId()) - 1;

            $this->assertTrue($operationalCountry instanceof OperationalCountry);
            $this->assertEquals($sampleOperationalCountries[$index]['id'], $operationalCountry->getId());
            $this->assertEquals($sampleCountries[$index]['cou_name'], $operationalCountry->getName());
            $this->assertEquals($sampleOperationalCountries[$index]['country_code'], $operationalCountry->getCountryCode());
        }
    }
    
    /**
     * @covers OperationalCountryDao::getLocationsMappedToOperationalCountry
     */
    public function testGetLocationsMappedToOperationalCountry_Successful() {
        $sampleData = sfYaml::load($this->fixture);
        $sampleData = $sampleData['Location'];
        
        $result = $this->dao->getLocationsMappedToOperationalCountry('LK');
        
        $this->assertTrue($result instanceof Doctrine_Collection);
        $this->assertEquals(2, $result->count());
        
        $sampleDataIndices = array(0, 1);
        foreach ($result as $i => $location) {
            $index = $sampleDataIndices[$i];
            $this->assertTrue($location instanceof Location);
            $this->assertEquals($sampleData[$index]['id'], $location->getId());
            $this->assertEquals($sampleData[$index]['name'], $location->getName());
        }
        
        $result = $this->dao->getLocationsMappedToOperationalCountry('US');
        $this->assertTrue($result instanceof Doctrine_Collection);
        $this->assertEquals(1, $result->count());
        
        $sampleDataIndices = array(2);
        foreach ($result as $i => $location) {
            $index = $sampleDataIndices[$i];
            $this->assertTrue($location instanceof Location);
            $this->assertEquals($sampleData[$index]['id'], $location->getId());
            $this->assertEquals($sampleData[$index]['name'], $location->getName());
        }
    }
    
    public function testGetOperationalCountriesForLocations() {
        $sampleData = sfYaml::load($this->fixture);
        $sampleOperationalCountries = $sampleData['OperationalCountry'];
        
        // Empty locations
        $locationIds = array();
        $result = $this->dao->getOperationalCountriesForLocations($locationIds);

        // unavalable location
        $locationIds = array(11);
        $result = $this->dao->getOperationalCountriesForLocations($locationIds);
        $this->assertEquals(0, count($result));

        // location without operational country
        $locationIds = array(4);
        $result = $this->dao->getOperationalCountriesForLocations($locationIds);
        $this->assertEquals(0, count($result));
        
        
        $expected = array($sampleOperationalCountries[0]);
        $locationIds = array(1);
        $result = $this->dao->getOperationalCountriesForLocations($locationIds);
        $this->compareOperationalCountries($expected, $result);
        
        $expected = array($sampleOperationalCountries[0], $sampleOperationalCountries[1]);
        $locationIds = array(1, 2, 3, 4);
        $result = $this->dao->getOperationalCountriesForLocations($locationIds);
        $this->compareOperationalCountries($expected, $result);

    }
    
    protected function compareOperationalCountries($expected, $result) {
        $this->assertEquals(count($expected), count($result));
        
        for ($i = 0; $i < count($result); $i++) {
            $country = $result[$i];
            $expectedCountry = $expected[$i];
            $this->assertEquals($expectedCountry['id'], $country->getId());
            $this->assertEquals($expectedCountry['country_code'], $country->getCountryCode());
        }
    }

}

?>
