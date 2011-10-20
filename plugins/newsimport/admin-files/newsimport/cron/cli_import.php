#!/usr/bin/env php
<?php

if ("cli" != php_sapi_name()) {
    exit(1);
}

$spec_run = false;
$spec_params = array();
foreach($argv as $one_arg) {
    if (!$spec_run) {
        if ('--' == $one_arg) {
            $spec_run = true;
        }
        continue;
    }
    $one_arg = trim($one_arg, '-');
    $one_spec_arr = explode('=', $one_arg);
    if (2 == count($one_spec_arr)) {
        $spec_params[$one_spec_arr[0]] = $one_spec_arr[1];
    }
}
$GLOBALS['import_params'] = $spec_params;

$plugin_dir = dirname(dirname(dirname(dirname(__FILE__))));
require_once($plugin_dir.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'NewsImportEnv.php');

if ( ("cli" == php_sapi_name()) && (!isset($GLOBALS['g_cliInited'])) ) {
    NewsImportEnv::BootCli();
    $GLOBALS['g_cliInited'] = true;
}

$msg = NewsImportEnv::ImportResponse($spec_params);
echo $msg;

?>