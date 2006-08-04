<?php

if (!is_array($GLOBALS['argv'])) {
	echo "Can't read command line arguments\n";
	exit(1);
}

$ETC_DIR = isset($GLOBALS['argv'][1]) ? trim($GLOBALS['argv'][1]) : "";
$archive_file = isset($GLOBALS['argv'][2]) ? trim($GLOBALS['argv'][2]) : "";
$options = getopt("t:ef");

echo "\nCampsite Restore Utility\n";
echo "------------------------\n";

$usage =
"  Usage:
  campsite-restore-instance <backup_file>  [-t <destination_instance_name>] [-e] [-f]

  This script will replace an existing instance with the one in the
  backup file.  You must run this script from a directory that you
  have write access to because this script needs to create a temporary
  directory.  Note that your backup database and files will automatically
  be upgraded if they are older than the currently installed version
  of Campsite.

  Note: For multiple installations of Campsite on a single server, you
  must run this script from the installation directory where you want to
  restore the instance.  For example, if you have installed Campsite
  in two locations: /usr/local/foo and /usr/local/bar, and you want to
  restore an instance in the 'foo' installation, you must run
  /usr/local/foo/bin/campsite-restore-instance,
  and NOT /usr/local/bar/bin/campsite-restore-instance.

  Parameters:
    <backup_file>
        The tarball created by the 'campsite-backup-instance' script.
        Give the full or relative path to the file.

    [-t <destination_instance_name>]
        If this is specified, the script will use the instance name
        specified instead of the one specified in the backup file.
        This is useful for site-to-site transfer of a website, that is,
        moving your website from one server to another.

    [-e]
        Use the existing configuration files instead of the ones in the
        backup file.  In other words, the existing config files in the
        destination instance will not be replaced.

    [-f]
        Dont prompt, assume 'yes' to questions.

  See also:
      campsite-backup-instance
      campsite-create-instance
      campsite-remove-instance

";

$useExistingConfig = isset($options['e']);
$doPrompt = !isset($options['f']);
$destInstanceSpecified = isset($options['t']);
$destInstanceName = isset($options['t']) ? $options['t'] : "";

if (empty($ETC_DIR) || empty($archive_file) || ($destInstanceSpecified && $destInstanceName == "")) {
	echo $usage;
	exit(1);
}

if (!file_exists($archive_file)) {
	echo "ERROR!  The backup file you specified does not exist.\n\n";
	echo $usage;
	exit(1);
}

require_once("cli_script_lib.php");

if (!camp_is_readable("$ETC_DIR/install_conf.php")) {
	exit(1);
}

// include install configuration file
require_once("$ETC_DIR/install_conf.php");

if (!is_writable(getcwd())) {
	echo "You do not have permissions to the currect directory.";
	exit(1);
}

$adviceOnError = "Please run this script as 'root' or as '" . $Campsite['APACHE_USER'] . "'.";

//
// Get the name of the directory that will be untarred.
//
echo " * Initializing...\n";
$archiveExtension = pathinfo($archive_file, PATHINFO_EXTENSION);
if ($archiveExtension == "gz") {
	$tarGzOption = "z";
} else {
	$tarGzOption = "";
}
$isNewBackupFormat = true;
$cmd = "tar tf$tarGzOption " . escapeshellarg($archive_file)." |grep sql";
exec($cmd, $output);
if (count($output) == 0) {
	$isNewBackupFormat = false;
	$cmd = "tar tf$tarGzOption " . escapeshellarg($archive_file);
	exec($cmd, $output);
	if (count($output) == 0) {
		camp_exit_with_error("Invalid backup file.");
	}
	echo "   * Old backup file detected (pre-2.6.0)\n";
}
$output = array_pop($output);
if ($isNewBackupFormat) {
	$parts = split("/", $output);
	$tempDirName = array_shift($parts);
} else {
	$tempDirName = "backup-temp-".date("Y-m-d-H-i-s");
}
echo "   * Temp directory: $tempDirName\n";
echo "   * Initialization done.\n";

