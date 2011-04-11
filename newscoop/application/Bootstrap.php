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

        // case-insensitive annotations
        $autoloader->pushAutoloader(function($class) {
            $file = implode('/', array_map('ucfirst', explode('\\', $class)));
            include_once "$file.php";
        }, 'Newscoop\Annotations');

        // controller plugin loader
        $autoloader->pushAutoloader(function($class) {
            $front = Zend_Controller_Front::getInstance();
            $path = $front->getControllerDirectory('admin');
            $file = array_pop(explode('_', $class));
            include_once "$path/plugins/$file.php";
        }, 'Admin_Controller_Plugin_');

        return $autoloader;
    }
}
