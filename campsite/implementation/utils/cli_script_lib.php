<?php

/**
 * Escape special characters that are going to be passed to the shell
 * in a command line argument.
 *
 * @param string $p_arg
 * @return string
 */
function camp_escape_shell_arg($p_arg)
{
	$arg = str_replace('\\', '\\\\', $p_arg);
	$arg = str_replace(' ', '\ ', $arg);
	$arg = str_replace('`', '\`', $arg);
	$arg = str_replace('!', '\!', $arg);
	$arg = str_replace('@', '\@', $arg);
	$arg = str_replace('$', '\$', $arg);
	$arg = str_replace('%', '%%', $arg);
	$arg = str_replace('^', '\^', $arg);
	$arg = str_replace('&', '\&', $arg);
	$arg = str_replace('*', '\*', $arg);
	$arg = str_replace('(', '\(', $arg);
	$arg = str_replace(')', '\)', $arg);
	$arg = str_replace('=', '\=', $arg);
	$arg = str_replace('{', '\{', $arg);
	$arg = str_replace('}', '\}', $arg);
	$arg = str_replace('[', '\[', $arg);
	$arg = str_replace(']', '\]', $arg);
	$arg = str_replace(':', '\:', $arg);
	$arg = str_replace(';', '\;', $arg);
	$arg = str_replace('"', '\"', $arg);
	$arg = str_replace('\'', '\\\'', $arg);
	$arg = str_replace('<', '\<', $arg);
	$arg = str_replace('>', '\>', $arg);
	$arg = str_replace(',', '\,', $arg);
	$arg = str_replace('?', '\?', $arg);
	$arg = str_replace('|', '\|', $arg);
	return $arg;
} // fn camp_escape_shell_arg


/**
 * Execute a command in the shell.
 *
 * @param string $p_cmd
 * @param string $p_errMsg
 * @param boolean $p_printOutput
 */
function camp_exec_command($p_cmd, $p_errMsg = "", $p_printOutput = true)
{
	@exec($p_cmd, $output, $result);
	if ($result != 0) {
		if (!$p_printOutput) {
			$output = array();
		}
		if ($p_errMsg != "") {
			$my_output[] = $p_errMsg;
			$output = array_merge($my_output, $output);
		}
		camp_exit_with_error($output);
	}
} // fn camp_exec_command


/**
 * Create a directory.  If this fails, print out the given error
 * message or a default one.
 *
 * @param string $p_dirName
 * @param string $p_msg
 * @return 0
 */
function camp_create_dir($p_dirName, $p_msg = "")
{
	if ($p_msg == "") {
		$p_msg = "Unable to create directory $p_dirName.";
	}
	if (!is_dir($p_dirName) && !mkdir($p_dirName)) {
		camp_exit_with_error($p_msg);
	}
	return 0;
} // fn camp_create_dir


/**
 * Rename the given file so it has a time stamp embedded in its name.
 * If there is an error, a message will be placed in the $p_output
 * variable.
 *
 * @param string $p_filePath
 * @param string $p_output
 * @return boolean
 */
function camp_backup_file($p_filePath, &$p_output)
{
	if (!is_file($p_filePath)) {
		$p_output = "File $p_filePath does not exist.";
		return 1;
	}
	$dir_name = dirname($p_filePath);
	if (!($file_stat = @stat($p_filePath))) {
		$p_output = "Unable to read file $p_filePath data.";
		return 1;
	}
	$file_name = basename($p_filePath);
	$extension = pathinfo($p_filePath, PATHINFO_EXTENSION);
	$change_time = strftime("%Y-%m-%d-%H", $file_stat['ctime']);
	$new_name = "$base_name-$change_time$extension";

	if (is_file("$dir_name/$new_name")) {
		return 0;
	}

	if (!rename($p_filePath, "$dir_name/$new_name")) {
		$p_output = "Unable to rename file $p_filePath";
		return 1;
	}
	return 0;
} // fn camp_backup_file


/**
 * Tar the given source file into the given destination directory and
 * give it the name $p_fileName.  If there is an error, return an error
 * message in the $p_output variable.
 *
 * @param string $p_sourceFile
 * @param string $p_destDir
 * @param string $p_fileName
 * @param string $p_output
 * @return int
 */
