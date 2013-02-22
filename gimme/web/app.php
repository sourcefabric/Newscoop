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
require_once(__DIR__ . '/../../newscoop/constants.php');
$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../../newscoop/db_connect.php';

use Symfony\Component\HttpFoundation\Request;

/**
 * Create Symfony kernel
 */
$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();

/**
 * Create request object from global variables
 */
$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);