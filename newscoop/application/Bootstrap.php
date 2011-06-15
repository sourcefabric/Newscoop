<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Init autoloader
     */
    protected function _initAutoloader()
    {
        global $g_campsiteDir;

        $g_campsiteDir = realpath(APPLICATION_PATH . '/../');

        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../classes/'),
            realpath(APPLICATION_PATH . '/../classes/Extension/'),
            realpath(APPLICATION_PATH . '/../template_engine/classes/'),
            realpath(APPLICATION_PATH . '/../template_engine/metaclasses/'),
            realpath(APPLICATION_PATH . '/../library/Service/'),
        )) . PATH_SEPARATOR . get_include_path());

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(TRUE);

        // fix adodb loading error
        $autoloader->pushAutoloader(function($file) {
            return;
        }, 'ADO');

        // init session before loading plugins to prevent session start errors
        $this->bootstrap('session');

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

    protected function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_Upgrade());
        $front->registerPlugin(new Application_Plugin_CampPluginAutoload());
        $front->registerPlugin(new Application_Plugin_Bootstrap($this->getOptions()));
    }
}
