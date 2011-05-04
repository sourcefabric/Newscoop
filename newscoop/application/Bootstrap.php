<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Init autoloader
     */
    protected function _initAutoloader()
    {
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../classes/'),
            realpath(APPLICATION_PATH . '/../classes/Extension/'),
            realpath(APPLICATION_PATH . '/../template_engine/classes/'),
            realpath(APPLICATION_PATH . '/../template_engine/metaclasses/'),
        )) . PATH_SEPARATOR . get_include_path());

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(TRUE);

        // fix adodb loading error
        $autoloader->pushAutoloader(function($file) {
            return;
        }, 'ADO');

        return $autoloader;
    }

    /**
     * Init session
     */
    protected function _initSession()
    {
        $options = $this->getOptions();
        $name = isset($options['session']['name']) ? $options['session']['name'] : session_name();

        Zend_Session::setOptions(array(
            'name' => $name,
        ));

        Zend_Session::start();
    }

    /**
     * Init bootstrap plugin
     */
    protected function _initBootstrapPlugin()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_Bootstrap($this->getOptions()));
    }
}