if (file_exists($tempDirName)) {
	echo "This script needs to create a temporary directory named '$tempDirName',\n";
	echo "but the directory already exists.  Please delete the existing directory or move it out of the way.\n\n";
	exit(1);
}

//
// Untar the backup
//
echo " * Extracting files into temp directory...";
if ($isNewBackupFormat) {
	$cmd = "tar xf$tarGzOption " . escapeshellarg($archive_file);
	camp_exec_command($cmd, $adviceOnError);
} else {
	camp_create_dir($tempDirName);
	$currentDir = getcwd();
	chdir($tempDirName);
	$cmd = "tar xf$tarGzOption " . escapeshellarg("../".$archive_file) . " &> /dev/null";
	camp_exec_command($cmd, $adviceOnError);
	chdir($currentDir);
}

if (!file_exists($tempDirName)) {
	echo "ERROR! Could not extract archive.\n\n";
	exit(1);
}

//
// Get the original instance name from the extracted files.
//
$database_dump_file = glob("$tempDirName/*-database*");
if (sizeof($database_dump_file) != 1) {
	camp_exit_with_error("Archive $archive_file is invalid.");
}
$db_file_name = basename($database_dump_file[0]);
$origInstanceName = substr($db_file_name, 0, strrpos($db_file_name, '-'));

if (!$destInstanceSpecified) {
	// Use the config parameters specified in the backup file.
	$destInstanceName = $origInstanceName;
}

// If old backup format, extract the tar files inside the tar file.
if (!$isNewBackupFormat) {
	$packages = glob("$tempDirName/$origInstanceName-*.tar.gz");
	foreach ($packages as $index => $package) {
		$package_name = basename($package);
		if ($package == "") {
			continue;
		}
		$currentDir = getcwd();
		chdir($tempDirName);
		camp_exec_command("tar xzf " . escapeshellarg($package_name), $adviceOnError);
		chdir($currentDir);
	}
}
echo "done.\n";

echo " * Backup instance name is '$origInstanceName'.\n";
echo " * Destination instance name (to be replaced) is '$destInstanceName'.\n";

require_once($Campsite['WWW_COMMON_DIR']."/html/campsite_version.php");

if ($useExistingConfig) {
	$includeFile = "$ETC_DIR/$destInstanceName/database_conf.php";
} else {
	$includeFile = "$tempDirName/$origInstanceName/database_conf.php";
}

// Check if the instance exists.
if (!file_exists($includeFile)) {
    echo "\nThe destination instance ('$destInstanceName') does not exist.\n";
    echo "You can create an instance using 'campsite-create-instance'.\n\n";
	echo " * Cleaning up...";
	camp_remove_dir($tempDirName);
	echo "done.\n\n";
	exit(1);
}

// Check if instance files are readable.
if (!camp_is_readable($includeFile)) {
	echo " * Cleaning up...";
	camp_remove_dir($tempDirName);
	echo "done.\n\n";
	exit(1);
}
require_once($includeFile);

// Create the instance if it doesnt exist.
echo " * Creating destination instance (if necessary)...";
$bin_dir = $Campsite['BIN_DIR'];
$cmd = "$bin_dir/campsite-create-instance --db_name $destInstanceName --no_database";
@exec($cmd, $output, $result);
if ($result != 0) {
	echo "\n\nERROR: ";
	echo "Unable to create instance '$destInstanceName'.\n";
	echo "$adviceOnError\n\n";
	echo " * Cleaning up...";
	camp_remove_dir($tempDirName);
	echo "done.\n\n";
	exit(1);
}

echo "done.\n";

//
// Restore the backup files
//
$lookDestDir = $Campsite['WWW_DIR']."/".$destInstanceName."/html/look";
$lookSrcDir = "$tempDirName/look";
$imagesDestDir = $Campsite['WWW_DIR']."/".$destInstanceName."/html/images";
$imagesSrcDir = "$tempDirName/images";
$filesDestDir = $Campsite['WWW_DIR']."/".$destInstanceName."/html/files";
$fileSrcDir = "$tempDirName/files";
$configDestDir = "$ETC_DIR/$destInstanceName";
$configSrcDir = "$tempDirName/$origInstanceName";

