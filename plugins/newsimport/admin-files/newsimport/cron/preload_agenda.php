#!/usr/bin/env php
<?php

$plugin_dir = dirname(dirname(dirname(dirname(__FILE__))));
require_once($plugin_dir.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'NewsImportEnv.php');
require_once($plugin_dir.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'AgendaPreloader.php');

if ( ("cli" == php_sapi_name()) && (!isset($GLOBALS['g_cliInited'])) ) {
    NewsImportEnv::BootCli();
    $GLOBALS['g_cliInited'] = true;
}

$preload_default_locks = NewsImportEnv::GetLockDir();

// whether we can start now
$locks_path_dir = NewsImportEnv::AbsolutePath($preload_default_locks);
if (!NewsImportEnv::Start($locks_path_dir, 'preloading')) {
    exit(1);
    //$msg = 'preloads_locked';
    //return $msg;
}

AgendaPreloader::Preload();



NewsImportEnv::Stop($locks_path_dir, 'preloading');

?>