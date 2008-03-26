<?php

function camp_is_readable($p_fileName)
{
    if (!is_readable($p_fileName)) {
        echo "\nThis script requires access to the file $p_fileName.\n";
        echo "Please run this script as a user with appropriate privileges.\n";
        echo "Most often this user is 'root'.\n\n";
        return false;
    }
    return true;
} // fn camp_is_readable


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
    $p_cmd .= " 2> /dev/null";
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
 * So that it also works on windows in the future...
 *
 * @return string
 */
function camp_readline()
{
    $fp = fopen("php://stdin", "r");
    $in = fgets($fp, 4094); // Maximum windows buffer size
    fclose ($fp);
    return $in;
} // fn camp_readline


/**
 * Create a directory.  If this fails, print out the given error
 * message or a default one.
 *
 * @param string $p_dirName
 * @param string $p_msg
 * @return void
 */
function camp_create_dir($p_dirName, $p_msg = "")
{
    if ($p_msg == "") {
        $p_msg = "Unable to create directory $p_dirName.";
    }
    if (!is_dir($p_dirName) && !mkdir($p_dirName)) {
        camp_exit_with_error($p_msg);
    }
} // fn camp_create_dir


/**
 * @return boolean
 */
function camp_is_empty_dir($p_dirName)
{
    $cnt = 0;
    if(is_dir($p_dirName) ){
        $files = opendir($p_dirName);
        while ($file = @readdir($files)) {
            $cnt++;
            if ($cnt > 2) {
                return false;
            }
        }
        return true;
    }

    return false;
} // fn camp_is_empty_dir


/**
 *
 */
function camp_read_files($p_startDir = '.')
{
    $files = array();
    if (is_dir($p_startDir)) {
        $fh = opendir($p_startDir);
        while(($file = readdir($fh)) !== false) {
            // loop through the files, skipping . and .., and
            // recursing if necessary
            if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) {
                continue;
            }
            $filePath = $p_startDir . '/' . $file;
            if (is_dir($filePath)) {
                $files = array_merge($files, camp_read_files($filePath));
            } else {
                array_push($files, $filePath);
            }
        }
        closedir($fh);
    } else {
        // false if the function was called with an invalid
        // non-directory argument
        $files = false;
    }

    return $files;
} // fn camp_read_files


/**
 * Remove the specified directory and everything underneath it.
 *
 * @param string $p_dirName
 * @param string $p_msg
 * @return void
 */
function camp_remove_dir($p_dirName, $p_msg = "")
{
    if ($p_dirName == "" || $p_dirName == "/") {
        camp_exit_with_error("ERROR! camp_remove_dir: Bad directory name.");
    }
    if (empty($p_msg)) {
        $p_msg = "Unable to remove directory $p_dirName";
    }
    $command = "rm -rf $p_dirName";
    camp_exec_command($command, $p_msg);
} // fn camp_remove_dir


/**
 * Recursively copy the given directory or file to the given
 * destination.
 *
 * @param string $p_src
 * @param string $p_dest
 * @param string $p_msg
 * @return void
 */
function camp_copy_files($p_src, $p_dest, $p_msg = "")
{
    if ($p_msg == "") {
        $p_msg = "Unable to copy file/dir $p_src to $p_dest.";
    }
    $command = "cp -R $p_src $p_dest";
    camp_exec_command($command, $p_msg);
} // fn camp_copy_files


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
 * Tar the given source file/dir into the given destination directory and
 * give it the name $p_fileName.  If there is an error, return an error
 * message in the $p_output variable.
 *
 * @param mixed $p_sourceFile
 * @param string $p_destDir
 * @param string $p_fileName
 * @param string $p_output
 * @return int
 */
function camp_archive_file($p_sourceFile, $p_destDir, $p_fileName, &$p_output)
{
    $fileStr = escapeshellarg(basename($p_sourceFile));
    $source_dir = dirname($p_sourceFile);
    $currentDir = getcwd();
    chdir($source_dir);
    $cmd = "tar czf " . escapeshellarg("$p_destDir/$p_fileName.tar.gz") . " $fileStr &> /dev/null";
    @exec($cmd, $p_output, $result);
    chdir($currentDir);
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
    echo "\nERROR!\n$p_errorStr\n";
    exit(1);
} // fn camp_exit_with_error


