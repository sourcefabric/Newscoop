<?php

require_once __DIR__ . '/../application.php';
$application->bootstrap();

if (empty($GLOBALS['zend_bootstrap_only'])) { // workaround for CS-3806
    $application->run();
}
