<?php

$etc_dir = trim($GLOBALS['argv'][1]);
$instance_name = trim($GLOBALS['argv'][2]);

if ($etc_dir == "")
	die("Please supply the configuration directory as the first argument.\n");
if ($instance_name == "")
	die("Please supply the instance name as the second argument.\n");

// include install_conf.php file
require_once("$etc_dir/install_conf.php");
require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");
require_once("$etc_dir/$instance_name/database_conf.php");
$html_dir = $Campsite['WWW_DIR'] . "/$instance_name/html";
$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name";

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
exec($cmd, $output, $result);
if ($result != 0)
	exit_with_error($output);
unlink("$backup_dir/$instance_name-conf.tar.gz");
unlink("$backup_dir/$instance_name-images.tar.gz");
unlink("$backup_dir/$instance_name-look.tar.gz");
unlink("$backup_dir/$instance_name-database.tar.gz");
unlink("$backup_dir/$instance_name-database.sql");

?>
