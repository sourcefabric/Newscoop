<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once ( CAMP_CLASSES . "BugReporter.php");


class TestOfBugReporter extends UnitTestCase
{
    function test_with6Args()
    {
        $vars = new BugReporterVars();
        $reporter = new BugReporter (1, "bad bad error", "file.php", 77, "Campsite", "2.5.0", "11:00", "[backtrace]");
        $this->assertEqual ($reporter->m_time, "11:00");
        $this->assertEqual ($reporter->m_backtrace, "[backtrace]");
    }
}

class TestOfBugSetServer extends UnitTestCase
{

    function setUp () {
        $this->reporter = new BugReporter (1, "bad bad error", "file.php", 77, "Campsite", "2.5.0");
    }

    function test_serverPathIsCorrect ()
    {
        $vars = new BugReporterVars();
        $this->assertIdentical ($this->reporter->__server, $vars->defaultServer);
    }

    function test_setServer()
    {
        $url = "http://localhost/admin/test/dummytracserver";
        $this->assertNotEqual($url, $this->reporter->getServer());
        $this->reporter->setServer($url);
        $this->assertEqual ($url, $this->reporter->getServer());
    }
}


class TestOfPingServer extends UnitTestCase
{
    function setUp () {
        $this->reporter = new BugReporter (1, "bad bad error", "file.php", 77, "Campsite", "2.5.0");
    }

    function test_pingPathIsCorrect ()
    {
        $vars = new BugReporterVars();
        $this->assertIdentical ($this->reporter->__ping, $vars->defaultPing);
    }

    function test_pingServer_redirect ()
    {
        $this->reporter->setServer("http://test.n-space.org/mocktrac/302");
        $this->reporter->__ping = "http://test.n-space.org/mocktrac/302";
        $wasResponse = $this->reporter->pingServer();
        $this->assertIdentical (True, $wasResponse);
    }

    function test_pingServer ()
    {
        $this->reporter->__ping = "http://test.n-space.org/mocktrac/dummytracserver";
        $wasResponse = $this->reporter->pingServer ();
        $this->assertIdentical (True, $wasResponse);
    }

    function test_pingServer_404 () {
        $this->reporter->setServer("http://test.n-space.org/mocktrac/404");
        $wasResponse = $this->reporter->pingServer();
        $this->assertIdentical (False, $wasResponse);
    }

    // --- Also test a ping that doesn't return "pong" ---
    function test_notPong ()
    {
        $this->reporter->__ping = "http://test.n-space.org/mocktrac/nopong";
        $this->assertFalse ($this->reporter->pingServer ());
        $body = $this->reporter->__responseBody;
        $this->assertNoUnwantedPattern ("/\bpong\b/", "$body");
    }

    function test_pong ()
    {
        $this->reporter->setServer ("http://test.n-space.org/mocktrac/dummytracserver");
        $this->reporter->__ping = "http://test.n-space.org/mocktrac/dummytracserver";
        $this->reporter->pingServer ();
        $body = $this->reporter->__responseBody;
        $this->assertWantedPattern ("/\bpong\b/", "$body");
    }


    // --- Also add in tests which set the server ---
}

class TestOfSendToServer extends UnitTestCase
{
    function setUp () {
        $this->reporter = new BugReporter (1, "bad bad error", "file.php", 77, "Campsite", "2.5.0");
    }

    function test_DefaultNewreportPathIsCorrect ()
    {
        $vars = new BugReporterVars();
        $this->assertIdentical ($this->reporter->m_newReport, $vars->defaultNewreport);
    }

    function test_returnsTrueWhenReceivesAccepted ()
    {
        $this->reporter->m_newReport =       "http://test.n-space.org/mocktrac/basicnewreport";
        $this->assertTrue ($this->reporter->sendToServer() );
    }

    function test_returnsFalseWhenReceivesError ()
    {
        $this->reporter->m_newReport =       "http://test.n-space.org/mocktrac/newreporterror";
        $this->assertFalse ($this->reporter->sendToServer() );
    }

    function test_returnsFalseWhenReceivesJabberwocky ()
    {
        $this->reporter->m_newReport =       "http://test.n-space.org/mocktrac/jabberwocky";
        $this->assertFalse ($this->reporter->sendToServer() );
    }

