<?php

if (!is_array($GLOBALS['argv'])) {
	echo "Can't read command line arguments\n";
	exit(1);
}

$etc_dir = isset($GLOBALS['argv'][1]) ? trim($GLOBALS['argv'][1]) : "";
$instance_name = isset($GLOBALS['argv'][2]) ? trim($GLOBALS['argv'][2]) : "";
$arg3 = isset($GLOBALS['argv'][3]) ? trim($GLOBALS['argv'][3]) : "";

if ($etc_dir == "")
	die("Please supply the configuration directory as the first argument.\n");
if ($instance_name == "")
	die("Please supply the instance name as the second argument.\n");
$silent = $arg3 == "--silent_exit";

// include install_conf.php file
require_once("$etc_dir/install_conf.php");
require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");
if (!is_file("$etc_dir/$instance_name/database_conf.php")) {
	if ($silent)
		exit(0);
	echo "Database configuration file does not exist, can't backup\n";
	exit(1);
}
require_once("$etc_dir/$instance_name/database_conf.php");
$html_dir = $Campsite['WWW_DIR'] . "/$instance_name/html";
$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name";
exec_command("mkdir -p \$'$backup_dir'");

// backup look (templates) directory
if (archive_file("$html_dir/look", $backup_dir, "$instance_name-look", $output) != 0)
	exit_with_error($output);

// backup images directory
if (archive_file("$html_dir/images", $backup_dir, "$instance_name-images", $output) != 0)
	exit_with_error($output);

// backup configuration directory
if (archive_file("$etc_dir/$instance_name", $backup_dir, "$instance_name-conf", $output) != 0)
	exit_with_error($output);

// backup the database
$db_file_name = "$backup_dir/$instance_name-database.sql";
if (is_file($db_file_name) && (backup_file($db_file_name, $output) != 0))
	exit_with_error($output);
if (backup_database($instance_name, $db_file_name, $output) != 0)
	exit_with_error($output);
if (archive_file($db_file_name, $backup_dir, "$instance_name-database", $output) != 0)
	exit_with_error($output);

// create the final archive
$cmd = "pushd $backup_dir > /dev/null && tar cf "
	. escapeshellarg("$instance_name-bak.tar")
	. " *.tar.gz && popd > /dev/null";
exec_command($cmd);
unlink("$backup_dir/$instance_name-conf.tar.gz");
unlink("$backup_dir/$instance_name-images.tar.gz");
unlink("$backup_dir/$instance_name-look.tar.gz");
unlink("$backup_dir/$instance_name-database.tar.gz");
unlink("$backup_dir/$instance_name-database.sql");

?>
