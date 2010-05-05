<?php

restore_error_handler();

if (! defined('SIMPLE_TEST')) {
    define ('SIMPLE_TEST', 'simpletest/');
}

// todo: make this a relative path
if (! defined('CAMP_TESTS')) {
    define ('CAMP_TESTS', $Campsite['HTML_DIR'] . "/$ADMIN_DIR/tests/bugreporter/");
}

if (! defined('CAMP_CLASSES')) {
    define ('CAMP_CLASSES', $GLOBALS['g_campsiteDir'] . "/classes/");
}

require_once (SIMPLE_TEST . 'unit_tester.php');
require_once (SIMPLE_TEST . 'reporter.php');

$test = new GroupTest('All Tests');
$test->addTestFile (CAMP_TESTS . 'bugreporter_test.php');
$test->addTestFile (CAMP_TESTS . 'autotrac_test.php');
$test->run(new HtmlReporter());
?>
