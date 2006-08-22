<?php

if (!is_array($GLOBALS['argv'])) {
	camp_msg("Can't read command line arguments");
	exit(1);
}

global $Campsite, $CampsiteVars, $CampsiteOld, $info_messages, $g_silent;
$Campsite = array();
$CampsiteVars = array();
$CampsiteOld = array();
$info_messages = array();
$g_silent = false;

$errors = array();
camp_msg('');
camp_msg('Campsite create instance utility');
camp_msg('--------------------------------');
if (!camp_create_instance($GLOBALS['argv'], $errors)) {
	if ($g_silent) {
		exit(1);
	}
	camp_msg('There were ERRORS!!!\n');
	foreach($errors as $index=>$error) {
		camp_msg("$error", true, 2);
	}
	if (sizeof($Campsite) > 0) {
		camp_msg('');
		camp_msg('Campsite parameters:');
		foreach($Campsite as $var_name=>$value) {
			camp_msg("$var_name = $value", true, 2);
		}
	}
	exit(1);
}
camp_msg('');
camp_msg('The instance ' . $Campsite['DATABASE_NAME'] . ' was created successfuly.');
foreach ($info_messages as $index=>$message) {
	camp_msg($message);
}


function camp_create_instance($p_arguments, &$p_errors)
{
	global $Campsite, $CampsiteVars;

	$p_errors = array();
	// read parameters
	if (!$defined_parameters = camp_read_cmdline_parameters($p_arguments, $p_errors)) {
		camp_print_usage();
		return false;
	}

	$etc_dir = $defined_parameters['--etc_dir'];
	// check if etc directory was valid
	if (!is_dir($etc_dir)) {
		$p_errors[] = "Invalid etc directory " . $defined_parameters['--etc_dir'];
		return false;
	}

	// check if install_conf.php and parser_conf.php files exist
	if (!is_file($etc_dir . "/install_conf.php")
		|| !is_file($etc_dir . "/parser_conf.php")) {
		$p_errors = "Configuration file(s) are missing";
		return false;
	}

	require_once("cli_script_lib.php");

	if (!camp_is_readable("$etc_dir/install_conf.php")) {
		exit(1);
	}

	require_once($etc_dir . "/install_conf.php");
	require_once($etc_dir . "/parser_conf.php");
	require_once($Campsite['WWW_COMMON_DIR']."/html/campsite_version.php");
	camp_msg('Create instance script version: ' . $Campsite['VERSION']);

	if (!is_array($CampsiteVars['install']) || !is_array($CampsiteVars['parser'])
		|| !is_array($Campsite)) {
		$p_errors = "Invalid configuration file(s) format";
		return false;
	}

	camp_fill_missing_parameters($defined_parameters);
	camp_msg('Creating instance: ' . $Campsite['DATABASE_NAME']);

	if (!($res = camp_create_configuration_files($defined_parameters)) == 0) {
		$p_errors[] = $res;
		$p_errors[] = "Please run this script as user 'root' or '" . $Campsite['APACHE_USER'] . "'.";
		return false;
	}

	if (!($res = camp_create_site($defined_parameters)) == 0) {
		$p_errors[] = $res;
		$p_errors[] = "Please run this script as user 'root' or '" . $Campsite['APACHE_USER'] . "'.";
		return false;
	}

	$instance_etc_dir = $defined_parameters['--etc_dir'] . "/" . $defined_parameters['--db_name'];
	require_once($instance_etc_dir . "/database_conf.php");
	if (!$defined_parameters['--no_database']
		&& !($res = camp_create_database($defined_parameters)) == 0) {
		$p_errors[] = $res;
		return false;
	}

	// create language links
	$html_dir = $Campsite['WWW_DIR'] . "/" . $Campsite['DATABASE_NAME'] . "/html";
	require_once("$html_dir/parser_utils.php");
	camp_create_language_links($html_dir);

	return true;
} // fn camp_create_instance


