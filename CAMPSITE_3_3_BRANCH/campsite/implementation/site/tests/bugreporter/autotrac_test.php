<?php

require_once ('simpletest/unit_tester.php');
require_once ('simpletest/reporter.php');
require_once('simpletest/web_tester.php');

global $autotrac_test_delay;
$autotrac_test_delay = 1000000;

class TestOfAutotrac extends WebTestCase 
{

    function setUp ( )
    {
        global $autotrac_test_delay;
        usleep ($autotrac_test_delay);
        $this->vars = new AutotracVars ( );
    }

    function test_siteExists ( )
    {
        $this->assertTrue($this->get($this->vars->m_autotracUrl));
    }
}

class TestOfAutotracPing extends WebTestCase
{
    function setUp ( )
    {
        global $autotrac_test_delay;
        usleep ($autotrac_test_delay);
        $this->vars = new AutotracVars ( );
    }

    function test_siteExists ( )
    {
        $this->assertTrue($this->get($this->vars->m_pingUrl));
    }

    function test_correctResponse ( )
    {
        $this->get($this->vars->m_pingUrl);
        $this->assertWantedPattern('/\bpong\b/');
    }

    function test_noErrorsInReply ()
    {
        $this->get($this->vars->m_pingUrl);
        $this->assertNoUnwantedPattern('/\berror\b/i');
    }
}

class TestOfAutotracNewreport extends WebTestCase
{
    function setUp ( )
    {
        global $autotrac_test_delay;
        usleep ($autotrac_test_delay);
        $this->vars = new AutotracVars ( );
    }

    function test_siteExists ( )
    {
        $this->assertTrue($this->get($this->vars->m_newreportUrl));
    }

    // --- Eventually this may (or may not) show a form to post ---
    function test_correctResponse ( )
    {
        $this->get($this->vars->m_newreportUrl);
        $this->assertWantedPattern('/\berror\b/i');
    }

    function test_postNoVariables ( )
    {
        $this->post($this->vars->m_newreportUrl, 
                    array());

        $this->assertWantedPattern ('/\berror\b/');
        $this->assertWantedPattern ('/\berror\ id\b/i');

        # --- If we try to req.write() a Python None, Trac gets confused 
        # and puts the data to write before the header info before the.  
        # This test is confirming that didn't happen.  
        $this->assertNoUnwantedPattern ('/^Status:/');
    }

    function test_postSingleVariable ( )
    {
        $this->post($this->vars->m_newreportUrl, 
                    array('f_software' => 'Campware'));

        $this->assertWantedPattern('/\berror\b/');
        $this->assertWantedPattern('/\berror id\b/i');

        $this->assertNoUnwantedPattern ('/^Status:/');
    }

    function test_postJustErrorId ( )
    {
        $this->post($this->vars->m_newreportUrl, 
                    array('f_id' => $this->vars->m_errorId));

        $this->assertWantedPattern('/\baccepted\b/');

        $this->assertNoUnwantedPattern ('/\berror\b/');
        $this->assertNoUnwantedPattern ('/^Status:/');
    }

    function test_postMultiVars ( )
    {
        $this->post($this->vars->m_newreportUrl, 
                    array('f_id' => $this->vars->m_errorId,
                          'f_version' => '1.5', 
                          'f_software' => 'Campware'
                          ) );

        $this->assertWantedPattern('/\baccepted\b/');
        $this->assertWantedPattern('/\bversion\b.+\b1\.5\b/');
        $this->assertWantedPattern('/\bsoftware\b.+\bCampware\b/');

        $this->assertNoUnwantedPattern ('/\berror\b/');
        $this->assertNoUnwantedPattern ('/^Status:/');
    }

    function test_postMultiBlankVars ( )
    {
        $this->post($this->vars->m_newreportUrl, 
                    array('f_id' => $this->vars->m_errorId,
                          'f_software' => '',
                          'f_version' => '')
                    );

        
        $this->assertNoUnwantedPattern ('/\berror\b/i');
        $this->assertNoUnwantedPattern ('/^Status:/');
    }

    function test_noErrorsInReply ()
    {    
        $this->post($this->vars->m_newreportUrl, 
                    array('f_id' =>$this->vars->m_errorId, 
                          'f_software' => 'Campware',
                          'f_version' => '1.5') );

        $this->assertNoUnwantedPattern ('/\berror\b/i');
        $this->assertNoUnwantedPattern ('/^Status:/');
    }
}
/*
class TestOfAutotracDebug extends WebTestCase
{
    function setUp ( )
    {
        $this->vars = new AutotracVars ( );
    }


    function test_postSingleVariable ( )
    {
        $this->post($this->vars->m_newreportUrl, 
                    array('f_software' => 'Campware'));

        $this->get ($this->vars->m_debugUrl);

        $this->assertWantedPattern('/not yet implemented/i');
    }
    
}
*/
class AutotracVars 
{
    function AutotracVars ( )
    {
        $this->m_tracDomain = "localhost";
        $this->m_tracUrl = "http://$this->m_tracDomain/tracunit";
        $this->m_autotracUrl = "$this->m_tracUrl/autotrac";
        $this->m_pingUrl = "$this->m_autotracUrl/ping";
        $this->m_newreportUrl = "$this->m_autotracUrl/newreport";
        $this->m_debugUrl = "$this->m_autotracUrl/debug";
//         $this->m_errorId = "(error ID)";
        $this->m_errorId = '2:xxx:yyy:zzz:5';
        
    }
}    

?>