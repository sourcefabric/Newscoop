<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
define('APPLICATION_ENV', 'testing');

defined('IN_PHPUNIT')
    || define('IN_PHPUNIT', TRUE);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
    '/usr/share/php/libzend-framework-php',
)));

/** Zend_Application */
require_once 'Zend/Application.php';

require_once dirname(__FILE__) . '/../RepositoryTestCase.php';

// Create application, bootstrap, and run
$application = new Zend_Application
(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();
clearstatcache();
