<?php
$pos = strpos($_SERVER['REQUEST_URI'], 'api');

if ($pos !== false) {
    require_once __DIR__ . '/../../gimme/web/app_dev.php';
} else {
	require_once __DIR__ . '/../application.php';
	$application->bootstrap();
	$application->run();
}
