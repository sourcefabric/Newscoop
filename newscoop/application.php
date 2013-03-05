<?php
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/vendor/autoload.php';

$container = \Zend_Registry::get('container');

// Set container to the Zend_Registry and fill zend application options
$config = $container->getParameterBag()->all();

$application = new \Zend_Application(APPLICATION_ENV);
$iniConfig = APPLICATION_PATH . '/configs/application.ini';
if (file_exists($iniConfig)) {
    $userConfig = new \Zend_Config_Ini($iniConfig, APPLICATION_ENV);
    $config = $application->mergeOptions($config, $userConfig->toArray());
}

$application->setOptions($config);
