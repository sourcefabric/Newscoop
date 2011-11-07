<?php

class Application_Plugin_ContentType extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        // When no Content-Type has been set, set the default text/html; charset=utf-8
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');

        switch ($request->getModuleName()) {
            case 'default':
                $response->setHeader('Cache-Control', 'public, max-age=3600', true);
                $response->setHeader('Pragma', 'cache', true);
                break;
        }
    }
}

