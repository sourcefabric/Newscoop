<?php

class Application_Plugin_ContentType extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        $response = $this->getResponse();

        // When no Content-Type has been set, set the default text/html; charset=utf-8
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');
    }
}