function camp_create_configuration_files($p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	$global_etc_dir = $Campsite['ETC_DIR'];
	$instance_etc_dir = $global_etc_dir . "/" . $p_defined_parameters['--db_name'];
	if (!is_dir($instance_etc_dir)) {
		if (!@mkdir($instance_etc_dir)) {
			return "Unable to create configuration directory $instance_etc_dir.";
		}
	}

	$html_common_dir = $Campsite['WWW_COMMON_DIR'] . "/html";
	require_once($html_common_dir . "/classes/ModuleConfiguration.php");

	camp_msg(' * Creating the database configuration...', false);
	$db_module = new ModuleConfiguration();
	$db_variables = array('DATABASE_NAME'=>$p_defined_parameters['--db_name'],
		'DATABASE_SERVER_ADDRESS'=>$p_defined_parameters['--db_server_address'],
		'DATABASE_SERVER_PORT'=>$p_defined_parameters['--db_server_port'],
		'DATABASE_USER'=>$p_defined_parameters['--db_user'],
		'DATABASE_PASSWORD'=>$p_defined_parameters['--db_password']);
	$db_module->create('database', $db_variables);
	if (!($res = $db_module->save($instance_etc_dir)) == 0) {
		return $res;
	}
	camp_msg('done.');

	camp_msg(' * Creating the template engine configuration...', false);
	$parser_module = new ModuleConfiguration();
	$parser_variables = array('PARSER_PORT'=>$p_defined_parameters['--parser_port'],
		'PARSER_MAX_THREADS'=>$p_defined_parameters['--parser_max_threads']);
	$parser_module->create('parser', $parser_variables);
	if (!($res = $parser_module->save($instance_etc_dir)) == 0) {
		return $res;
	}
	camp_msg('done.');

	camp_msg(' * Creating the email notifiers configuration...', false);
	$smtp_module = new ModuleConfiguration();
	$smtp_variables = array(
		'SMTP_SERVER_ADDRESS'=>$p_defined_parameters['--smtp_server_address'],
		'SMTP_SERVER_PORT'=>$p_defined_parameters['--smtp_server_port']);
	$smtp_module->create('smtp', $smtp_variables);
	if (!($res = $smtp_module->save($instance_etc_dir)) == 0) {
		return $res;
	}
	camp_msg('done.');

	camp_msg(' * Creating the Apache configuration...', false);
	$apache_module = new ModuleConfiguration();
	$apache_variables = array('APACHE_USER'=>$p_defined_parameters['--apache_user'],
		'APACHE_GROUP'=>$p_defined_parameters['--apache_group']);
	$apache_module->create('apache', $apache_variables);
	if (!($res = $apache_module->save($instance_etc_dir)) == 0) {
		return $res;
	}
	camp_msg('done.');

	camp_msg(' * Setting privileges to the configuration files...', false);
	$cmd = "chown -R \"" . $Campsite['APACHE_USER'] . ":" . $Campsite['APACHE_GROUP']
		. "\" " . camp_escape_shell_arg($instance_etc_dir) . " 2>&1";
	@exec($cmd, $output, $res);
	if ($res != 0) {
		return implode("\n", $output);
	}

	$cmd = "chmod 640 " . camp_escape_shell_arg($instance_etc_dir) . "/* 2>&1";
	exec($cmd, $output, $res);
	if ($res != 0) {
		return implode("\n", $output);
	}
	camp_msg('done.');

	return 0;
} // fn camp_create_configuration_files


function camp_create_database($p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	$db_name = $p_defined_parameters['--db_name'];
	$db_dir = $Campsite['CAMPSITE_DIR'] . "/instance/database";

	$db_user = $Campsite['DATABASE_USER'];
	$db_password = $Campsite['DATABASE_PASSWORD'];
	$res = @mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'] . ":"
		. $Campsite['DATABASE_SERVER_PORT'], $db_user, $db_password);
	if (!$res) {
		return "Unable to connect to database server.";
	}

	if (camp_database_exists($db_name) && !camp_is_empty_database($db_name)) {
		camp_msg(' * Found an already existing database.');
		camp_msg(' * Backing up the existing database...', false);
		if (!($res = camp_backup_database_default($db_name, $p_defined_parameters)) == 0) {
			return $res;
		}
		camp_msg('done.');
		camp_msg("   The database of the instance $db_name was saved in the file:\n   "
				. $Campsite['CAMPSITE_DIR'] . "/backup/$db_name/$db_name-database.sql");
		if (!($res = camp_upgrade_database($db_name, $p_defined_parameters)) == 0) {
			camp_restore_database($db_name, $p_defined_parameters);
			return $res . "\nThere was an error when upgrading the database; "
				. "the old database was restored.\nA backup of the old database is in "
				. $Campsite['CAMPSITE_DIR'] . "/backup/$db_name directory.";
		}
	} else {
		camp_msg(' * Creating the database...', false);
		if (!camp_database_exists($db_name)
			&& !mysql_query("CREATE DATABASE $db_name CHARACTER SET utf8 COLLATE utf8_bin")) {
			return "Unable to create the database " . $db_name;
		}
		$cmd = "mysql --user=$db_user --host=" . $Campsite['DATABASE_SERVER_ADDRESS']
			. " --port=" . $Campsite['DATABASE_SERVER_PORT'];
		if ($db_password != "") {
			$cmd .= " --password=\"$db_password\"";
		}
		$cmd .= " " . camp_escape_shell_arg($db_name) . " < " . camp_escape_shell_arg($db_dir)
			. "/campsite-db.sql 2>&1";
		exec($cmd, $output, $res);
		if ($res != 0) {
			return implode("\n", $output);
		}
		camp_msg('done.');
	}

	return 0;
} // fn camp_create_database


