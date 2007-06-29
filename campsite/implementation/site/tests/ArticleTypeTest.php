<?php

$_SERVER['DOCUMENT_ROOT'] = '/usr/local/campsite/www/campsite/html';

set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/local/campsite/www/campsite/html');

require_once('PHPUnit/Framework.php');
require_once('configuration.php');
require_once('db_connect.php');
require_once('classes/ArticleType.php');

class ArticleTypeTest extends PHPUnit_Framework_TestCase {

	protected $articleType;

	protected $testTypeName = 'test_article_type';

	protected $testLanguageId = 88888888;

	public function ArticleTypeTest()
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

	protected function setUp()
	{
		// initialize the test object
		$this->articleType =& new ArticleType($this->testTypeName);
	}

	public function test_IsValidFieldName()
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

	public function test_create()
	{
		$this->assertType('ADORecordSet_empty', $this->articleType->create(), 'The test article type can not be created.');
	}

	public function test_exist()
	{
		$this->assertTrue($this->articleType->exists(), 'The test article type does not exist after creation.');
	}

	public function test_getTypeName()
	{
		$this->assertEquals($this->testTypeName, $this->articleType->getTypeName());
	}

	public function test_getTableName()
	{
		$this->assertEquals('X'.$this->testTypeName, $this->articleType->getTableName());
	}

	public function test_setName()
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

	public function test_getDisplayName()
	{
		$this->assertEquals('test_name_language', $this->articleType->getDisplayName($this->testLanguageId));
	}

	public function test_translationExists()
	{
		$this->assertNotEquals(0, $this->articleType->translationExists($this->testLanguageId));
	}

	public function test_getTranslations()
	{
		$this->assertEquals(array($this->testLanguageId =>'test_name_language'), $this->articleType->getTranslations());
	}

	public function test_getPhraseId()
	{
		global $g_ado_db;

		$query = "SELECT fk_phrase_id FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$this->assertEquals($g_ado_db->GetOne($query), $this->articleType->getPhraseId());
	}

	public function test_getMetadata()
	{
		global $g_ado_db;

		$query = "SELECT * FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$row = $g_ado_db->GetRow($query);
		$this->assertEquals($row, $this->articleType->getMetadata());
	}

	public function test_unsetName()
	{
		$this->articleType->setName($this->testLanguageId, NULL);
		$this->assertEquals(0, $this->articleType->translationExists($this->testLanguageId));
	}

	public function test_setCommentsEnabled()
	{
		global $g_ado_db;

		$this->articleType->setCommentsEnabled(true);
		$query = "SELECT comments_enabled FROM ArticleTypeMetadata WHERE type_name = '"
				. $this->articleType->getTypeName() . "' AND field_name = 'NULL'";
		$this->assertEquals(1, $g_ado_db->GetOne($query));
		$this->assertEquals(1, $this->articleType->commentsEnabled());
	}

	public function test_commentsEnabled()
	{
		$this->articleType->setCommentsEnabled(false);
		$this->assertEquals(0, $this->articleType->commentsEnabled());
		$this->articleType->setCommentsEnabled(true);
		$this->assertEquals(1, $this->articleType->commentsEnabled());
	}

	public function test_setStatus()
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

	public function test_getStatus()
	{
		$this->articleType->setStatus('hide');
		$this->assertEquals('hidden', $this->articleType->getStatus());
		$this->articleType->setStatus('show');
		$this->assertEquals('shown', $this->articleType->getStatus());
	}

	public function test_getArticlesArray()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function test_GetArticleTypes()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function test_getDisplayNameLanguageCode()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function test_getNumArticles()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function test_getPreviewArticleData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function test_getUserDefinedColumns()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function test_merge()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function test_rename()
	{
		global $g_ado_db;

		$this->articleType->rename($this->testTypeName.'_second');
		$count = $g_ado_db->GetOne("SELECT COUNT(*) FROM ArticleTypeMetadata WHERE type_name = '".$this->testTypeName."_second'");
		$this->assertNotEquals(0, $count);
		$tableName = $g_ado_db->GetOne("SHOW TABLES LIKE '%X".$this->testTypeName."_second%'");
		$this->assertEquals('X'.$this->testTypeName.'_second', $tableName);

		$this->articleType->rename($this->testTypeName);
	}

	public function test_delete()
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

?>
