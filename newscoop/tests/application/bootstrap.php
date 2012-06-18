<?php

define('APPLICATION_ENV', 'testing');
define('IN_PHPUNIT', true);

require_once __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/../../application.php';
$application->bootstrap();

require_once __DIR__ . '/../RepositoryTestCase.php';
require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../AdoDbDoctrineAdapter.php';
