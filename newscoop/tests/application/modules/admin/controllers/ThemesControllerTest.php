<?php

class Admin_ThemesControllerTest extends AdminControllerTestCase
{
    public function testIndexAction()
    {
        $params = array( 'action' => 'index', 'controller' => 'Themes', 'module' => 'admin' );
        $url = $this->url( $this->urlizeOptions( $params ) );
        $this->dispatch( $url );

        // assertions
        $this->assertModule( $params['module'] );
        $this->assertController( $params['controller'] );
        $this->assertAction( $params['action'] );
    }


}