function camp_archive_file($p_sourceFile, $p_destDir, $p_fileName, &$p_output)
{
	$source_dir = dirname($p_sourceFile);
	$source_file_name = basename($p_sourceFile);
	$cmd = "pushd $source_dir > /dev/null && tar czf "
		. escapeshellarg("$dest_dir/$p_fileName.tar.gz")
		. " " . escapeshellarg($source_file_name) . " &> /dev/null && popd > /dev/null";
	@exec($cmd, $p_output, $result);
	return $result;
} // fn camp_archive_file


/**
 * Dump the given database into the file $p_destFile.  If there is an
 * error, it will be returned in $p_output.
 *
 * @param string $p_dbName
 * @param string $p_destFile
 * @param string $p_output
 * @return int
 */
function camp_backup_database($p_dbName, $p_destFile, &$p_output)
{
	global $Campsite;

	$user = $Campsite['DATABASE_USER'];
	$password = $Campsite['DATABASE_PASSWORD'];
	$cmd = "mysqldump --add-drop-table -c -e -Q --user=$user --host="
		. $Campsite['DATABASE_SERVER_ADDRESS']
		. " --port=" . $Campsite['DATABASE_SERVER_PORT'];
	if ($password != "") {
		$cmd .= " --password=$password";
	}
	$cmd .= " $p_dbName > $p_destFile";
	@exec($cmd, $p_output, $result);
	return $result;
} // fn camp_backup_database


/**
 * Print out the given message and exit the program with an error code.
 *
 * @param string $p_errorStr
 * @return void
 */
function camp_exit_with_error($p_errorStr)
{
	if (is_array($p_errorStr)) {
		$p_errorStr = implode("\n", $p_errorStr);
	}
	echo "ERROR!\n$p_errorStr\n";
	camp_clean_files();
	exit(1);
} // fn camp_exit_with_error


/**
 * Delete all the backup files for the current instance.
 *
 */
function camp_clean_files()
{
	global $Campsite;

	if (isset($Campsite['CAMPSITE_DIR']) && isset($Campsite['DATABASE_NAME'])) {
		$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/" . $Campsite['DATABASE_NAME'];
		camp_exec_command("rm -f $backup_dir/*.tar.gz");
	}
} // fn camp_clean_files


/**
 * Connect to the MySQL database.
 *
 * @param string $p_dbName
 * @return mixed
 * 		Zero on success, error message string on error.
 */
function camp_connect_to_database($p_dbName = "")
{
	global $Campsite;

	$db_user = $Campsite['DATABASE_USER'];
	$db_password = $Campsite['DATABASE_PASSWORD'];
	$res = mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'] . ":"
		. $Campsite['DATABASE_SERVER_PORT'], $db_user, $db_password);
	if (!$res) {
		return "Unable to connect to database server";
	}

	if ($p_dbName != "" && !mysql_select_db($p_dbName)) {
		return "Unable to select database $p_dbName";
	}

	return 0;
} // fn camp_connect_to_database


/**
 * Return TRUE if the database contains no data.
 *
 * @param string $db_name
 * @return boolean
 */
function camp_is_empty_database($p_dbName)
{
	if (!mysql_select_db($p_dbName)) {
		return "camp_is_empty_database: can't select the database";
	}
	if (!($res = mysql_query("show tables"))) {
		return "camp_is_empty_database: can't read tables";
	}
	return (mysql_num_rows($res) == 0);
} // fn camp_is_empty_database


/**
 * Drop all tables in the given database.
 *
 * @param string $p_dbName
 * @return mixed
 * 		Zero on success, and an error message on error.
 */
function camp_clean_database($p_dbName)
{
	if (!mysql_select_db($p_dbName)) {
		return "camp_clean_database: can't select the database";
	}
	if (!($res = mysql_query("show tables"))) {
		return "Can not clean the database: can't read tables";
	}
	while ($row = mysql_fetch_row($res)) {
		$table_name = $row[0];
		mysql_query("drop table `" . mysql_escape_string($table_name) . "`");
	}
	return 0;
} // fn camp_clean_database


/**
 * Return TRUE if the database exists.
 *
 * @param string $p_dbName
 * @return boolean
 */
function camp_database_exists($p_dbName)
{
	$res = mysql_list_dbs();
	while ($row = mysql_fetch_object($res)) {
		if ($row->Database == $p_dbName) {
			return true;
		}
	}
	return false;
} // fn camp_database_exists

?>