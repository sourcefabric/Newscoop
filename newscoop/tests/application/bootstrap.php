<?php

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', 'testing');

defined('IN_PHPUNIT')
    || define('IN_PHPUNIT', TRUE);

require_once __DIR__ . '/../../application.php';
$application->bootstrap();

clearstatcache();

require_once __DIR__ . '/../RepositoryTestCase.php';
require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../AdoDbDoctrineAdapter.php';
