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
    
    if (strpos($subdir, 'install') === false) { 
        header("Location: $subdir/install/");
        exit;
    }
}

//require Composer autoloader
$autoload = require_once __DIR__ . '/vendor/autoload.php';

if (is_dir(__DIR__ . '/library/Zend')) {
    // remove vendor zend lib from include path which would cause conflicts
    $includePaths = explode(PATH_SEPARATOR, get_include_path());
    array_shift($includePaths);
    set_include_path(implode(PATH_SEPARATOR, $includePaths));
}

if (!class_exists('Zend_Application')) {
    die('Missing dependency! Please install Zend Framework library!');
}

// Create application, bootstrap, and run
$application = new \Zend_Application(APPLICATION_ENV);

// Set config
$setConfig = function(\Zend_Application $application) {
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