/**
 * Connect to the MySQL database.
 *
 * @param string $p_dbName
 * @return void
 */
function camp_connect_to_database($p_dbName = "")
{
    global $Campsite;

    $db_user = $Campsite['DATABASE_USER'];
    $db_password = $Campsite['DATABASE_PASSWORD'];
    $res = mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'] . ":"
        . $Campsite['DATABASE_SERVER_PORT'], $db_user, $db_password);
    if (!$res) {
        camp_exit_with_error("Unable to connect to database server");
    }

    if ($p_dbName != "" && !mysql_select_db($p_dbName)) {
        camp_exit_with_error("Unable to select database $p_dbName");
    }
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
        camp_exit_with_error("camp_is_empty_database: can't select the database");
    }
    if (!($res = mysql_query("show tables"))) {
        camp_exit_with_error("camp_is_empty_database: can't read tables");
    }
    return (mysql_num_rows($res) == 0);
} // fn camp_is_empty_database


/**
 * Drop all tables in the given database.
 *
 * @param string $p_dbName
 * @return void
 */
function camp_clean_database($p_dbName)
{
    if (!mysql_select_db($p_dbName)) {
        camp_exit_with_error("camp_clean_database: can't select the database");
    }
    if (!($res = mysql_query("show tables"))) {
        camp_exit_with_error("Can not clean the database: can't read tables");
    }
    while ($row = mysql_fetch_row($res)) {
        $table_name = $row[0];
        mysql_query("drop table `" . mysql_escape_string($table_name) . "`");
    }
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


/**
 * @param string $p_dbName
 */
function camp_upgrade_database($p_dbName)
{
    global $Campsite;

    $campsite_dir = $Campsite['CAMPSITE_DIR'];
    $etc_dir = $Campsite['ETC_DIR'];

    if (!camp_database_exists($p_dbName)) {
        return "Can't upgrade database $p_dbName: it doesn't exist";
    }

    if (!($res = camp_detect_database_version($p_dbName, $old_version)) == 0) {
        return $res;
    }

    $first = true;
    $versions = array("2.0.x", "2.1.x", "2.2.x", "2.3.x", "2.4.x", "2.5.x",
                      "2.6.0", "2.6.1", "2.6.2", "2.6.3", "2.6.4", "2.6.x",
                      "2.7.x");
    foreach ($versions as $index=>$db_version) {
        if ($old_version > $db_version) {
            continue;
        }
        if ($first) {
            echo "\n\t* Upgrading the database from version $db_version...";
        }
        $output = array();

        $upgrade_dir = $campsite_dir . "/database/upgrade/$db_version/";
        $db_conf_file = $etc_dir . '/database_conf.php';
        $install_conf_file = $etc_dir . "/install_conf.php";

        // run upgrade scripts
        $cmd_prefix = "cd " . camp_escape_shell_arg($upgrade_dir)
            . " && mysql --user=" . $Campsite['DATABASE_USER']
            . " --host=" . $Campsite['DATABASE_SERVER_ADDRESS']
            . " --port=" . $Campsite['DATABASE_SERVER_PORT'];
        if ($Campsite['DATABASE_PASSWORD'] != "") {
            $cmd_prefix .= " --password=\"" . $Campsite['DATABASE_PASSWORD'] . "\"";
        }
        $cmd_prefix .= " " . camp_escape_shell_arg($p_dbName) . " < ";
        $sql_scripts = array("tables.sql", "data-required.sql", "data-optional.sql");
        foreach ($sql_scripts as $index=>$script) {
            if (!is_file($upgrade_dir . $script)) {
                continue;
            }
            $cmd = $cmd_prefix . $script . " 2>&1";
            exec($cmd, $output, $res);
            if ($res != 0 && $script != "data-optional.sql") {
                return "$script ($db_version): " . implode("\n", $output);
            }
        }
        if ($first) {
            echo "done.\n";
            $first = false;
        }
    }

    return 0;
} // fn camp_upgrade_database


/**
 * Find out which version is the given database.
 *
 * @param string $p_dbName
 * @param string $version
 *
 * @return mixed
 */
function camp_detect_database_version($p_dbName, &$version)
{
    if (!mysql_select_db($p_dbName)) {
        return "Can't select the database $p_dbName";
    }

    if (!$res = mysql_query("SHOW TABLES")) {
        return "Unable to query the database $p_dbName";
    }

    $version = "2.0.x";
    $row = mysql_fetch_row($res);
    if (in_array($row[0], array("ArticleTopics", "Topics"))) {
        $version = $version < "2.1.x" ? "2.1.x" : $version;
    }
    if (in_array($row[0], array("URLTypes", "TemplateTypes", "Templates", "Aliases",
                                "ArticlePublish", "IssuePublish", "ArticleImages"))) {
        $version = "2.2.x";
        if (!$res2 = mysql_query("DESC Articles PublishDate")) {
            return "Unable to query the database $p_dbName";
        }
        if (mysql_num_rows($res2) > 0) {
            $version = "2.3.x";
        }
        if (!$res2 = mysql_query("SHOW TABLES LIKE 'Attachments'")) {
            return "Unable to query the database $p_dbName";
        }
        if (mysql_num_rows($res2) > 0) {
            $version = "2.4.x";
        }
        if (!$res2 = mysql_query("DESC SubsSections IdLanguage")) {
            return "Unable to query the database $p_dbName";
        }
        if (mysql_num_rows($res2) > 0) {
            $version = "2.5.x";
        }
        if (!$res2 = mysql_query("SHOW TABLES LIKE 'ArticleTypeMetadata'")) {
            return "Unable to query the database $p_dbName";
        }
        if (mysql_num_rows($res2) > 0) {
            $version = "2.6.0";
            if (!$res2 = mysql_query("SHOW COLUMNS FROM ArticleTypeMetadata LIKE 'type_name'")) {
                return "Unable to query the database $p_dbName";
            }
            $row = mysql_fetch_array($res2, MYSQL_ASSOC);
            if (!is_null($row) && strstr($row['Type'], '166') != '') {
                $version = "2.6.1";
            } else {
                return 0;
            }
            if (!$res2 = mysql_query("SHOW COLUMNS FROM phorum_users LIKE 'fk_campsite_user_id'")) {
                return "Unable to query the database $p_dbName";
            }
            if (mysql_num_rows($res2) > 0) {
                $version = "2.6.2";
            } else {
                return 0;
            }
            if (!$res2 = mysql_query("SELECT * FROM Events WHERE Id = 171")) {
                return "Unable to query the database $p_dbName";
            }
            if (mysql_num_rows($res2) > 0) {
                $version = "2.6.3";
            } else {
                return 0;
            }
            $res2 = mysql_query("SELECT * FROM UserConfig "
                                . "WHERE varname = 'ExternalSubscriptionManagement'");
            if (is_resource($res2) && mysql_num_rows($res2) > 0) {
                $version = "2.6.4";
            }
            if (!$res2 = mysql_query("SELECT * from phorum_users "
                                     . "WHERE fk_campsite_user_id IS NULL")) {
                return "Unable to query the database $p_dbName";
            }
            if (mysql_num_rows($res2) == 0) {
                $version = "2.6.x";
            }
        }
        if (!$res2 = mysql_query("SHOW TABLES LIKE '%Audioclip%'")) {
            return "Unable to query the database $p_dbName";
        }
        if (mysql_num_rows($res2) > 0) {
            $version = "2.7.x";
        }
        if (!$res2 = mysql_query("SHOW TABLES LIKE 'liveuser_users'")) {
            return "Unable to query the database $p_dbName";
        }
        if (mysql_num_rows($res2) > 0) {
            if (!$res2 = mysql_query("SELECT * FROM liveuser_users "
                                     . "WHERE fk_user_type = 1")) {
                return "Unable to query the database $p_dbName";
            }
            if (mysql_num_rows($res2) > 0) {
                $version = "3.0";
            }
        }
    }

    return 0;
} // fn camp_detect_database_version

?>