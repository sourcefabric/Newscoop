<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', __DIR__ . '/application');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// goes to install process if configuration files does not exist yet
if (!defined('INSTALL') && (!file_exists(APPLICATION_PATH . '/../conf/configuration.php')
    || !file_exists(APPLICATION_PATH . '/../conf/database_conf.php'))) {
    $subdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));
    header("Location: $subdir/install/");
    exit;
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../include'),
    get_include_path(),
)));

if (!is_file('Zend/Application.php')) {
	// include libzend if we don't have zend_application
	set_include_path(implode(PATH_SEPARATOR, array(
		'/usr/share/php/libzend-framework-php',
		get_include_path(),
	)));
}

/** Zend_Application */
require_once 'Zend/Application.php';

if (defined('INSTALL')) {
    $oldErrorReporting = error_reporting();
    error_reporting(0);

    if (!class_exists('Zend_Application', FALSE)) {
        die('Missing dependency! Please install Zend Framework library!');
    }

    error_reporting($oldErrorReporting);
}

// Create application, bootstrap, and run
$application = new \Zend_Application(APPLICATION_ENV);

// Set config
$setConfig = function(\Zend_Application $application) {
    require_once 'Zend/Config/Ini.php';
    $defaultConfig = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini-dist', APPLICATION_ENV);
    $config = $defaultConfig->toArray();
    if (is_readable(APPLICATION_PATH . '/configs/application.ini')) {
        try {
            $userConfig = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
            $config = $application->mergeOptions($config, $userConfig->toArray());
        } catch (\Zend_Config_Exception $e) { // ignore missing section
        }
    }
    $application->setOptions($config);
};

$setConfig($application);
unset($setConfig);
