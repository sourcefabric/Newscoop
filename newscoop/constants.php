<?php

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', __DIR__ . '/application');

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$GLOBALS['g_campsiteDir'] = realpath(APPLICATION_PATH . '/../');
