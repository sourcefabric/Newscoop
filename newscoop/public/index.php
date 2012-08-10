<?php

if (strpos('gimme', $_SERVER['REQUEST_URI']) !== true) {
    require_once __DIR__.'/../../gimme/web/app_dev.php';
} else {
	require_once __DIR__ . '/../application.php';
	$application->bootstrap();
	$application->run();
}