function camp_upgrade_database($p_db_name, $p_defined_parameters)
{
	global $Campsite, $CampsiteVars;
	$campsite_dir = $Campsite['CAMPSITE_DIR'];
	$etc_dir = $Campsite['ETC_DIR'];
	$db_user = $p_defined_parameters['--db_user'];
	$db_password = $p_defined_parameters['--db_password'];

	if (!camp_database_exists($p_db_name)) {
		return "Can't upgrade database $p_db_name: it doesn't exist";
	}

	if (!($res = camp_detect_database_version($p_db_name, $old_version)) == 0) {
		return $res;
	}

	$first = true;
	$versions = array("2.0.x", "2.1.x", "2.2.x", "2.3.x", "2.4.x", "2.5.x", "2.6.0");
	foreach ($versions as $index=>$db_version) {
		if ($old_version > $db_version) {
			continue;
		}
		if ($first) {
			camp_msg(" * Upgrading the database from version $db_version...", false);
		}
		$output = array();
		// create symlinks to configuration files
		$upgrade_dir = $campsite_dir . "/instance/database/upgrade/$db_version/";
		$db_conf_file = $etc_dir . "/$p_db_name/database_conf.php";
		$link = $upgrade_dir . "/database_conf.php";
		@unlink($link);
		if (!is_link($link) && !symlink($db_conf_file, $link)) {
			return "Unable to create link to database configuration file";
		}
		$install_conf_file = $etc_dir . "/install_conf.php";
		$link = $upgrade_dir . "/install_conf.php";
		@unlink($link);
		if (!is_link($link) && !symlink($install_conf_file, $link)) {
			return "Unable to create link to install configuration file";
		}

		// run upgrade scripts
		$cmd_prefix = "cd " . camp_escape_shell_arg($upgrade_dir) . " && mysql --user=$db_user --host="
			. $Campsite['DATABASE_SERVER_ADDRESS']
			. " --port=" . $Campsite['DATABASE_SERVER_PORT'];
		if ($db_password != "") {
			$cmd_prefix .= " --password=\"$db_password\"";
		}
		$cmd_prefix .= " " . camp_escape_shell_arg($p_db_name) . " < ";
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
			camp_msg('done.');
			$first = false;
		}
	}

	return 0;
} // fn camp_upgrade_database


function camp_detect_database_version($p_db_name, &$version)
{
	if (!mysql_select_db($p_db_name)) {
		return "Can't select the database $p_db_name";
	}

	if (!$res = mysql_query("SHOW TABLES")) {
		return "Unable to query the database $p_db_name";
	}

	$version = "2.0.x";
	while ($row = mysql_fetch_row($res)) {
		if (in_array($row[0], array("ArticleTopics", "Topics"))) {
			$version = $version < "2.1.x" ? "2.1.x" : $version;
		}
		if (in_array($row[0], array("URLTypes", "TemplateTypes", "Templates", "Aliases",
				"ArticlePublish", "IssuePublish", "ArticleImages"))) {
			$version = "2.2.x";
			if (!$res2 = mysql_query("DESC UserTypes ManageReaders")) {
				return "Unable to query the database $p_db_name";
			}
			if (mysql_num_rows($res2) > 0) {
				$version = "2.3.x";
			}
			if (!$res2 = mysql_query("SHOW TABLES LIKE 'UserConfig'")) {
				return "Unable to query the database $p_db_name";
			}
			if (mysql_num_rows($res2) > 0) {
				$version = "2.4.x";
			}
			if (!$res2 = mysql_query("DESC SubsSections IdLanguage")) {
				return "Unable to query the database $p_db_name";
			}
			if (mysql_num_rows($res2) > 0) {
				$version = "2.5.x";
			}
			if (!$res2 = mysql_query("SHOW TABLES LIKE 'ArticleTypeMetadata'")) {
				return "Unable to query the database $p_db_name";
			}
			if (mysql_num_rows($res2) > 0) {
				$version = "2.6.0";
			}
			if (!$res2 = mysql_query("SHOW COLUMNS FROM ArticleTypeMetadata LIKE 'type_name'")) {
				return "Unable to query the database $p_db_name";
			}
			$row = mysql_fetch_array($res2, MYSQL_ASSOC);
			if (!is_null($row) && strstr($row['Type'], '166') != '') {
				$version = "2.6.x";
			}
		}
	}

	return 0;
} // fn camp_detect_database_version


