<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Framework_AllTests::main');
}

require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');

// Database access classes
require_once('AliasTest.php');
require_once('ArticleTypeTest.php');
require_once('TopicTest.php');
// require_once('IssueTest.php');
// require_once('SectionTest.php');
// require_once('ArticleAttachmentTest.php');
// require_once('ArticleImageTest.php');

// Metaclasses (template engine)
require_once('MetaLanguageTest.php');

// Template engine classes
require_once('CampContextTest.php');
require_once('CampDatabaseTest.php');
require_once('CampConfigTest.php');
require_once('OperatorTest.php');
require_once('ListObjectTest.php');
require_once('ArticlesListTest.php');
require_once('CampURITest.php');
require_once('CampURIShortNamesTest.php');
require_once('CampURITemplatePathTest.php');


class Framework_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('DBClasses Framework');

        // Database access classes
//        $suite->addTestSuite('ArticleTypeTest');
//        $suite->addTestSuite('AliasTest');
        $suite->addTestSuite('TopicTest');
        // $suite->addTestSuite('IssueTest');
        // $suite->addTestSuite('SectionTest');
        // $suite->addTestSuite('ArticleAttachmentTest');
        // $suite->addTestSuite('ArticleImageTest');

        // Metaclasses (template engine)
//        $suite->addTestSuite('MetaLanguageTest');

        // Template engine classes
        $suite->addTestSuite('CampDatabaseTest');
        $suite->addTestSuite('CampContextTest');
        $suite->addTestSuite('CampConfigTest');

        $suite->addTestSuite('OperatorTest');

        $suite->addTestSuite('ListObjectTest');
        $suite->addTestSuite('ArticlesListTest');

//        $suite->addTestSuite('CampURITest');
//        $suite->addTestSuite('CampURIShortNamesTest');
//        $suite->addTestSuite('CampURITemplatePathTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Framework_AllTests::main') {
    Framework_AllTests::main();
}
?>
