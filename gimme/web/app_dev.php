<?php

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Newscoop\Gimme\Framework;

/**
 * Create Symfony kernel
 */
$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();

/**
 * Create request object from global variables
 */
$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);