function camp_backup_database_default($p_db_name, $p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	if (!camp_database_exists($p_db_name)) {
		return "Can't back up database $p_db_name: it doesn't exist";
	}

	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$p_db_name";
	if (!is_dir($backup_dir) && !mkdir($backup_dir)) {
		return "Unable to create the database backup directory $backup_dir";
	}

	$cmd = "mysqldump --user=" . $Campsite['DATABASE_USER'] . " --host="
		. $Campsite['DATABASE_SERVER_ADDRESS'] . " --port="
		. $Campsite['DATABASE_SERVER_PORT'] . " --add-drop-table -e -Q";
	if ($Campsite['DATABASE_PASSWORD'] != "") {
		$cmd .= " --password=\"" . $Campsite['DATABASE_PASSWORD'] . "\"";
	}
	$cmd .= " " . camp_escape_shell_arg($p_db_name) . " > "
		. camp_escape_shell_arg($backup_dir) . "/" . camp_escape_shell_arg($p_db_name) . "-database.sql";
	exec($cmd, $output, $res);
	if ($res != 0) {
		return implode("\n", $output);
	}

	return 0;
} // fn camp_backup_database_default


function camp_restore_database($p_db_name, $p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	$backup_file = $Campsite['CAMPSITE_DIR'] . "/backup/$p_db_name/$p_db_name-database.sql";
	if (!is_file($backup_file)) {
		return "Can't restore database: backup file not found";
	}

	if (camp_database_exists($p_db_name)) {
		if (($clean = camp_clean_database($p_db_name)) !== 0) {
			return $clean;
		}
	} else {
		if (!mysql_query("CREATE DATABASE $p_db_name CHARACTER SET utf8 COLLATE utf8_bin")) {
			return "Unable to restore database: can't create the database";
		}
	}

	$cmd = "mysql --user=" . $Campsite['DATABASE_USER'] . " --host="
		. $Campsite['DATABASE_SERVER_ADDRESS']
		. " --port=" . $Campsite['DATABASE_SERVER_PORT'];
	if ($Campsite['DATABASE_PASSWORD'] != "") {
		$cmd .= " --password=\"" . $Campsite['DATABASE_PASSWORD'] . "\"";
	}
	$cmd .= " $p_db_name < " . camp_escape_shell_arg($backup_file);
	exec($cmd, $output, $res);
	if ($res != 0) {
		return implode("\n", $output);
	}

	return 0;
} // fn camp_restore_database