    function test_returnsFalseWhenReceives404 ()
    {
        $this->reporter->m_newReport =       "http://test.n-space.org/mocktrac/404";
        $this->assertFalse ($this->reporter->sendToServer() );
    }

    function test_returnsFalseWhenReceivesServerNotFound ()
    {
        $this->reporter->m_newReport =       "http://test.n-space.or";
        $this->assertFalse ($this->reporter->sendToServer() );
    }

    function test_accepted ()
    {
        $this->reporter->m_newReport =       "http://test.n-space.org/mocktrac/basicnewreport";
        $this->reporter->sendToServer();
        $this->assertWantedPattern ("/\baccepted\b/", $this->reporter->__responseBody);
    }

    function test_paramsAreSuccessfullySent ()
    {
        $this->reporter->m_newReport = "http://test.n-space.org/mocktrac/echo.php";
        $this->reporter->sendToServer();
        $this->assertWantedPattern ("/\bf_software\b.*\n?.*\bCampsite\b/", $this->reporter->__responseBody);
        $this->assertWantedPattern ("/\bf_version\b.*\n?.*\b2.5.0\b/", $this->reporter->__responseBody);
        $this->assertWantedPattern ("/\bf_num\b.*\n?.*\b1\b/", $this->reporter->__responseBody);
        $this->assertWantedPattern ("/\bf_str\b.*\n?.*\bbad\ bad\ error/", $this->reporter->__responseBody);
        $this->assertWantedPattern ("/\bf_line\b.*\n?.*\b77\b/", $this->reporter->__responseBody);
        $this->assertWantedPattern ("/\bf_id\b.*\n?.*1:Campsite:2.5.0:file.php:77/", $this->reporter->__responseBody);

        /*
        $this->assertWantedPattern ("/\bsoftware\b.*\n?.*Campsite/", $this->reporter->__responseBody);
        */
    }

}

class TestOfGetFileWithoutPath extends UnitTestCase
{
    function setUp(){
        $this->vars = new BugReporterVars();
    }

    function test_getFileWithoutPath()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $errId = $reporter->getFileWithoutPath();
        $this->assertEqual ($errId, "file.php");
    }

    function test_getFileWithoutPath_noPath ()

    {
        $reporter = new BugReporter (2, "bad bad error", "file.php", 3, "Campsite", "2.5.0");
        $errId = $reporter->getFileWithoutPath();
        $this->assertEqual($errId, "file.php");
    }
    /*
    // --- Todo: The next few functions test with unusual values.
    // --- Disabled for the moment ---

    function test_getFileWithoutPath_zeroValueVars ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $reporter = camp_change_error_object_vars ($reporter, 0);
        $errFile = $reporter->getFileWithoutPath();
        $this->assertIdentical ("0", $errFile);
    }

    function test_getFileWithoutPath_zeroValueVarsAndNoFilePath ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $reporter = camp_change_error_object_vars ($reporter, 0);
        $errFile = $reporter->getFileWithoutPath();
        $this->assertIdentical ("0", $errFile);
    }

    function test_getFileWithoutPath_emptyStringVars ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $reporter = camp_change_error_object_vars ($reporter, $p_type = "");
        $errFile = $reporter->getFileWithoutPath();
        $this->assertEqual ("", $errFile);
    }
    */
    function test_trailingSlashInFile()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $reporter->m_file .= "/";
        $reporter->getFileWithoutPath();
        $this->assertError();
    }
}

class TestOfGetId extends UnitTestCase
{
    function setUp(){
        $this->vars = new BugReporterVars();
    }

    function test_getId ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $errId = $reporter->getId();
        $this->assertEqual($errId, "2:Campsite:2.5.0:file.php:3");

    }

    function test_getId_withSlashes ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $errId = $reporter->getId();
        $this->assertEqual($errId, "2:Campsite:2.5.0:file.php:3");
    }
    /*
    function test_getId_zeroValues ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $reporter = camp_change_error_object_vars ($reporter, 0);
        $errId = $reporter->getId();
        $this->assertIdentical ($errId, "0:0:0:0:0");
    }

    // --- Todo: The next few functions test with unusual values.
    // --- Disabled for the moment ---
    function test_getId_withEmptyStrings ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/non-dir/file.php", 3, "Campsite", "2.5.0");
        $reporter = camp_change_error_object_vars ($reporter, "");
        $errId = $reporter->getId();
        $this->assertIdentical ($errId, "::::");
    }

    //function test_fileContainingColons ()
    //{
    //}
    */
}

