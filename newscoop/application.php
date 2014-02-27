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


$response = $kernel->handle($request, \Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST, false);
$response->send();
$kernel->terminate($request, $response);