$destDirs = array($lookDestDir, $imagesDestDir, $filesDestDir);
if (!$useExistingConfig) {
	array_push($destDirs, $configDestDir);
}

foreach ($destDirs as $dir) {
	if (!file_exists($dir)) {
		echo "ERROR! Directory $dir does not exist.  Is Campsite installed?\n\n";
		exit(1);
	}
	if (!is_writable($dir)) {
		echo "ERROR! Directory $dir is not writable by this script.\n\n";
		exit(1);
	}
}

if ($doPrompt) {
	// Clear out all files currently residing in these directories
	echo "\n All files in the following directories will be deleted.\n";
	echo " (The backup files will be copied to these locations)\n";
	foreach ($destDirs as $dir) {
		echo "    ".$dir."\n";
	}
	$answer = "dummy_value";
	while (!in_array($answer, array('y','n', ''))) {
		echo " Are you sure you want to continue? (y/N) ";
		$answer = camp_readline();
		$answer = strtolower(trim($answer));
	}
	if ($answer == 'n' || $answer == '') {
		echo "\n Restore cancelled.\n";
		echo " * Cleaning up...";
		camp_remove_dir($tempDirName);
		echo "done.\n\n";
		exit(0);
	}
}

//
// Remove all existing files...
//
foreach ($destDirs as $dir) {
	echo " * Removing files in $dir...";
	camp_remove_dir($dir."/*");
	echo "done.\n";
}

//
// Restore files from backup...
//
echo " * Restoring templates...";
camp_copy_files($lookSrcDir."/*", $lookDestDir);
echo "done.\n";

echo " * Restoring images...";
camp_copy_files($imagesSrcDir."/*", $imagesDestDir);
echo "done.\n";

echo " * Restoring file attachments...";
camp_copy_files($fileSrcDir."/*", $filesDestDir);
echo "done.\n";

if (!$useExistingConfig) {
	echo " * Restoring configuration...";
	camp_copy_files($configSrcDir."/*", $configDestDir);
	echo "done.\n";
}

//
// Restore the database
//
echo " * Restoring the database...";
if (($res = camp_connect_to_database()) != 0) {
	camp_exit_with_error($res);
}
if (camp_database_exists($destInstanceName)) {
	camp_clean_database($destInstanceName);
} else {
	if (!mysql_query("CREATE DATABASE $destInstanceName")) {
		camp_exit_with_error("Can't create database $destInstanceName");
	}
}

$sqlFile = "$tempDirName/$origInstanceName-database.sql";
$cmd = "mysql -u " . $Campsite['DATABASE_USER'] . " --host="
	. $Campsite['DATABASE_SERVER_ADDRESS'] . " --port="
	. $Campsite['DATABASE_SERVER_PORT'];
if ($Campsite['DATABASE_PASSWORD'] != "") {
	$cmd .= " --password=\"" . $Campsite['DATABASE_PASSWORD'] . "\"";
}
$cmd .= " $destInstanceName < $sqlFile";
camp_exec_command($cmd, "Unable to import database. (Command: $cmd)");
echo "done.\n";

// Call campsite-create-instance to upgrade the database.
echo " * Upgrading (if necessary)...";
$bin_dir = $Campsite['BIN_DIR'];
camp_exec_command("$bin_dir/campsite-create-instance --db_name $destInstanceName",
			 	  "Upgrade failed.");
echo "done.\n";

//
// Remove the temp dir.
//
echo " * Cleaning up...";
camp_remove_dir($tempDirName);
echo "done.\n\n";

if ($useExistingConfig || $destInstanceSpecified) {
	echo
"Note: If you are doing a site-to-site transfer you may have to fix the
aliases in your publications before the frontend will work.  An alias
is the base URL for your publication.  To change an alias, login to the
administration interface and go to the publication configure screen.\n\n";
}

exit;

?>