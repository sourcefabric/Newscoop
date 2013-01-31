<?php

if (!file_exists(__DIR__ . '/../vendor')) {
    echo "Missing dependency! Please install all dependencies with composer.";
    echo "<pre>curl -s https://getcomposer.org/installer | php <br/>php composer.phar install</pre>";
    die;
}

error_reporting(error_reporting() & ~E_STRICT & ~E_DEPRECATED);

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

// goes to install process if configuration files does not exist yet
if (php_sapi_name() !== 'cli'
    && !defined('INSTALL')
    && (!file_exists(APPLICATION_PATH . '/../conf/configuration.php')
        || !file_exists(APPLICATION_PATH . '/../conf/database_conf.php'))
) {
    $subdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));
    if (strpos($subdir, 'install') === false) {
        header("Location: $subdir/install/");
        exit;
    }
}

// Build container
$containerFactory = new \Newscoop\DependencyInjection\ContainerFactory();
$container = $containerFactory->buildContainer();
\Zend_Registry::set('container', $container);

// Set container to the Zend_Registry and fill zend application options
$config = $container->getParameterBag()->all();

$application = new \Zend_Application(APPLICATION_ENV);
$iniConfig = APPLICATION_PATH . '/configs/application.ini';
if (file_exists($iniConfig)) {
    $userConfig = new \Zend_Config_Ini($iniConfig, APPLICATION_ENV);
    $config = $application->mergeOptions($config, $userConfig->toArray());
}

$application->setOptions($config);
