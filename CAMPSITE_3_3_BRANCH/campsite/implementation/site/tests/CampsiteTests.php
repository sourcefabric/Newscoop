<?

require_once("PHPUnit.php");
require_once(dirname(dirname(__FILE__))."/classes/Article.php");

class ArticleTest extends PHPUnit_TestCase {
	function ArticleTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	function test_article() {
		$article = new Article(9000001,9000002,9000003,9000004);

		// Test create
		$article->create("Unit Test Long Name",
						 "Unit Test Short Name",
						 "fastnews");
		$this->assertTrue($article->exists());

		// Test SET functions
		$article->setTitle("Unit Test New Title");
		$article->setCreatorId(9000005);
		$article->setOnFrontPage(true);
		$article->setOnSection(true);
		$article->setWorkflowStatus('Y');
		$article->setKeywords("Unit, Test");
		$article->setIsIndexed(true);

		// Test GET functions
		$articleCopy = new Article(9000001, 9000002, 9000003, 9000004, $article->getArticleId());
		$this->assertEquals(9000001, $articleCopy->getPublicationId());
		$this->assertEquals(9000002, $articleCopy->getIssueNumber());
		$this->assertEquals(9000003, $articleCopy->getSectionNumber());
		$this->assertEquals(9000004, $articleCopy->getLanguageId());
		$this->assertEquals(9000005, $articleCopy->getCreatorId());
		$this->assertEquals("Unit Test New Title", $articleCopy->getTitle());
		$this->assertEquals(true, $articleCopy->onFrontPage());
		$this->assertEquals(true, $articleCopy->onSection());
		$this->assertEquals('Y', $articleCopy->getWorkflowStatus());
		$this->assertEquals("Unit, Test", $articleCopy->getKeywords());
		$this->assertEquals(true, $articleCopy->isIndexed());

		// Test DELETE functions
		$article->delete();
		$this->assertFalse($article->exists());
	}
}

$suite = new PHPUnit_TestSuite("ArticleTest");
$result = PHPUnit::run($suite);
echo $result->toHtml();


?>