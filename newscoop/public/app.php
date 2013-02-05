<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Development prod
 */
require_once(__DIR__ . '/../constants.php');
$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__ . '/../db_connect.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Newscoop\Gimme\Framework;
use Symfony\Component\Routing\Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

try {
	/**
	 * Create Symfony kernel
	 */
	if (APPLICATION_ENV === 'production') {
		$kernel = new AppKernel('prod', false);
	} else if (APPLICATION_ENV === 'development') {
		$kernel = new AppKernel('dev', true);
	} else {
		$kernel = new AppKernel('APPLICATION_ENV', true);
	}
	
	$kernel->loadClassCache();

	/**
	 * Create request object from global variables
	 */
	$request = Request::createFromGlobals();

	$response = $kernel->handle($request, \Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST, false);
} catch (NotFoundHttpException $e) {
	require_once __DIR__ . '/../application.php';
	$application->bootstrap();
	$application->run();
}

$response->send();
$kernel->terminate($request, $response);