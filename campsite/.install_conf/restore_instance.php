<?php

$etc_dir = trim($GLOBALS['argv'][1]);
$type = trim($GLOBALS['argv'][2]);
if ($type == "-a")
	$archive_file = trim($GLOBALS['argv'][3]);
if ($type == "-i")
	$instance_name = trim($GLOBALS['argv'][3]);

if ($etc_dir == "" || $type == "" || ($type == "-a" && $archive_file == "")
	|| ($type == "-i" && $instance_name == "")) {
	echo "Invalid parameters received; usage:\n"
		. "\tphp restore_backup.php [etc_dir] [type] [archive_name/instance_name]\n"
		. "\twhere [etc_dir] = campsite etc directory\n"
		. "\t[type] = restore type: -a from archive file, -i instance name\n"
		. "\t[archive_name] = archive file name, use with -a type\n"
		. "\t[instance_name] = instance to be restored, use with -i type\n"
		. "\t\tThe archive file must be in backup/[instance_name] directory.\n";
	exit(1);
}

// include install configuration file
require_once("$etc_dir/install_conf.php");
require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");

if ($type == "-a") {
	// copy the archive to a temporary directory to read the instance name
	// create temporary directory
	$tmp_dir = $Campsite['CAMPSITE_DIR'] . "/backup/.tmp";
	create_dir($tmp_dir, "Unable to create temporary directory.");
	exec_command("rm -f $tmp_dir/*");

	// unarchive the backup
	$cmd = "pushd " . escapeshellarg($tmp_dir) . " > /dev/null && tar xf "
		. escapeshellarg($archive_file) . " && popd > /dev/null";
	exec_command($cmd);

	// read instance name from database package
	$db_file = glob("$tmp_dir/*-database.tar.gz");
	if (sizeof($db_file) != 1)
		exit_with_error("Archive $archive_file is invalid.");
	$db_file_name = file_name($db_file[0]);
	$instance_name = substr($db_file_name, 0, strrpos($db_file_name, '-'));

	// move files to instance backup directory and remove temporary directory
	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name";
	create_dir($backup_dir, "Unable to create instance backup directory.");
	exec_command("mv -f $tmp_dir/* " . escapeshellarg($backup_dir));
	exec_command("rmdir " . escapeshellarg($tmp_dir));
}

if ($type == "-i") {
	// look for the archive in backup directory
	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name";
	$archive_file = "$backup_dir/$instance_name-bak.tar";
	if (!is_file($archive_file))
		exit_with_error("Archive file for instance $instance_name does not exist.");

	// unarchive the backup
	$cmd = "pushd " . escapeshellarg($backup_dir) . " > /dev/null && tar xf "
		. escapeshellarg($archive_file) . " && popd > /dev/null";
	exec_command($cmd);
}

// backup old database dump if exists
//if (is_file("$backup_dir/$instance_name-database.sql")) {}

// extract packages
$html_dir = $Campsite['WWW_DIR'] . "/$instance_name/html";
$packages = glob("$backup_dir/$instance_name-*.tar.gz");
foreach ($packages as $index=>$package) {
	$package_name = file_name($package);
	switch ($package_name) {
	case "$instance_name-database.tar.gz": $dest_dir = $backup_dir; break;
	case "$instance_name-conf.tar.gz": $dest_dir = $etc_dir; break;
	default: $dest_dir = $html_dir; break;
	}
	$cmd = "pushd " . escapeshellarg($dest_dir) . " && tar xzf "
		. escapeshellarg($package) . "  && popd > /dev/null";
	exec_command($cmd);
}

// remove packages
exec_command("rm -f $backup_dir/*.tar.gz");

?>
