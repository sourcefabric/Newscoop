<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

define('APPLICATION_ENV', 'testing');
define('IN_PHPUNIT', true);
define('DIR_SEP', DIRECTORY_SEPARATOR);

require_once __DIR__ . '/../../application.php';

$application->bootstrap('container');
$application->bootstrap('session');

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../RepositoryTestCase.php';
require_once __DIR__ . '/../AdoDbDoctrineAdapter.php';
