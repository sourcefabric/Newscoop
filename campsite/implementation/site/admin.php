<?php
/**
 * This file gets called before any file in the "admin-files" directory is executed.
 * Think of it as a wrapper for all admin interface scripts.
 * Here you can set up anything that should be applied globally to all scripts.
 */

// goes to install process if configuration files does not exist yet
if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/configuration.php')
        || !file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/database_conf.php')) {
    header('Location: /install/index.php');
}

require_once($_SERVER['DOCUMENT_ROOT'].'/index.php');

?>