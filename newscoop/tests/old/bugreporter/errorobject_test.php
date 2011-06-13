<?php
require_once ('simpletest/unit_tester.php');
require_once ('simpletest/reporter.php');
require_once ( CAMP_CLASSES . "BugReporter.php");

class TestOfErrorObject extends UnitTestCase
{
    function setUp(){
        $this->vars = new ErrorObjectVars();
    }

    function test_ErrorObject_emptyStringForName()
    {
        new ErrorObject("", "1.5", 2, "", "nonfile.php", 3,
                        "Fri, 17 Mar 2006 09:47:15 -0500", $this->vars->backtrace);
        $this->assertError();
    }

    function test_ErrorObject_NumberForName()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", 1, 3,
                               $this->vars->time, $this->vars->backtrace);
        $this->assertError();
        //$this->assertEqual ("1", $err->getFileWithoutPath());
    }

    function test_ErrorObject_trailingSlashInFileName()

    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "nonfile.php/", 3, $this->vars->time, $this->vars->backtrace);
        $this->assertError();
    }

    function test_ErrorObject_preAndPostSlashInFileName()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/nonfile.php/", 3,
                               $this->vars->time, $this->vars->backtrace);
        $this->assertError();
        //$this->assertEqual ("1", $err->getFileWithoutPath());
    }

    function test_ErrorObject_emptyStringForVersion()
    {
        new ErrorObject("Campsite", "", 2, "", "nonfile.php", 3,
                        $this->vars->time, $this->vars->backtrace);
        $this->assertError();
    }

    function test_ErrorObject_stringForErrorCode()
    {
        $err = new ErrorObject("Campsite", "1.5", "error message", "", "file.php", 3, $this->vars->time,
                               $this->vars->backtrace);
        $this->assertError();
    }

    function test_ErrorObject_floatForErrorCode()
    {
        $err = new ErrorObject("Campsite", "1.5", 1.1, "", "file.php", 3,
                               $this->vars->time, $this->vars->backtrace);
        $this->assertError();
    }

    function test_ErrorObject_software ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3,
                               $this->vars->time, $this->vars->backtrace );
        $this->assertEqual ("Campsite", $err->m_software);
    }

    function test_backtraceParamIsAString()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3,
                               $this->vars->time, $this->vars->backtraceString );

        $this->assertEqual ($err->m_backtrace, $this->vars->backtraceString);
    }

    function test_numbersAreNumStrings()
    {
        $err = new ErrorObject("Campware", "1.5", "2", "", "nonfile.php", "3",
                        "Fri, 17 Mar 2006 09:47:15 -0500", $this->vars->backtrace);
        $this->assertIdentical ($err->m_num, 2);
        $this->assertIdentical ($err->m_line, 3);
    }
}

class TestOfGetFileWithoutPathX extends UnitTestCase
{
    function setUp(){
        $this->vars = new ErrorObjectVars();
    }

    function test_getFileWithoutPath()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/non-dir/nonfile.php", 3,
                               $this->vars->time, $this->vars->backtrace );
        $errId = $err->getFileWithoutPath();
        $this->assertEqual ($errId, "nonfile.php");
    }

    function test_getFileWithoutPath_noPath ()

    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "nonfile.php",
                               3, $this->vars->time, $this->vars->backtrace );
        $errId = $err->getFileWithoutPath();
        $this->assertEqual($errId, "nonfile.php");
    }

    function test_getFileWithoutPath_zeroValueVars ()

    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/non-dir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $err = camp_change_error_object_vars ($err, 0);
        $errFile = $err->getFileWithoutPath();
        $this->assertIdentical ("0", $errFile);
    }

    function test_getFileWithoutPath_zeroValueVarsAndNoFilePath ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $err = camp_change_error_object_vars ($err, 0);
        $errFile = $err->getFileWithoutPath();
        $this->assertIdentical ("0", $errFile);
    }

    function test_getFileWithoutPath_emptyStringVars ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/non-dir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $err = camp_change_error_object_vars ($err, $p_type = "");
        $errFile = $err->getFileWithoutPath();
        $this->assertEqual ("", $errFile);
    }

    function test_trailingSlashInFile()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "nonfile.php", 3, $this->vars->time, $this->vars->backtrace);
        $err->m_file .= "/";
        $err->getFileWithoutPath();
        $this->assertError();
    }

}

class TestOfGetIdX extends UnitTestCase
{
    function setUp(){
        $this->vars = new ErrorObjectVars();
    }

