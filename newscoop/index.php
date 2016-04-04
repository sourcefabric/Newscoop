<?php
$uri = '';
if (isset($_SERVER)) {
    $installPrefix = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = substr($uri, strlen($installPrefix)) ?: '';
    $uri = ltrim($uri, '/');
}

switch (true) {
    case substr($uri, 0, strlen('_statistics')) === '_statistics':
        require_once __DIR__ . '/classes/Statistics.php';
        $stats_only = false;
        Statistics::ProcessStats($stats_only);
        exit(0);
        break;

    default:
        require_once __DIR__ . '/public/index.php';
}
