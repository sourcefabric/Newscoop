<?php

use Newscoop\Log\Writer;

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
     * Init Log
     */
    protected function _initLog()
    {
        // get entity manager
        $this->bootstrap('doctrine');
        $em = $this->getResource('doctrine')
            ->getEntityManager();

        // create logger
        $writer = new Writer($em);
        return new Zend_Log($writer);
    }
}
