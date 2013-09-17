<?php
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/application/bootstrap.php.cache';
require_once __DIR__ . '/application/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * THIS FILE IS STIL THERE FOR LEGACY (NOT BASED ON INDEX.PHP) FILES
 * IT'S COPYIED FROM INDEX.PHP
 */

/**
 * Create Symfony kernel
 */
if (APPLICATION_ENV === 'production') {
    $kernel = new AppKernel('prod', false);
} else if (APPLICATION_ENV === 'development' || APPLICATION_ENV === 'dev') {
    $kernel = new AppKernel('dev', true);
} else {
    $kernel = new AppKernel(APPLICATION_ENV, true);
}

$kernel->loadClassCache();
$request = Request::createFromGlobals();

try {
    $response = $kernel->handle($request, \Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST, false);
    $response->send();
    $kernel->terminate($request, $response);
} catch (NotFoundHttpException $e) {
    if (!\Zend_Registry::isRegistered('container')) {
        $container = $kernel->getContainer();
        \Zend_Registry::set('container', $container);
    }
    
    $container = \Zend_Registry::get('container');

    // Fill zend application options
    $config = $container->getParameterBag()->all();
    $application = new \Zend_Application(APPLICATION_ENV);
    $iniConfig = APPLICATION_PATH . '/configs/application.ini';
    if (file_exists($iniConfig)) {
        $userConfig = new \Zend_Config_Ini($iniConfig, APPLICATION_ENV);
        $config = $application->mergeOptions($config, $userConfig->toArray());
    }

    $application->setOptions($config);
    if (!defined('DONT_BOOTSTRAP_ZEND')) {
        $application->bootstrap();
    }
}
