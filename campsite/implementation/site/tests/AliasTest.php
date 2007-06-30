<?php
// Call AliasTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "AliasTest::main");
}

require_once('PHPUnit/Framework/TestCase.php');
require_once('PHPUnit/Framework/TestSuite.php');

require_once('set_path.php');
require_once('db_connect.php');
require_once('classes/Alias.php');

/**
 * Test class for Alias.
 */
class AliasTest extends PHPUnit_Framework_TestCase {

	/**
	 * Alias object
	 *
	 * @var object
	 */
	protected $alias;

	/**
	 * The name of the test alias.
	 *
	 * @var string
	 */
	protected $testName = 'test_alias.test_alias.com';

	/**
	 * The identifier of the test publication.
	 *
	 * @var int
	 */
	protected $testPublicationId = 88888888;

	/**
	 * Deletes the test data.
	 *
	 * @return void
	 */
	protected function clear()
	{
    	global $g_ado_db;

		$g_ado_db->Execute("DELETE FROM Aliases WHERE Name = '".$this->testName."'");
		$g_ado_db->Execute("DELETE FROM Aliases WHERE Name = '".$this->testName."_second'");
	}

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once('PHPUnit/TextUI/TestRunner.php');

        $suite  = new PHPUnit_Framework_TestSuite("AliasTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
    	global $g_ado_db;

    	$this->clear();

		$query = "INSERT INTO Aliases (Name, IdPublication) VALUES('"
    			. $this->testName . "', " . $this->testPublicationId . ")";
    	$g_ado_db->Execute($query);
    	$this->alias = new Alias($g_ado_db->Insert_ID());
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    	$this->clear();
    }

    /**
     * @todo Implement testCreate().
     */
    public function testCreate()
    {
    	global $g_ado_db;

    	$this->clear();

    	$this->alias = new Alias();
    	$aliasData = array('Name'=>$this->testName,
    						'IdPublication'=>$this->testPublicationId);
    	$this->alias->create($aliasData);
    	$query = "SELECT COUNT(*) FROM Aliases WHERE Name = '" . $this->testName
    			. "' AND IdPublication = " . $this->testPublicationId;
    	$this->assertNotEquals(0, $g_ado_db->GetOne($query));
    	$this->assertNotEquals(0, $this->alias->getId());
    }

    /**
     * @todo Implement testGetId().
     */
    public function testGetId()
    {
    	$this->assertNotEquals(0, $this->alias->getId());
    }

    /**
     * @todo Implement testGetName().
     */
    public function testGetName()
    {
		$this->assertEquals($this->testName, $this->alias->getName());
    }

    /**
     * @todo Implement testSetName().
     */
    public function testSetName()
    {
    	global $g_ado_db;

    	$this->alias->setName($this->testName . '_second');
    	$query = "SELECT Name FROM Aliases WHERE Id = " . $this->alias->getId();
    	$this->assertEquals($this->testName.'_second', $g_ado_db->GetOne($query));
    }

    /**
     * @todo Implement testGetPublicationId().
     */
    public function testGetPublicationId()
    {
    	$this->assertNotEquals(0, $this->alias->getPublicationId());
    }

    /**
     * @todo Implement testSetPublicationId().
     */
    public function testSetPublicationId()
    {
    	$this->alias->setPublicationId($this->testPublicationId + 1);
    	$this->assertEquals($this->testPublicationId + 1, $this->alias->getPublicationId());
    }

    /**
     * @todo Implement testGetAliases().
     */
    public function testGetAliases()
    {
    	$aliases = $this->alias->GetAliases($this->alias->getId());
    	$expectedAliases = array($this->alias);
    	$this->assertEquals($expectedAliases, $aliases);
    }

    /**
     * @todo Implement testDelete().
     */
    public function testDelete()
    {
    	global $g_ado_db;

    	$aliasId = $this->alias->getId();
    	$this->alias->delete();
    	$query = "SELECT COUNT(*) FROM Aliases WHERE Id = " . $aliasId;
    	$this->assertEquals(0, $g_ado_db->GetOne($query));
    	$this->assertEquals(0, $this->alias->getId());
    }
}

// Call AliasTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "AliasTest::main") {
    AliasTest::main();
}
?>