    function test_getId ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $errId = $err->getId();
        $this->assertEqual($errId, "2:Campsite:1.5:nonfile.php:3");

    }

    function test_getId_withSlashes ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $errId = $err->getId();
        $this->assertEqual($errId, "2:Campsite:1.5:nonfile.php:3");
    }

    function test_getId_zeroValues ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $err = camp_change_error_object_vars ($err, 0);
        $errId = $err->getId();
        $this->assertIdentical ($errId, "0:0:0:0:0");
    }

    function test_getId_withEmptyStrings ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $err = camp_change_error_object_vars ($err, "");
        $errId = $err->getId();
        $this->assertIdentical ($errId, "::::");
    }

    //function test_fileContainingColons ()
    //{
    //}
}
/*
class TestOfGetBacktraceArray extends UnitTestCase
{
    var $m_startFile = "admin.php";
    var $m_thisFile = "errorobject_test.php";

    function setUp(){
        $this->vars = new ErrorObjectVars();
    }

    function test_getBacktraceArray_nameOfFirstFile ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceArray();
        $firstFile = basename ($backtrace [0]["file"]);
        $this->assertEqual ($firstFile, $this->m_thisFile);
    }

   function test_getBacktraceArray_nameOfLastFile ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceArray();
        $lastFile = basename ($backtrace [count ($backtrace) - 1]["file"]);
        $this->assertEqual ($lastFile, $this->m_startFile);
    }

    function test_getBacktraceArray_zeroValueVars ()
    {
        $err = new ErrorObject("Campsite", "1.5", 2, "", "nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $err = camp_change_error_object_vars ($err, $p_type = 0);
        $backtrace = $err->getBacktraceArray();
        $this->assertIdentical ($backtrace, 0);
    }

    //function test_getFileWithoutPath_emptyStringVars ()
    //{
    //    $err = new ErrorObject("Campsite", "1.5", 2, "", "/non-dir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
    //    $err = camp_change_error_object_vars ($err, $p_type = "");
    //    $errFile = $err->getFileWithoutPath();
    //    $this->assertEqual ("", $errFile);
    //}
}
*/
class TestOfGetBacktraceStringX extends UnitTestCase
{
    var $m_startFile = "admin.php";
    var $m_thisFile = "errorobject_test.php";

    function setUp(){
        $this->vars = new ErrorObjectVars();
    }

    function test_getBacktraceString_nameOfThisFile ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceString();
        $this->assertWantedPattern ("/$this->m_thisFile/", $backtrace);
    }

    function test_getBacktraceString_nameOfMostRecentFile ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceString();
        $this->assertWantedPattern ("/$this->m_startFile/", $backtrace);
    }

    function test_getBacktraceString_ThisFileInBraces ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceString();
        $this->assertWantedPattern ("/\[[^:]*$this->m_thisFile\:[1-9][0-9]*\]/", $backtrace);
    }

    function test_getBacktraceString_mostRecentFileInBraces ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceString();
        $this->assertWantedPattern ("/\[[^:]*$this->m_startFile\:[1-9][0-9]*\]/", $backtrace);
    }

    function test_getBacktraceString_lineNumberAtLinesEnd ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceString();
        $this->assertWantedPattern ("/\:[1-9][0-9]*\]/", $backtrace);
    }

    function test_formatOfLine ()
    {
        $err = new ErrorObject ("Campsite", "1.5", 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $err->getBacktraceString();
        $this->assertWantedPattern
            ("/[a-z_]+\:\:[a-z_]+\(\)\ called\ at\ \[[^][:]+\.php\:[1-9][0-9]*\]/", $backtrace);
    }


    // --- Create a test for too many colons ---
}

/**
 * Clear the value of each variable in $p_errObj.  The purpose of this
 * function is to test errObj methods when confronted with non-values.
 *
 * @param error
 */
function camp_change_error_object_vars ($p_errObj, $p_newValue = "")
{
    //    $newValue = $p_var;

    $p_errObj->m_software = $p_newValue;
    $p_errObj->m_version = $p_newValue;
    $p_errObj->m_num = $p_newValue;
    $p_errObj->m_str = $p_newValue;
    $p_errObj->m_file = $p_newValue;
    $p_errObj->m_line = $p_newValue;
    $p_errObj->m_context = $p_newValue;
    $p_errObj->m_backtrace = $p_newValue;

    return $p_errObj;
}

class ErrorObjectVars
{
    function ErrorObjectVars()
    {
        $this->time = "Fri, 17 Mar 2006 09:47:15 -0500";
        $this->backtrace = debug_backtrace();
        $this->backtraceString = "bugreporter::bugreporter() called at [/usr/local/campsite/www-common/html/admin.php:146]
report_bug() called at [:]
errorobject() called at [/usr/local/campsite/www-common/html/classes/BugReporter.php:165]
errorobject::errorobject() called at [/usr/local/campsite/www-common/html/classes/BugReporter.php:26]
bugreporter::bugreporter() called at [/usr/local/campsite/www-common/html/admin-files/senderrorform.php:7]
require_once() called at [/usr/local/campsite/www-common/html/admin.php:110]";
    }
}