function camp_create_site($p_defined_parameters)
{
	global $Campsite, $CampsiteVars, $CampsiteOld;

	$db_name = $p_defined_parameters['--db_name'];
	$etc_dir = $p_defined_parameters['--etc_dir'];
	$instance_etc_dir = $etc_dir . "/$db_name";
	require_once($etc_dir . "/install_conf.php");

	camp_msg(' * Creating site directories...', false);
	$instance_www_dir = $Campsite['WWW_DIR'] . "/$db_name";
	$html_dir = $instance_www_dir . "/html";
	$cgi_dir = $instance_www_dir . "/cgi-bin";
	$common_html_dir = $Campsite['WWW_COMMON_DIR'] . "/html";
	$common_cgi_dir = $Campsite['WWW_COMMON_DIR'] . "/cgi-bin";
	$instance_dirs = array('WWW_DIR'=>$instance_www_dir,
		'HTML_DIR'=>$instance_www_dir . "/html",
		'IMAGES_DIR'=>$instance_www_dir . "/html/images",
		'THUMBNAILS_DIR'=>$instance_www_dir . "/html/images/thumbnails",
		'TEMPLATES_DIR'=>$instance_www_dir . "/html/look",
		'CGI_DIR'=>$instance_www_dir . "/cgi-bin",
		'ATTACHMENTS_DIR'=>$instance_www_dir . "/html/files");
	// create directories
	foreach ($instance_dirs as $dir_type=>$dir_name) {
		if (!is_dir($dir_name) && !@mkdir($dir_name)) {
			return "Unable to create directory $dir_name";
		}
	}
	camp_msg('done.');

	if (isset($CampsiteOld['.MODULES_HTML_DIR'])) {
		camp_msg(' * Copying templates from the old 2.1 instance...', false);
		$cmd = "cp -fr " . camp_escape_shell_arg($CampsiteOld['.MODULES_HTML_DIR']) . "/look "
			. camp_escape_shell_arg($html_dir);
		exec($cmd, $output, $exit_code);
		if ($exit_code != 0) {
			return "Unable to copy files to templates directory ($html_dir/look).";
		}
		camp_msg('done.');
	}
	
	camp_msg(' * Creating the site structure...', false);
	// create symbolik links to configuration files
	$link_files = array("$etc_dir/install_conf.php"=>"$html_dir/install_conf.php",
		"$instance_etc_dir/database_conf.php"=>"$html_dir/database_conf.php",
		"$instance_etc_dir/parser_conf.php"=>"$html_dir/parser_conf.php",
		"$instance_etc_dir/smtp_conf.php"=>"$html_dir/smtp_conf.php",
		"$instance_etc_dir/apache_conf.php"=>"$html_dir/apache_conf.php",
		"$common_html_dir/index.php"=>"$html_dir/index.php",
		"$common_html_dir/admin.php"=>"$html_dir/admin.php",
		"$common_html_dir/db_connect.php"=>"$html_dir/db_connect.php",
		"$common_html_dir/configuration.php"=>"$html_dir/configuration.php",
		"$common_html_dir/campsite_version.php"=>"$html_dir/campsite_version.php",
		"$common_html_dir/parser_utils.php"=>"$html_dir/parser_utils.php",
		"$common_html_dir/classes"=>"$html_dir/classes",
		"$common_html_dir/css"=>"$html_dir/css",
		"$common_html_dir/include"=>"$html_dir/include",
		"$common_html_dir/javascript"=>"$html_dir/javascript",
		"$common_html_dir/priv"=>"$html_dir/admin-files",
		"$common_html_dir/attachment.php"=>"$html_dir/attachment.php",
		"$common_html_dir/comment_lib.php"=>"$html_dir/comment_lib.php"
		);
	foreach ($link_files as $file=>$link) {
		if (!is_link($link) && !@symlink($file, $link)) {
			return "Unable to create symbolic link to $file";
		}
	}

	$cp_cmd = "cp -f " . camp_escape_shell_arg($common_cgi_dir) . "/* " . camp_escape_shell_arg($cgi_dir)
			. " &> /dev/null";
	exec($cp_cmd, $output, $exit_code);
	if ($exit_code != 0) {
		return "Unable to copy files to CGI directory ($cgi_dir).";
	}
	camp_msg('done.');

	camp_msg(' * Creating the Apache virtual host configuration file...', false);
	if (!($res = camp_create_virtual_host($p_defined_parameters)) == 0) {
		return $res;
	}
	camp_msg('done.');

	camp_msg(' * Setting privileges for the site directories...', false);
	$cmd = "chown -R " . $Campsite['APACHE_USER'] . ":" . $Campsite['APACHE_GROUP']
		. " " . camp_escape_shell_arg($cgi_dir);
	exec($cmd);
	$cmd = "chmod -R u+w " . camp_escape_shell_arg($cgi_dir);
	exec($cmd);
	$cmd = "chmod -R ug+r " . camp_escape_shell_arg($cgi_dir);
	exec($cmd);

	$cmd = "chown -R " . $Campsite['APACHE_USER'] . ":" . $Campsite['APACHE_GROUP']
		. " " . camp_escape_shell_arg($instance_www_dir);
	exec($cmd);
	$cmd = "chmod -R u+w " . camp_escape_shell_arg($instance_www_dir);
	exec($cmd);
	$cmd = "chmod -R ug+r " . camp_escape_shell_arg($instance_www_dir);
	exec($cmd);
	camp_msg('done.');

	return 0;
} // fn camp_create_site


function camp_create_virtual_host(&$p_defined_parameters)
{
	global $Campsite, $info_messages;

	$etc_dir = $p_defined_parameters['--etc_dir'];
	$instance_name = $p_defined_parameters['--db_name'];
	$vhost_template = "$etc_dir/vhost-template.conf";
	$instance_vhost = "$etc_dir/$instance_name/$instance_name-vhost.conf";
	if (!is_file($vhost_template)) {
		return "Virtual host template file does not exist";
	}

	if (file_exists($instance_vhost)) {
		$info_messages[] = "The apache virtual host configuration file:\n\t$instance_vhost\nexisted and was not overwritten.";
		return 0;
	}

	$file_content = file_get_contents($vhost_template);

	$html_dir = $Campsite['WWW_DIR'] . "/$instance_name/html";
	$cgi_dir = $Campsite['WWW_DIR'] . "/$instance_name/cgi-bin";

	$search = array('$INSTANCE_HTML_DIR', '$INSTANCE_CGI_DIR');
	$replace = array($html_dir, $cgi_dir);
	$new_file_content = str_replace($search, $replace, $file_content);

	if (!$res = @fopen($instance_vhost, "w")) {
		return "Can not create instance virtual host configuration file.";
	}
	fwrite($res, $new_file_content);
	fclose($res);
	chown($instance_vhost, $Campsite['APACHE_USER']);
	chgrp($instance_vhost, $Campsite['APACHE_GROUP']);

	$info_messages[] = "The apache virtual host configuration file:\n\t$instance_vhost\nwas created.";
	$info_messages[] = "Please edit it and replace \$SERVER_NAME with the appropriate value.";

	return 0;
} // fn camp_create_virtual_host