class TestOfGetBacktraceString extends UnitTestCase
{
    var $m_startFile = "admin.php";
    var $m_thisFile = "errorobject_test.php";

    function setUp(){
        $this->vars = new BugReporterVars();
    }

    /*
    // --- This test doesn't apply since name of this file is weeded out by current bugreporter ---
    function test_getBacktraceString_nameOfThisFile ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace, "Campsite", "2.5.0");
        //$reporter = new ErrorObject ("Campsite", 2.5.0, 2, "", "/nondir/nonfile.php", 3, $this->vars->time, $this->vars->backtrace );
        $backtrace = $reporter->getBacktraceString();
        $this->assertWantedPattern ("/$this->m_thisFile/", $backtrace);
    }
    */

    function test_getBacktraceString_nameOfMostRecentFile ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/nondir/nonfile.php", 3, "Campsite", "2.5.0", $this->vars->time, $this->vars->backtrace);
        $backtrace = $reporter->getBacktraceString();
        $this->assertWantedPattern ("/$this->m_startFile/", $backtrace);
    }

    function test_getBacktraceString_FileNameInBraces ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/nondir/nonfile.php", 3, "Campsite", "2.5.0");
        $backtrace = $reporter->getBacktraceString();
        $this->assertWantedPattern ("/\[[^:]*[a-zA-Z_\/-]*\:[1-9][0-9]*\]/", $backtrace);
    }

    function test_getBacktraceString_mostRecentFileInBraces ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/nondir/nonfile.php", 3, "Campsite", "2.5.0", $this->vars->time, $this->vars->backtraceString, $this->vars->time, $this->vars->backtrace);
        $backtrace = $reporter->getBacktraceString();
        $this->assertWantedPattern ("/\[[^:]*$this->m_startFile\:[1-9][0-9]*\]/", $backtrace);
    }

    function test_getBacktraceString_lineNumberAtLinesEnd ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/nondir/nonfile.php", 3, "Campsite", "2.5.0", $this->vars->time, $this->vars->backtrace);
        $backtrace = $reporter->getBacktraceString();
        $this->assertWantedPattern ("/\:[1-9][0-9]*\]/", $backtrace);
    }

    function test_formatOfLine ()
    {
        $reporter = new BugReporter (2, "bad bad error", "/nondir/nonfile.php", 3, "Campsite", "2.5.0", $this->vars->time, $this->vars->backtrace);
        $backtrace = $reporter->getBacktraceString();
        $this->assertWantedPattern
            ("/[a-z_]+\:\:[a-z_]+\(\)\ called\ at\ \[[^][:]+\.php\:[1-9][0-9]*\]/", $backtrace);
    }


    // --- Create a test for too many colons ---
}

class BugReporterVars {
    function BugReporterVars() {
        $this->defaultServer = "http://code.campware.org/projects/campsite/autotrac";
        $this->defaultPing = "$this->defaultServer/ping";
        $this->defaultNewreport = "$this->defaultServer/newreport";

        $this->time = "Fri, 17 Mar 2006 09:47:15 -0500";
        $this->backtrace = debug_backtrace();
        $this->backtraceString = "bugreporter::bugreporter() called at [/usr/local/campsite/www-common/html/admin.php:146]
report_bug() called at [:]
errorobject() called at [/usr/local/campsite/www-common/html/classes/BugReporter.php:165]
errorobject::errorobject() called at [/usr/local/campsite/www-common/html/classes/BugReporter.php:26]
bugreporter::bugreporter() called at [/usr/local/campsite/www-common/html/admin-files/senderrorform.php:7]
require_once() called at [/usr/local/campsite/www-common/html/admin.php:110]";    }
}

