<?php

$_SERVER['DOCUMENT_ROOT'] = '/usr/local/campsite/www/campsite/html';

set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/local/campsite/www/campsite/html');

require_once('PHPUnit/Framework.php');
require_once('configuration.php');
require_once('db_connect.php');
require_once('classes/ArticleType.php');

class ArticleTypeTest extends PHPUnit_Framework_TestCase {

	protected $articleType;

	protected $testLanguageId = 88888888;

	public function ArticleTypeTest()
	{
		global $g_ado_db;

		$phraseId = $g_ado_db->GetOne("SELECT fk_phrase_id FROM ArticleTypeMetadata WHERE type_name = 'test_article_type'");
		if ($phraseId > 0) {
			$g_ado_db->Execute('DELETE FROM Translations WHERE phrase_id = ' . $phraseId);
		}
		$g_ado_db->Execute('DROP TABLE Xtest_article_type');
		$g_ado_db->Execute("DELETE FROM ArticleTypeMetadata WHERE type_name = 'test_article_type'");
	}

	protected function setUp()
	{
		// initialize the test object
		$this->articleType =& new ArticleType('test_article_type');
	}

	public function testArticleTypeCreate()
	{
		$this->assertType('ADORecordSet_empty', $this->articleType->create(), 'The test article type can not be created.');
	}

	public function testArticleTypeExist()
	{
		$this->assertTrue($this->articleType->exists(), 'The test article type does not exist after creation.');
	}

	public function testSetName()
	{
		global $g_ado_db;

		$this->articleType->setName($this->testLanguageId, 'test_name_language_id_1');
		$query = "SELECT t.translation_text "
				. "FROM ArticleTypeMetadata atm, Translations t "
				. "WHERE atm.type_name= '" . $this->articleType->getTypeName() . "' "
				. "AND atm.field_name = 'NULL' AND atm.fk_phrase_id = t.phrase_id "
				. "AND t.fk_language_id = '" . $this->testLanguageId . "'";
		$this->assertEquals('test_name_language_id_1', $g_ado_db->GetOne($query));
	}

	public function testTranslationExists()
	{
		$this->assertNotEquals(0, $this->articleType->translationExists($this->testLanguageId));
	}

	public function testUnsetName()
	{
		$this->articleType->setName($this->testLanguageId, NULL);
		$this->assertEquals(0, $this->articleType->translationExists($this->testLanguageId));
	}

	public function testDelete()
	{
		$this->articleType->delete();
		$this->assertFalse($this->articleType->exists());
	}
}

?>
