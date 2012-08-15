<?php

// collect statistics
require_once(dirname(__FILE__) . '/classes/Statistics.php');
$stats_only = false;
Statistics::ProcessStats($stats_only);
if ($stats_only) {
    exit(0);
}

$pos = strpos($_SERVER['REQUEST_URI'], 'api') || strpos($_SERVER['REQUEST_URI'], '_profiler');

if ($pos !== false) {
	// run api
    require_once __DIR__ . '/../gimme/web/app_dev.php';
} else {
	// run zend
	require_once dirname(__FILE__) . '/public/index.php';
}
