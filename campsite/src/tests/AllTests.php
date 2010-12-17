<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');

require_once('Framework_AllTests.php');

require_once('set_path.php');
require_once('bootstrap.php');

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('DBClasses');

        $suite->addTest(Framework_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
?>
