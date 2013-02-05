<?php
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

if (!Zend_Registry::isRegistered('container')) {
    // Build container
    $containerFactory = new \Newscoop\DependencyInjection\ContainerFactory();
    $container = $containerFactory->buildContainer();
    \Zend_Registry::set('container', $container);
}

// Set container to the Zend_Registry and fill zend application options
$config = $container->getParameterBag()->all();

$application = new \Zend_Application(APPLICATION_ENV);
$iniConfig = APPLICATION_PATH . '/configs/application.ini';
if (file_exists($iniConfig)) {
    $userConfig = new \Zend_Config_Ini($iniConfig, APPLICATION_ENV);
    $config = $application->mergeOptions($config, $userConfig->toArray());
}

$application->setOptions($config);
