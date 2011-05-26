<?php

class AdminControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = array( $this, 'appBootstrap' );
        $this->_loginRealUser();
        parent::setUp();
    }
 
    public function appBootstrap()
    {
    }
    
    private function _loginRealUser()
    {
        $this->getRequest()
            ->setControllerKey( 'index' )
            ->setModuleName( 'admin' )
            ->setActionName( 'index' )
            ->setDispatched( false )
            ->setMethod( 'POST' )
            ->setPost( array
            (
                'f_user_name' => 'admin',
                'f_password'  => 'admin',
            ));
            
        $this->log
        ( 
            $this->getFrontController()->dispatch( $this->getRequest() ) 
        );
        $this->assertRedirect;
//        $this->assertTrue( Zend_Auth::getInstance()->hasIdentity() );
    }
    
    public static function log( $x )
    {
        openlog( "newscoop admin test: ", LOG_PID | LOG_PERROR, LOG_LOCAL0);
        ob_start();
        var_dump( $x );
        $d = ob_get_contents();
        ob_end_clean();
        var_dump( syslog( LOG_WARNING, $d."\n" ) );
        closelog();
    }
}