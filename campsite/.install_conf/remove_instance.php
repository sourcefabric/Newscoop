<?php

$etc_dir = trim($GLOBALS['argv'][1]);
$instance_name = trim($GLOBALS['argv'][2]);
$no_backup = $GLOBALS['argv'][3] == '--no_backup';

if ($etc_dir == "")
	die("Please supply the configuration directory as the first argument.\n");
if ($instance_name == "")
	die("Please supply the instance name as the second argument.\n");

// include install_conf.php file
require_once("$etc_dir/install_conf.php");
require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");
require_once("$etc_dir/$instance_name/database_conf.php");

if (!$no_backup) {
	// backup instance
	$cmd = $Campsite['BIN_DIR'] . "/backup_instance \$'$instance_name'";
	exec_command($cmd);
}

// remove WWW directory
$www_dir = $Campsite['WWW_DIR'] . "/$instance_name";
$cmd = "rm -fr \$'$www_dir'";
exec_command($cmd);

// remove etc directory
$etc_dir = $Campsite['ETC_DIR'] . "/$instance_name";
$cmd = "rm -fr \$'$etc_dir'";
exec_command($cmd);

// drop database
if (($res = connect_to_database()) != 0)
	exit_with_error($res);
mysql_query("DROP DATABASE $p_db_name");

?>