function camp_fill_missing_parameters(&$p_defined_parameters)
{
	global $Campsite, $CampsiteVars, $CampsiteOld;
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults;
	camp_define_globals();

	// read existing configuration (if exists)
	if (!isset($p_defined_parameters['--db_name']) || trim($p_defined_parameters['--db_name']) == "") {
		$p_defined_parameters['--db_name'] = $g_parameters_defaults['--db_name'];
	}
	$db_name = $p_defined_parameters['--db_name'];
	$etc_dir = $p_defined_parameters['--etc_dir'];
	$instance_etc_dir = "$etc_dir/$db_name";
	$db_defined = false;
	if (file_exists("$instance_etc_dir/database_conf.php")) {
		require_once("$instance_etc_dir/database_conf.php");
		$db_params = array('--db_server_address'=>'DATABASE_SERVER_ADDRESS',
			'--db_server_port'=>'DATABASE_SERVER_PORT',
			'--db_user'=>'DATABASE_USER', '--db_password'=>'DATABASE_PASSWORD');
		foreach ($db_params as $cmd_param=>$conf_param) {
			if (!isset($p_defined_parameters[$cmd_param])) {
				$p_defined_parameters[$cmd_param] = $Campsite[$conf_param];
			}
		}
		$db_defined = true;
	}
	$parser_defined = false;
	if (file_exists("$instance_etc_dir/parser_conf.php")) {
		require_once("$instance_etc_dir/parser_conf.php");
		if (!isset($p_defined_parameters['--parser_port'])) {
			$p_defined_parameters['--parser_port'] = $Campsite['PARSER_PORT'];
		}
		if (!isset($p_defined_parameters['--parser_max_threads'])) {
			$p_defined_parameters['--parser_max_threads'] = $Campsite['PARSER_MAX_THREADS'];
		}
		$parser_defined = true;
	}
	$smtp_defined = false;
	if (file_exists("$instance_etc_dir/smtp_conf.php")) {
		require_once("$instance_etc_dir/smtp_conf.php");
		if (!isset($p_defined_parameters['--smtp_server_address'])) {
			$p_defined_parameters['--smtp_server_address'] =
				$Campsite['SMTP_SERVER_ADDRESS'];
		}
		if (!isset($p_defined_parameters['--smtp_server_port'])) {
			$p_defined_parameters['--smtp_server_port'] = $Campsite['SMTP_SERVER_PORT'];
		}
		$smtp_defined = true;
	}
	$apache_defined = false;
	if (file_exists("$instance_etc_dir/apache_conf.php")) {
		require_once("$instance_etc_dir/apache_conf.php");
		if (!isset($p_defined_parameters['--apache_user'])) {
			$p_defined_parameters['--apache_user'] = $Campsite['APACHE_USER'];
		}
		if (!isset($p_defined_parameters['--apache_group'])) {
			$p_defined_parameters['--apache_group'] = $Campsite['APACHE_GROUP'];
		}
		$apache_defined = true;
	}

	// read old configuration
	$old_conf_dir = "/etc/campsite.d/$db_name";
	if (is_dir("/usr/local/etc/campsite.d/$db_name")) {
		$old_conf_dir = "/usr/local/etc/campsite.d/$db_name";
	}

	$CampsiteOld = array();
	if (!$db_defined && camp_read_old_config($old_conf_dir, 'database', $CampsiteOld)) {
		$p_defined_parameters['--db_server_address'] = $CampsiteOld['DATABASE_SERVER'];
		$p_defined_parameters['--db_server_port'] = $CampsiteOld['DATABASE_PORT'];
		$p_defined_parameters['--db_user'] = $CampsiteOld['DATABASE_USER'];
		$p_defined_parameters['--db_password'] = $CampsiteOld['DATABASE_PASSWORD'];
	}
	if (!$parser_defined && camp_read_old_config($old_conf_dir, 'parser', $CampsiteOld)) {
		$p_defined_parameters['--parser_port'] = $CampsiteOld['PARSER_PORT'];
		$p_defined_parameters['--parser_max_threads'] = $CampsiteOld['PARSER_THREADS'];
	}
	if (!$smtp_defined && camp_read_old_config($old_conf_dir, 'smtp', $CampsiteOld)) {
		$p_defined_parameters['--smtp_server_address'] = $CampsiteOld['SMTP_SERVER'];
	}
	if (!$apache_defined && camp_read_old_config($old_conf_dir, 'parser', $CampsiteOld)) {
		$p_defined_parameters['--apache_user'] = $CampsiteOld['PARSER_USER'];
		$p_defined_parameters['--apache_group'] = $CampsiteOld['PARSER_GROUP'];
	}
	camp_read_old_config("$old_conf_dir/install", '.modules', $CampsiteOld);

	$params = array('--db_server_address'=>'DATABASE_SERVER_ADDRESS',
		'--db_server_port'=>'DATABASE_SERVER_PORT', '--db_user'=>'DATABASE_USER',
		'--db_password'=>'DATABASE_PASSWORD', '--db_name'=>'DATABASE_NAME',
		'--parser_port'=>'PARSER_PORT', '--parser_max_threads'=>'PARSER_MAX_THREADS',
		'--smtp_server_address'=>'SMTP_SERVER_ADDRESS',
		'--smtp_server_port'=>'SMTP_SERVER_PORT',
		'--apache_user'=>'APACHE_USER', '--apache_group'=>'APACHE_GROUP',
		'--etc_dir'=>'ETC_DIR');
	foreach ($g_instance_parameters as $param_index=>$param_name) {
		if (!array_key_exists($param_name, $p_defined_parameters)) {
			$param_value = $g_parameters_defaults[$param_name];
			if (strncmp($param_value, "___", 3) == 0) {
				$param_value = $Campsite[substr($param_value, 3)];
			}
			$p_defined_parameters[$param_name] = $param_value;
		}
		if (!array_key_exists($param_name, $params)) {
			continue;
		}
		$Campsite[$params[$param_name]] = $p_defined_parameters[$param_name];
	}

	// if the parser port was not defined yet calculate it
	if ($p_defined_parameters['--parser_port'] == 0) {
		$p_defined_parameters['--parser_port'] =
			camp_generate_parser_port($p_defined_parameters);
	}
} // fn camp_fill_missing_parameters


