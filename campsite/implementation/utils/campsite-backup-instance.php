<?php

if (!is_array($GLOBALS['argv'])) {
	echo "\n";
	echo "Can't read command line arguments\n";
	echo "\n";
	exit(1);
}

$processUserId = posix_geteuid();
if ($processUserId != 0) {
	echo "\n";
	echo "You must run this script as root.\n";
	echo "\n";
	exit(1);
}

$etc_dir = isset($GLOBALS['argv'][1]) ? trim($GLOBALS['argv'][1]) : "";
$instance_name = isset($GLOBALS['argv'][2]) ? trim($GLOBALS['argv'][2]) : "";

if ($etc_dir == "") {
	die("Please supply the configuration directory as the first argument.\n");
}
if ($instance_name == "") {
	die("Please supply the instance name as the second argument.\n");
}
$silent = false;
$default_dir = false;
for ($i = 3; ; $i++) {
	if (!isset($GLOBALS['argv'][$i])) {
		break;
	}
	$arg = trim($GLOBALS['argv'][$i]);
	$silent = $arg == "--silent";
	$default_dir = $arg == "--default_dir";
}

// include install_conf.php file
require_once("$etc_dir/install_conf.php");
require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");
if (!is_dir("$etc_dir/$instance_name")) {
	if (!$silent) {
		echo "\n";
		echo "Instance '$instance_name' does not exist!\n";
		echo "\n";
	}
	exit(1);
}
if (!is_file("$etc_dir/$instance_name/database_conf.php")) {
	if (!$silent) {
		echo "\n";
		echo "Database configuration file for instance '$instance_name' is missing!\n";
		echo "\n";
	}
	exit(1);
}
require_once("$etc_dir/$instance_name/database_conf.php");
$html_dir = $Campsite['WWW_DIR'] . "/$instance_name/html";
if ($default_dir) {
	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name";
	exec_command("mkdir -p " . escape_shell_arg($backup_dir), !$silent);
} else {
	$backup_dir = getcwd();
}

// backup look (templates) directory
if (archive_file("$html_dir/look", $backup_dir, "$instance_name-look", $output) != 0) {
	exit_with_error($silent ? "" : $output);
}

// backup images directory
if (archive_file("$html_dir/images", $backup_dir, "$instance_name-images", $output) != 0) {
	exit_with_error($silent ? "" : $output);
}

// backup files directory
if (archive_file("$html_dir/files", $backup_dir, "$instance_name-files", $output) != 0) {
	exit_with_error($silent ? "" : $output);
}

// backup configuration directory
if (archive_file("$etc_dir/$instance_name", $backup_dir, "$instance_name-conf", $output) != 0) {
	exit_with_error($silent ? "" : $output);
}

// backup the database
$db_file_name = "$backup_dir/$instance_name-database.sql";
if (is_file($db_file_name) && (backup_file($db_file_name, $output) != 0)) {
	exit_with_error($silent ? "" : $output);
}
if (backup_database($instance_name, $db_file_name, $output) != 0) {
	exit_with_error($silent ? "" : $output);
}
if (archive_file($db_file_name, $backup_dir, "$instance_name-database", $output) != 0) {
	exit_with_error($silent ? "" : $output);
}

// create the final archive
$cmd = "pushd $backup_dir > /dev/null && tar cf "
	. escapeshellarg("$instance_name-bak.tar")
	. " *.tar.gz && popd > /dev/null";
exec_command($cmd, !$silent);
unlink("$backup_dir/$instance_name-conf.tar.gz");
unlink("$backup_dir/$instance_name-files.tar.gz");
unlink("$backup_dir/$instance_name-images.tar.gz");
unlink("$backup_dir/$instance_name-look.tar.gz");
unlink("$backup_dir/$instance_name-database.tar.gz");
unlink("$backup_dir/$instance_name-database.sql");

if ($silent) {
	exit(0);
}

echo "Backup archive file '$instance_name-bak.tar' was created in the directory:\n";
echo "$backup_dir\n";

?>