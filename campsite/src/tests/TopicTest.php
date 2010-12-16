<?php
// Call TopicTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "TopicTest::main");
}

require_once('PHPUnit/Framework/TestCase.php');
require_once('PHPUnit/Framework/TestSuite.php');

require_once('set_path.php');
require_once('db_connect.php');
require_once('classes/Topic.php');


/**
 * Test class for Topic.
 */
class TopicTest extends PHPUnit_Framework_TestCase
{
	/**
	 * The test language identifier.
	 *
	 * @var int
	 */
	protected $testLanguageId = 1;


    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once('PHPUnit/TextUI/TestRunner.php');

        $suite  = new PHPUnit_Framework_TestSuite("TopicTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    } // fn main


    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
	{
		global $g_ado_db;

		$tmpTopicTable = "CREATE TABLE TmpTopics (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    node_left int(10) unsigned NOT NULL,
    node_right int(10) unsigned NOT NULL,
    PRIMARY KEY (id),
    INDEX(node_left),
    INDEX(node_right)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$g_ado_db->Execute($tmpTopicTable);
		$g_ado_db->Execute('INSERT INTO TmpTopics SELECT * FROM Topics');

		$tmpTopicNamesTable = "CREATE TABLE TmpTopicNames (
    fk_topic_id int(10) unsigned NOT NULL,
    fk_language_id int(10) unsigned NOT NULL,
    name varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (fk_topic_id, fk_language_id),
    UNIQUE KEY (fk_language_id, name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$g_ado_db->Execute($tmpTopicNamesTable);
		$g_ado_db->Execute('INSERT INTO TmpTopicNames SELECT * FROM TopicNames');

		$g_ado_db->Execute('DELETE FROM Topics');
		$g_ado_db->Execute('DELETE FROM TopicNames');
	} // fn setUp


    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
		global $g_ado_db;

		$g_ado_db->Execute('DELETE FROM Topics');
		$g_ado_db->Execute('DELETE FROM TopicNames');
		$g_ado_db->Execute('INSERT INTO Topics SELECT * FROM TmpTopics');
		$g_ado_db->Execute('INSERT INTO TopicNames SELECT * FROM TmpTopicNames');
    } // fn tearDown


	public function testCreate()
	{
		$topic1 = new Topic(27);
		$topic1->create(array('names'=>array(1=>'Health', 2=>'Sănătate')));
		unset($topic1);
		$topic1 = new Topic(27);
		$this->assertTrue($topic1->exists());
		$this->assertEquals($topic1->getName(1), 'Health');
	} // fn testCreate


	public function testExist()
	{
		$this->assertTrue($this->articleType->exists(), 'The test article type does not exist after creation.');
	} // fn testExists


	public function testGetTypeName()
	{
		$this->assertEquals($this->testTypeName, $this->articleType->getTypeName());
	}


	public function testSetName()
	{
		global $g_ado_db;

		$this->articleType->setName($this->testLanguageId, 'test_name_language');
		$query = "SELECT t.translation_text "
				. "FROM ArticleTypeMetadata atm, Translations t "
				. "WHERE atm.type_name= '" . $this->articleType->getTypeName() . "' "
				. "AND atm.field_name = 'NULL' AND atm.fk_phrase_id = t.phrase_id "
				. "AND t.fk_language_id = '" . $this->testLanguageId . "'";
		$this->assertEquals('test_name_language', $g_ado_db->GetOne($query));
		$this->assertNotEquals(-1, $this->articleType->getPhraseId());
		$this->assertType('ADORecordSet_empty', $this->articleType->create(), 'The test article type can not be created.');
	}


	public function testGetDisplayName()
	{
		$this->articleType->setName($this->testLanguageId, 'test_name_language');
		$this->assertEquals('test_name_language', $this->articleType->getDisplayName($this->testLanguageId));
	}


	public function testGetNames()
	{
		$this->articleType->setName($this->testLanguageId, 'test_name_language');
		$this->assertEquals(array($this->testLanguageId =>'test_name_language'), $this->articleType->getTranslations());
	}


	public function testGetMetadata()
	{
		global $g_ado_db;

		$query = "SELECT * FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$row = $g_ado_db->GetRow($query);
		$this->assertEquals($row, $this->articleType->getMetadata());
	}


	public function testUnsetName()
	{
		$this->articleType->setName($this->testLanguageId, NULL);
		$this->assertEquals(0, $this->articleType->translationExists($this->testLanguageId));
	}


	public function testGetArticlesArray()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


	public function testMerge()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


	public function testRename()
	{
		global $g_ado_db;

		$this->articleType->rename($this->testTypeName.'_second');
		$count = $g_ado_db->GetOne("SELECT COUNT(*) FROM ArticleTypeMetadata WHERE type_name = '".$this->testTypeName."_second'");
		$this->assertNotEquals(0, $count);
		$tableName = $g_ado_db->GetOne("SHOW TABLES LIKE '%X".$this->testTypeName."_second%'");
		$this->assertEquals('X'.$this->testTypeName.'_second', $tableName);

		$this->articleType->rename($this->testTypeName);
	}


	public function testDelete()
	{
		global $g_ado_db;

		$this->articleType->delete();
		$this->assertFalse($this->articleType->exists());
		$tableName = $g_ado_db->GetOne("SHOW TABLES LIKE '%X".$this->testTypeName."'");
		$this->assertEquals('', $tableName);
		$count = $g_ado_db->GetOne("SELECT COUNT(*) FROM ArticleTypeMetadata WHERE type_name = '".$this->testTypeName."'");
		$this->assertEquals(0, $count);
	} // fn testDelete
}

// Call TopicTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "TopicTest::main") {
    TopicTest::main();
}
?>