function camp_generate_parser_port($p_defined_parameters)
{
	global $Campsite;

	$etc_dir = $p_defined_parameters['--etc_dir'];
	if (!is_dir($etc_dir)) {
		die("Invalid directory $etc_dir");
	}

	$old_port_value = 0;
	$max_port_value = 0;
	$parser_port = 0;
	if (!$dh = opendir($etc_dir)) {
		die("Can't open $etc_dir\n");
	}
	while (($file_name = readdir($dh)) !== false) {
		if ($file_name == "." || $file_name == ".." || !is_dir("$etc_dir/$file_name")) {
			continue;
		}
		require_once("$etc_dir/$file_name/parser_conf.php");
		if ($file_name == $p_defined_parameters['--db_name']
			&& $Campsite['PARSER_PORT'] != 0) {
			return $Campsite['PARSER_PORT'];
		}
		if ($max_port_value < $Campsite['PARSER_PORT']) {
			$max_port_value = $Campsite['PARSER_PORT'];
		}
	}
	require_once("$etc_dir/parser_conf.php");
	$start_port_value = 0 + $Campsite['PARSER_START_PORT'];
	if ($start_port_value > $max_port_value) {
		return $start_port_value + 1;
	}
	return $max_port_value + 1;
} // fn camp_generate_parser_port


function camp_read_cmdline_parameters($p_arguments, &$p_errors)
{
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults, $g_silent;
	camp_define_globals();

	$defined_parameters = array();
	$p_errors = array();
	for ($arg_n = 1; $arg_n < sizeof($p_arguments); $arg_n++) {
		// read the parameter name
		$param_name = $p_arguments[$arg_n];
		if ($param_name == '--help') {
			camp_print_usage();
			exit(0);
		}
		if (!in_array($param_name, $g_instance_parameters)) {
			$p_errors[] = "Invalid parameter '$param_name'";
			continue;
		}
		if ($param_name == "--no_database") {
			$defined_parameters['--no_database'] = true;
			continue;
		}
		if ($param_name == "--silent") {
			$g_silent = true;
			continue;
		}
		if ($param_name == '--db_password'
				&& (($arg_n + 1) >= sizeof($p_arguments)
					|| in_array($p_arguments[$arg_n+1], $g_instance_parameters))) {
			$param_val = "";
		} else {
			// read the parameter value
			$arg_n++;
			if ($arg_n >= sizeof($p_arguments)) {
				$p_errors[] = "Value not specified for parameter '$param_name'";
				break;
			}
			$param_val = $p_arguments[$arg_n];
		}

		// set the parameter value in $defined_parameters array
		$defined_parameters[$param_name] = $param_val;
		if (array_key_exists($param_name, $g_mandatory_parameters))
			$g_mandatory_parameters[$param_name] = true;
	}
	// check if all mandatory parameters were specified
	foreach ($g_mandatory_parameters as $mp_name=>$mp_value) {
		if ($mp_value == false) {
			$p_errors[] = "Mandatory parameter '$mp_name' was not specified";
		}
	}

	if (sizeof($p_errors) > 0) {
		return false;
	}
	return $defined_parameters;
} // fn camp_read_cmdline_parameters


