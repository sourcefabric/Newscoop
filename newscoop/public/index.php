<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!file_exists(__DIR__ . '/../vendor')) {
    echo "Missing dependency! Please install all dependencies with composer.";
    echo "<pre>curl -s https://getcomposer.org/installer | php <br/>php composer.phar install  --no-dev</pre>";
    die;
}

require_once __DIR__ . '/../constants.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

error_reporting(error_reporting() & ~E_STRICT & ~E_DEPRECATED);

// don't add php session Cache-Control values.
session_cache_limiter('none');

// check if this is upgrade
if (php_sapi_name() !== 'cli' &&
    file_exists(APPLICATION_PATH . '/../conf/configuration.php') &&
    file_exists(APPLICATION_PATH . '/../conf/database_conf.php') &&
    file_exists(APPLICATION_PATH . '/../conf/upgrading.php') &&
    file_exists(APPLICATION_PATH . '/../conf/installation.php')
) {
    // it's old installation
    // remove installation mark
    @unlink(APPLICATION_PATH . '/../conf/installation.php');
}

// check if this is installation
if (php_sapi_name() !== 'cli' &&
    !defined('INSTALL') &&
    (!file_exists(APPLICATION_PATH . '/../conf/database_conf.php'))
) {
    $subdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));
    if (strpos($subdir, 'install') === false) {
        header("Location: $subdir/install/");
        exit;
    }
}

require_once __DIR__ . '/../application/bootstrap.php.cache';
require_once __DIR__ . '/../application/AppKernel.php';

/**
 * Create Symfony kernel
 */
if (APPLICATION_ENV === 'production') {
    $kernel = new AppKernel('prod', false);
} else if (APPLICATION_ENV === 'development' || APPLICATION_ENV === 'dev') {
    $current_error_reporting = error_reporting();
    Debug::enable();
    error_reporting($current_error_reporting);
    $kernel = new AppKernel('dev', true);
} else {
    $kernel = new AppKernel(APPLICATION_ENV, true);
}

$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
