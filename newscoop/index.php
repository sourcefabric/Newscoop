<?php

// collect statistics
require_once(dirname(__FILE__) . '/classes/Statistics.php');
$stats_only = false;
Statistics::ProcessStats($stats_only);
if ($stats_only) {
    exit(0);
}

// run zend
require_once dirname(__FILE__) . '/public/index.php';
