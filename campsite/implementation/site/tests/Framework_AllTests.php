<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Framework_AllTests::main');
}

require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');

require_once('AliasTest.php');
require_once('ArticleTypeTest.php');
require_once('MetaLanguageTest.php');
require_once('CampContextTest.php');
require_once('ListObjectTest.php');
require_once('ArticleListTest.php');
require_once('CampDatabaseTest.php');
require_once('CampConfigTest.php');


class Framework_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('DBClasses Framework');

        $suite->addTestSuite('ArticleTypeTest');
        $suite->addTestSuite('AliasTest');

        $suite->addTestSuite('MetaLanguageTest');
        $suite->addTestSuite('CampContextTest');

        $suite->addTestSuite('ListObjectTest');
        $suite->addTestSuite('ArticleListTest');

        $suite->addTestSuite('CampDatabaseTest');
        $suite->addTestSuite('CampConfigTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Framework_AllTests::main') {
    Framework_AllTests::main();
}
?>
