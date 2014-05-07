<?php

class Application_Plugin_ContentType extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        // a workaround for wrongly set header here, when already being set (at theme export)
        $header_set = false;
        if (isset($GLOBALS['header_content_type_set']) && $GLOBALS['header_content_type_set']) {
            $header_set = true;
        }

        // When no Content-Type has been set, set the default text/html; charset=utf-8
        if (!$header_set) {
            $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        }
    }
}
