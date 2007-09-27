<?php

if (!is_array($GLOBALS['argv'])) {
	echo "Can't read command line arguments\n";
	exit(1);
}

$etc_dir = isset($GLOBALS['argv'][1]) ? trim($GLOBALS['argv'][1]) : "";
$instance_name = isset($GLOBALS['argv'][2]) ? trim($GLOBALS['argv'][2]) : "";
$arg3 = isset($GLOBALS['argv'][3]) ? trim($GLOBALS['argv'][3]) : "";
$no_backup = $arg3 == '--no_backup';

if ($etc_dir == "") {
	die("Please supply the configuration directory as the first argument.\n");
}
if ($instance_name == "") {
	die("Please supply the instance name as the second argument.\n");
}

// include install_conf.php file
require_once("$etc_dir/install_conf.php");
require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");

if (!camp_is_readable("$etc_dir/install_conf.php")) {
	exit(1);
}

if (!$no_backup) {
	// backup instance
	$cmd = $Campsite['BIN_DIR'] . "/campsite-backup-instance "
		. camp_escape_shell_arg($instance_name) . " --silent --default_dir";
	camp_exec_command($cmd);
}

// remove WWW directory
$www_dir = $Campsite['WWW_DIR'] . "/$instance_name";
$cmd = "rm -fr " . camp_escape_shell_arg($www_dir);
camp_exec_command($cmd);

// check if the database conf file exists
$database_conf_file = "$etc_dir/$instance_name/database_conf.php";
if (!is_file($database_conf_file)) {
	exit(0);
}

// drop database
require_once($database_conf_file);
if (camp_connect_to_database() != 0) {
	camp_exit_with_error($res);
}
mysql_query("DROP DATABASE $instance_name");

// remove etc directory
$cmd = "rm -fr " . camp_escape_shell_arg($etc_dir) . "/" . camp_escape_shell_arg($instance_name);
camp_exec_command($cmd);

?>