<?php
// Call ArticleTypeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ArticleTypeTest::main");
}

require_once('PHPUnit/Framework/TestCase.php');
require_once('PHPUnit/Framework/TestSuite.php');

require_once('set_path.php');
require_once('db_connect.php');
require_once('classes/ArticleType.php');

/**
 * Test class for ArticleType.
 */
class ArticleTypeTest extends PHPUnit_Framework_TestCase
{

	/**
	 * ArticleType object
	 *
	 * @var object
	 */
	protected $articleType;

	/**
	 * The name of the test article type.
	 *
	 * @var string
	 */
	protected $testTypeName = 'test_article_type';

	/**
	 * The test language identifier.
	 *
	 * @var int
	 */
	protected $testLanguageId = 88888888;


	/**
	 * Deletes the test data.
	 *
	 * @return void
	 */
	protected function clear()
	{
		global $g_ado_db;

		// make sure to clean the database in case the test type already existed
		$phraseId = $g_ado_db->GetOne("SELECT fk_phrase_id FROM ArticleTypeMetadata WHERE type_name = '".$this->testTypeName."'");
		if ($phraseId > 0) {
			$g_ado_db->Execute('DELETE FROM Translations WHERE phrase_id = ' . $phraseId);
		}
		$g_ado_db->Execute('DROP TABLE X'.$this->testTypeName);
		$g_ado_db->Execute("DELETE FROM ArticleTypeMetadata WHERE type_name = '".$this->testTypeName."'");

		$phraseId = $g_ado_db->GetOne("SELECT fk_phrase_id FROM ArticleTypeMetadata WHERE type_name = '".$this->testTypeName."_second'");
		if ($phraseId > 0) {
			$g_ado_db->Execute('DELETE FROM Translations WHERE phrase_id = ' . $phraseId);
		}
		$g_ado_db->Execute('DROP TABLE X'.$this->testTypeName.'_second');
		$g_ado_db->Execute("DELETE FROM ArticleTypeMetadata WHERE type_name = '".$this->testTypeName."_second'");
	}

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once('PHPUnit/TextUI/TestRunner.php');

        $suite  = new PHPUnit_Framework_TestSuite("ArticleTypeTest");
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

		// create the article type
		$queryStr = "CREATE TABLE `X".$this->testTypeName."`"
					."(NrArticle INT UNSIGNED NOT NULL, "
					." IdLanguage INT UNSIGNED NOT NULL, "
					." PRIMARY KEY(NrArticle, IdLanguage))";
		$g_ado_db->Execute($queryStr);
		$queryStr = "INSERT INTO ArticleTypeMetadata"
					."(type_name, field_name) "
					."VALUES ('".$this->testTypeName."', 'NULL')";
		$g_ado_db->Execute($queryStr);

		// initialize the test object
		$this->articleType = new ArticleType($this->testTypeName);
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

    public function testIsValidFieldName()
	{
		$this->assertTrue(ArticleType::IsValidFieldName('_a'));
		$this->assertTrue(ArticleType::IsValidFieldName('az'));
		$this->assertTrue(ArticleType::IsValidFieldName('a_'));
		$this->assertFalse(ArticleType::IsValidFieldName(''));
		$this->assertFalse(ArticleType::IsValidFieldName('_'));
		$this->assertFalse(ArticleType::IsValidFieldName('2'));
		$this->assertFalse(ArticleType::IsValidFieldName('_2'));
		$this->assertFalse(ArticleType::IsValidFieldName('2_'));
		$this->assertFalse(ArticleType::IsValidFieldName('a2'));
		$this->assertFalse(ArticleType::IsValidFieldName('a '));
	}

	public function testCreate()
	{
		$this->clear();
		$this->assertType('ADORecordSet_empty', $this->articleType->create(), 'The test article type can not be created.');
	}

	public function testExist()
	{
		$this->assertTrue($this->articleType->exists(), 'The test article type does not exist after creation.');
	}

	public function testGetTypeName()
	{
		$this->assertEquals($this->testTypeName, $this->articleType->getTypeName());
	}

	public function testGetTableName()
	{
		$this->assertEquals('X'.$this->testTypeName, $this->articleType->getTableName());
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
	}

	public function testGetDisplayName()
	{
		$this->articleType->setName($this->testLanguageId, 'test_name_language');
		$this->assertEquals('test_name_language', $this->articleType->getDisplayName($this->testLanguageId));
	}

	public function testTranslationExists()
	{
		$this->articleType->setName($this->testLanguageId, 'test_name_language');
		$this->assertNotEquals(0, $this->articleType->translationExists($this->testLanguageId));
	}

	public function testGetTranslations()
	{
		$this->articleType->setName($this->testLanguageId, 'test_name_language');
		$this->assertEquals(array($this->testLanguageId =>'test_name_language'), $this->articleType->getTranslations());
	}

	public function testGetPhraseId()
	{
		global $g_ado_db;

		$this->articleType->setName($this->testLanguageId, 'test_name_language');
		$query = "SELECT fk_phrase_id FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$this->assertEquals($g_ado_db->GetOne($query), $this->articleType->getPhraseId());
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

	public function testSetCommentsEnabled()
	{
		global $g_ado_db;

		$this->articleType->setCommentsEnabled(true);
		$query = "SELECT comments_enabled FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$this->assertEquals(1, $g_ado_db->GetOne($query));
		$this->assertEquals(1, $this->articleType->commentsEnabled());
	}

	public function testCommentsEnabled()
	{
		$this->articleType->setCommentsEnabled(false);
		$this->assertEquals(0, $this->articleType->commentsEnabled());
		$this->articleType->setCommentsEnabled(true);
		$this->assertEquals(1, $this->articleType->commentsEnabled());
	}

	public function testSetStatus()
	{
		global $g_ado_db;

		$this->articleType->setStatus('hide');
		$query = "SELECT is_hidden FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$this->assertEquals(1, $g_ado_db->GetOne($query));
		$this->articleType->setStatus('show');
		$query = "SELECT is_hidden FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$this->assertEquals(0, $g_ado_db->GetOne($query));
	}

	public function testGetStatus()
	{
		$this->articleType->setStatus('hide');
		$this->assertEquals('hidden', $this->articleType->getStatus());
		$this->articleType->setStatus('show');
		$this->assertEquals('shown', $this->articleType->getStatus());
	}

	public function testGetArticlesArray()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testGetArticleTypes()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testGetDisplayNameLanguageCode()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testGetNumArticles()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testGetPreviewArticleData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testGetUserDefinedColumns()
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
	}
}

// Call ArticleTypeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ArticleTypeTest::main") {
    ArticleTypeTest::main();
}
?>
