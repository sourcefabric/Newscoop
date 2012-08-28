<?php
// goes to install process if configuration files does not exist yet
if (!defined('INSTALL') && (!file_exists(APPLICATION_PATH . '/../conf/configuration.php')
    || !file_exists(APPLICATION_PATH . '/../conf/database_conf.php'))) {
    $subdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));
    
    if (strpos($subdir, 'install') === false) { 
    //    header("Location: $subdir/install/");
    //    exit;
    }
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../include'),
    realpath(APPLICATION_PATH . '/../../dependencies/include'),
    get_include_path(),
)));

//require Composer autoloader
require_once realpath(APPLICATION_PATH . '/../../vendor/autoload.php');

/** Zend_Application */
if (defined('INSTALL')) {
    $oldErrorReporting = error_reporting();
    error_reporting(0);

    if (!class_exists('Zend_Application', TRUE)) {
        die('Missing dependency! Please install Zend Framework library!');
    }

    error_reporting($oldErrorReporting);
}

/**
 * Build container
 */
$containerFactory = new \Newscoop\DependencyInjection\ContainerFactory();
$container = $containerFactory->buildContainer();

/**
 * Set container to the Zend_Registry and fill zend application options
 */
\Zend_Registry::set('container', $container);
$config = $container->getParameterBag()->all();

// Create application, bootstrap, and run
$application = new \Zend_Application(APPLICATION_ENV, new \Zend_Config($config));