function camp_print_usage()
{
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults, $g_parameters_description;

	camp_define_globals();
	echo "\nCampsite create instance utility\n"
		."--------------------------------\n"
		."  Usage: campsite-create-instance [parameters]\n"
		."  Parameters may be:\n";
	foreach ($g_instance_parameters as $index=>$parameter) {
		if ($parameter == '--etc_dir') {
			continue;
		}
		if ($parameter != '--no_database' && $parameter != '--silent') {
			echo "    $parameter [value]\n";
		} else {
			echo "    $parameter\n";
		}
		if (isset($g_parameters_description[$parameter])) {
			echo "        " . $g_parameters_description[$parameter] . "\n";
		}
	}
} // fn camp_print_usage


function camp_define_globals()
{
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults, $g_parameters_description;

	// global variables
	$g_instance_parameters = array('--etc_dir', '--db_server_address', '--db_server_port',
		'--db_name', '--db_user', '--db_password', '--parser_port',
		'--parser_max_threads', '--smtp_server_address', '--smtp_server_port',
		'--apache_user', '--apache_group', '--no_database', '--silent');
	$g_mandatory_parameters = array('--etc_dir'=>false);
	$g_parameters_defaults = array(
		'--db_server_address'=>'___DEFAULT_DATABASE_SERVER_ADDRESS',
		'--db_server_port'=>'0',
		'--db_name'=>'campsite',
		'--db_user'=>'root',
		'--db_password'=>'',
		'--parser_port'=>'0',
		'--parser_max_threads'=>'0',
		'--smtp_server_address'=>'___DEFAULT_SMTP_SERVER_ADDRESS',
		'--smtp_server_port'=>'___DEFAULT_SMTP_SERVER_PORT',
		'--apache_user'=>'___APACHE_USER',
		'--apache_group'=>'___APACHE_GROUP',
		'--no_database'=>false,
		'--silent'=>false
	);
	$g_parameters_description = array(
		'--db_server_address'=>'Set the IP address of the MySQL database server. (default: "localhost")',
		'--db_server_port'=>"Set the port MySQL server listens to. Use this option only if your\n"
				   ."        server does not use the default port.",
		'--db_name'=>'Set the name of the instance to create. (default: "campsite")',
		'--db_user'=>"Set the user used to connect to the MySQL database server.\n"
			.'        (default: "root")',
		'--db_password'=>"Set the password used to connect to the MySQL database server.\n"
				.'        (default: empty string)',
		'--parser_port'=>"The template engine listes for new requests on this port. This port\n"
				."        is allocated automatically by this script. Use this option if you want\n"
				."        to set a custom port.",
		'--parser_max_threads'=>"Set the maximum number of template engine threads. Each thread\n"
					   ."        processes a single request. The maximum number of threads specifies\n"
					   ."        how many requests can be served simultaneously. (default: 40)",
		'--smtp_server_address'=>'Set the IP address of the email (SMTP) server. (default: "localhost")',
		'--smtp_server_port'=>'Set the port SMTP server is listening to. (default: 25)',
		'--apache_user'=>'Set this value to the user name on which the Apache server is running.',
		'--apache_group'=>'Set this value to the user name on which the Apache server is running.',
		'--no_database'=>"Instructs the script not to create the instance database. The script\n"
				."        will create only the site and configuration file. The instance is not\n"
				."        usable without a database. Use this option only if you know what you\n"
				."        are doing.",
		'--silent'=>'Do not output any message.');
} // fn camp_define_globals


function camp_read_old_config($conf_dir, $module_name, &$variables)
{
	$conf_file = "$conf_dir/$module_name.conf";
	if (!is_readable($conf_file) || !is_file($conf_file)) {
		return false;
	}
	if (!$lines = file($conf_file)) {
		return false;
	}

	$module_name = strtoupper($module_name);
	foreach ($lines as $index=>$line) {
		$ids = explode(" ", $line);
		$var_name = trim($ids[0]);
		if ($var_name == "") {
			continue;
		}
		$value = @isset($ids[1]) ? trim($ids[1]) : "";
		if ($res = strpos($value, "{")) {
			$value = trim(substr($value, 0, $res));
		}
		$variables[$module_name . "_" . $var_name] = $value;
	}
	return true;
} // fn camp_read_old_config


function camp_msg($p_message, $p_endLine = true, $p_indent = 0, $p_indentCharacter = ' ')
{
	global $g_silent;
	
	if (!$g_silent) {
		for ($i = 0; $i < $p_indent; $i++) {
			echo $p_indentCharacter;
		}
		echo "$p_message";
		if ($p_endLine) {
			echo "\n";
		}
	}
}

?>