<?php

if (!is_array($GLOBALS['argv'])) {
	echo "\n";
	echo "Can't read command line arguments\n";
	echo "\n";
	exit(1);
}

$CAMPSITE_DIR = isset($GLOBALS['argv'][1]) ? trim($GLOBALS['argv'][1]) : "";
$ETC_DIR = isset($GLOBALS['argv'][2]) ? trim($GLOBALS['argv'][2]) : "";
$instance_name = isset($GLOBALS['argv'][3]) ? trim($GLOBALS['argv'][3]) : "";

$silent = false;
$default_dir = false;
for ($i = 0; ; $i++) {
	if (!isset($GLOBALS['argv'][$i])) {
		break;
	}
	$arg = trim($GLOBALS['argv'][$i]);
	$silent |= ($arg == "--silent");
	$default_dir |= ($arg == "--default_dir");
}

if (!$silent) {
	echo "\n";
	echo "Campsite Backup Utility\n";
	echo "-----------------------\n";
}

$usage =
"  Usage:
  campsite-backup-instance <instance_name> [--silent] [--default_dir]

  This script will backup all of your website data needed to re-create
  your site from scratch, except for the Campsite software itself.
  Later, if you want to restore from a backup file, use the script
  'campsite-restore-instance'.

  Parameters:
    <instance_name>
       The name of the instance you want to back up.
       This parameter is required.

    --silent
        Don't display any messages on success.

    --default_dir:
        Save the backup archive in the backup default directory:
            $CAMPSITE_DIR/backup/<instance_name>/
        By default the backup file is saved in the current directory.

  See also:
      campsite-restore-instance
      campsite-create-instance
      campsite-remove-instance

";

if (empty($ETC_DIR) || empty($instance_name)) {
	echo $usage;
	exit(1);
}

require_once("cli_script_lib.php");

if (!camp_is_readable("$ETC_DIR/install_conf.php")) {
	exit(1);
}

// include install_conf.php file
require_once("$ETC_DIR/install_conf.php");
require_once($Campsite['WWW_DIR']."/".$instance_name."/html/campsite_version.php");
if (!is_dir("$ETC_DIR/$instance_name")) {
	echo "\n";
	echo "Instance '$instance_name' does not exist!\n";
	echo "\n";
	exit(1);
}
if (!is_file("$ETC_DIR/$instance_name/database_conf.php")) {
	echo "\n";
	echo "Database configuration file for instance '$instance_name' is missing!\n";
	echo "\n";
	exit(1);
}
require_once("$ETC_DIR/$instance_name/database_conf.php");
$html_dir = $Campsite['WWW_DIR'] . "/$instance_name/html";

$timestampStr = date("Y-m-d-H-i-s");
$backupDirName = "backup-".$instance_name."-".$timestampStr;

if ($default_dir) {
	$adviceOnError = "Please run this script as 'root' or as '" . $Campsite['APACHE_USER'] . "'.";
	$backupDirFullPath = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name/$backupDirName";
	camp_exec_command("mkdir -p " . camp_escape_shell_arg($backupDirFullPath),
				 "Unable to create the default backup directory.\n$adviceOnError", !$silent);
	$cmd = "chown " . escapeshellarg($Campsite['APACHE_USER']) . ":"
			. escapeshellarg($Campsite['APACHE_GROUP']) . " " . escapeshellarg($backupDirFullPath);
	camp_exec_command($cmd,
				 "Unable to set the ownership of the default backup directory.\n$adviceOnError",
				 !$silent);
} else {
	$adviceOnError = "You may not have the right to write to the current directory.\n"
					. "Please set the current directory to a location where you have\n"
					. "the right to write.";
	$backupDirFullPath = getcwd()."/".$backupDirName;
}

if (!$silent) {
	echo "Backup script version: ".$Campsite["VERSION"]."\n";
	echo "Backing up campsite instance: $instance_name\n";
}

if (!file_exists($backupDirFullPath)) {
	camp_create_dir($backupDirFullPath, $adviceOnError);
}

$tmpVersion = split(" ", $Campsite["VERSION"]);
$tmpVersion = $tmpVersion[0];
$cmd = "touch $backupDirFullPath/BACKUP-VERSION-$tmpVersion";
camp_exec_command($cmd, "Couldn't write VERSION file.");

// backup the database
if (!$silent) {
	echo " * Backing up the database...";
}
$db_file_name = "$backupDirFullPath/$instance_name-database.sql";
if (is_file($db_file_name) && (camp_backup_file($db_file_name, $output) != 0)) {
	camp_exit_with_error($silent ? "" : array("Unable to create temporary archive.", $adviceOnError));
}
if (camp_backup_database($instance_name, $db_file_name, $output) != 0) {
	camp_exit_with_error($silent ? "" : array("Unable to create temporary archive.", $adviceOnError));
}
if (!$silent) {
	echo "done.\n";
}

if (!$silent) {
	echo " * Backing up the templates...";
}
camp_copy_files("$html_dir/look", $backupDirFullPath);
if (!$silent) {
	echo "done.\n";
}

if (!$silent) {
	echo " * Backing up images...";
}
camp_copy_files("$html_dir/images", $backupDirFullPath);
if (!$silent) {
	echo "done.\n";
}

if (!$silent) {
	echo " * Backing up file attachments...";
}
camp_copy_files("$html_dir/files", $backupDirFullPath);
if (!$silent) {
	echo "done.\n";
}

if (!$silent) {
	echo " * Backing up configuration files...";
}
camp_copy_files("$ETC_DIR/$instance_name", $backupDirFullPath);
if (!$silent) {
	echo "done.\n";
}

if (!$silent) {
	echo " * Creating tarball...";
}
if (camp_archive_file($backupDirFullPath, dirname($backupDirFullPath), $backupDirName, $output) != 0) {
	camp_exit_with_error($silent ? "" : array("Unable to create temporary archive.", $adviceOnError));
}
if ($default_dir) {
	$cmd = "chown " . escapeshellarg($Campsite['APACHE_USER']) . ":"
			. escapeshellarg($Campsite['APACHE_GROUP']) . " "
			. escapeshellarg("$backupDirFullPath.tar.gz");
	camp_exec_command($cmd, $adviceOnError, !$silent);
}
if (!$silent) {
	echo "done.\n";
}

if (!$silent) {
	echo " * Cleaning up...";
}
camp_remove_dir($backupDirFullPath, "Unable to remove temporary directory $backupDirFullPath");
if (!$silent) {
	echo "done.\n";
}

if (!$silent) {
	echo "\nBackup saved to file:\n  $backupDirFullPath.tar.gz\n\n";
}
exit(0);

?>