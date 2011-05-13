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

		$g_ado_db->Execute('DROP TABLE IF EXISTS TmpTopics');
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

		$g_ado_db->Execute('DROP TABLE IF EXISTS TmpTopicNames');
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
		// test create(), fetch(), getLeft(), getRight(), getName()
		$this->createAndTest(1, array('names'=>array(1=>'Sports', 2=>'Sport')), 1, 2);
		$this->createAndTest(3, array('names'=>array(1=>'Electronics', 2=>'Electronice')), 1, 2);
		$this->createAndTest(2, array('names'=>array(1=>'Education', 2=>'Educație')), 1, 2);
		$this->createAndTest(27, array('names'=>array(1=>'Health', 2=>'Sănătate')), 1, 2);

		$this->createAndTest(4, array('parent_id'=>3, 'names'=>array(1=>'Televisions', 2=>'Televizoare')), 6, 7);
		$this->createAndTest(9, array('parent_id'=>3, 'names'=>array(1=>'Portable Electronics', 2=>'Electronice portabile')), 6, 7);

		$this->createAndTest(14, array('parent_id'=>2, 'names'=>array(1=>'Culture', 2=>'Cultură')), 4, 5);
		$this->createAndTest(15, array('parent_id'=>2, 'names'=>array(1=>'Science', 2=>'Știință')), 4, 5);
		$this->createAndTest(26, array('parent_id'=>2, 'names'=>array(1=>'Religion', 2=>'Religie')), 4, 5);

		$this->createAndTest(16, array('parent_id'=>14, 'names'=>array(1=>'Music', 2=>'Muzică')), 9, 10);
		$this->createAndTest(19, array('parent_id'=>14, 'names'=>array(1=>'Film', 2=>'Film')), 9, 10);
		$this->createAndTest(22, array('parent_id'=>14, 'names'=>array(1=>'Books', 2=>'Cărți')), 9, 10);

		$this->createAndTest(17, array('parent_id'=>16, 'names'=>array(1=>'Classical', 2=>'Clasică')), 14, 15);
		$this->createAndTest(18, array('parent_id'=>16, 'names'=>array(1=>'Jazz', 2=>'Jazz')), 14, 15);

		$this->createAndTest(24, array('parent_id'=>15, 'names'=>array(1=>'Physics', 2=>'Fizică')), 7, 8);
		$this->createAndTest(25, array('parent_id'=>15, 'names'=>array(1=>'Mathematics', 2=>'Matematică')), 7, 8);

		// test constructor and GetByFullName()
		$topic = new Topic('Physics:en');

		// test other get methods
		$this->assertEquals(24, $topic->getTopicId());

		$this->assertEquals(15, $topic->getParentId());

		$this->assertEquals(2, $topic->getNumTranslations());

		$translations = array(1=>new TopicName(24, 1), 2=>new TopicName(24, 2));
		$this->assertEquals($translations, $topic->getTranslations());

		$path = array(2=>new Topic(2), 15=>new Topic(15), 24=>new Topic(24));
		$pathIds = array(2=>2, 15=>15, 24=>24);
		$this->assertEquals($path, $topic->getPath());
		$this->assertEquals($pathIds, $topic->getPath(true));

		$this->assertFalse($topic->hasSubtopics());

		$this->assertFalse($topic->isRoot());

		$this->assertEquals(1, $topic->getWidth());

		$this->assertEquals(array(), $topic->getSubtopics());

		$topic = new Topic('Educație:ro');

		$this->assertTrue($topic->isRoot());

		$this->assertTrue($topic->hasSubtopics());

		$this->assertEquals(21, $topic->getWidth());

		$this->assertEquals(null, $topic->getParentId());

		$this->assertEquals(array(2=>new Topic(2)), $topic->getPath());

		$subtopicsDepth1 = array(new Topic(26), new Topic(15), new Topic(14));
		$subtopicsDepth1Ids = array(26, 15, 14);
		$this->assertEquals($subtopicsDepth1, $topic->getSubtopics());
		$this->assertEquals($subtopicsDepth1Ids, $topic->getSubtopics(true));
		$subtopicsDepth2 = array(new Topic(26), new Topic(15), new Topic(25), new Topic(24),
		new Topic(14), new Topic(22), new Topic(19), new Topic(16));
		$this->assertEquals($subtopicsDepth2, $topic->getSubtopics(false, 2));
		$subtopicsAll = array(new Topic(26), new Topic(15), new Topic(25), new Topic(24),
		new Topic(14), new Topic(22), new Topic(19), new Topic(16), new Topic(18), new Topic(17));
		$this->assertEquals($subtopicsAll, $topic->getSubtopics(false, 0));

		$topics = array(new Topic(2));
		$this->assertEquals($topics, Topic::GetTopics(2));
		$this->assertEquals($topics, Topic::GetTopics(null, 1, 'Education'));
		$this->assertEquals($subtopicsDepth1, Topic::GetTopics(null, null, null, 2));
		$this->assertEquals($subtopicsAll, Topic::GetTopics(null, null, null, 2, 0));
		$subtopicsDepth1Name = array(new Topic(14), new Topic(26), new Topic(15));
		$this->assertEquals($subtopicsDepth1Name, Topic::GetTopics(null, 1, null, 2, 1,
		null, array(array('field'=>'byname', 'dir'=>'asc'))));

		$tree = array(
		array(27=>new Topic(27)),
		array(2=>new Topic(2)),
		array(2=>new Topic(2), 26=>new Topic(26)),
		array(2=>new Topic(2), 15=>new Topic(15)),
		array(2=>new Topic(2), 15=>new Topic(15), 25=>new Topic(25)),
		array(2=>new Topic(2), 15=>new Topic(15), 24=>new Topic(24)),
		array(2=>new Topic(2), 14=>new Topic(14)),
		array(2=>new Topic(2), 14=>new Topic(14), 22=>new Topic(22)),
		array(2=>new Topic(2), 14=>new Topic(14), 19=>new Topic(19)),
		array(2=>new Topic(2), 14=>new Topic(14), 16=>new Topic(16)),
		array(2=>new Topic(2), 14=>new Topic(14), 16=>new Topic(16), 18=>new Topic(18)),
		array(2=>new Topic(2), 14=>new Topic(14), 16=>new Topic(16), 17=>new Topic(17)),
		array(3=>new Topic(3)),
		array(3=>new Topic(3), 9=>new Topic(9)),
		array(3=>new Topic(3), 4=>new Topic(4)),
		array(1=>new Topic(1))
		);
		$this->assertEquals($tree, Topic::GetTree());
		$subtree = array(
		array(26=>new Topic(26)),
		array(15=>new Topic(15)),
		array(15=>new Topic(15), 25=>new Topic(25)),
		array(15=>new Topic(15), 24=>new Topic(24)),
		array(14=>new Topic(14)),
		array(14=>new Topic(14), 22=>new Topic(22)),
		array(14=>new Topic(14), 19=>new Topic(19)),
		array(14=>new Topic(14), 16=>new Topic(16)),
		array(14=>new Topic(14), 16=>new Topic(16), 18=>new Topic(18)),
		array(14=>new Topic(14), 16=>new Topic(16), 17=>new Topic(17))
		);
		$this->assertEquals($subtree, Topic::GetTree(2));
		$subtree = array(
		array(22=>new Topic(22)),
		array(19=>new Topic(19)),
		array(16=>new Topic(16)),
		array(16=>new Topic(16), 18=>new Topic(18)),
		array(16=>new Topic(16), 17=>new Topic(17))
		);
		$this->assertEquals($subtree, Topic::GetTree(14));

		Topic::UpdateOrder(array('topic_2'=>array('topic_26', 'topic_14', 'topic_15')));
		$topic = new Topic(14);
		$this->assertEquals(6, $topic->getLeft());
		$this->assertEquals(17, $topic->getRight());
		$topic = new Topic(15);
		$this->assertEquals(18, $topic->getLeft());
		$this->assertEquals(23, $topic->getRight());
		$topic = new Topic(16);
		$this->assertEquals(11, $topic->getLeft());
		$this->assertEquals(16, $topic->getRight());
		Topic::UpdateOrder(array('topic_0'=>array('topic_27', 'topic_3', 'topic_2', 'topic_1')));
		$topic = new Topic(3);
		$this->assertEquals(3, $topic->getLeft());
		$this->assertEquals(8, $topic->getRight());
		$topic = new Topic(2);
		$this->assertEquals(9, $topic->getLeft());
		$this->assertEquals(30, $topic->getRight());
		$topic = new Topic(16);
		$this->assertEquals(17, $topic->getLeft());
		$this->assertEquals(22, $topic->getRight());

		// test setName()
		$topic->setName(1, 'My Music');
		$topic = new Topic(16);
		$this->assertEquals('My Music', $topic->getName(1));

		// test delete()
		$topic->delete(2);
		$this->assertEquals('My Music', $topic->getName(1));
		$this->assertEquals(1, $topic->getNumTranslations());
		$topic->delete();
		$topic = new Topic(15);
		$this->assertEquals(18, $topic->getLeft());
		$this->assertEquals(23, $topic->getRight());
		$topic = new Topic(1);
		$this->assertEquals(25, $topic->getLeft());
		$this->assertEquals(26, $topic->getRight());
		$topic = new Topic(14);
		$subtopics = array(22, 19);
		$this->assertEquals($subtopics, $topic->getSubtopics(true));
	} // fn testCreate


	private function createAndTest($p_topicId, $p_data, $p_left, $p_right)
	{
		$topic = new Topic($p_topicId);
		$topic->create($p_data);
		unset($topic);
		$topic = new Topic($p_topicId);
		$this->assertTrue($topic->exists());
		foreach ($p_data['names'] as $languageId=>$name) {
			$this->assertEquals($name, $topic->getName($languageId));
		}
		$this->assertEquals($p_left, $topic->getLeft());
		$this->assertEquals($p_right, $topic->getRight());
	}
}

// Call TopicTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "TopicTest::main") {
    TopicTest::main();
}
?>