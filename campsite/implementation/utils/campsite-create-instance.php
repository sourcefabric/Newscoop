<?php

if (!is_array($GLOBALS['argv'])) {
	echo "Can't read command line arguments\n";
	exit(1);
}

global $Campsite, $CampsiteVars, $CampsiteOld, $info_messages;
$Campsite = array();
$CampsiteVars = array();
$CampsiteOld = array();
$info_messages = array();

$errors = array();
if (!create_instance($GLOBALS['argv'], $errors)) {
	foreach($errors as $index=>$error)
		echo "$error\n";
	echo "Campsite parameters:\n";
	foreach($Campsite as $var_name=>$value)
		echo "$var_name = $value\n";
	exit(1);
}
foreach ($info_messages as $index=>$message)
	echo "$message\n";


function create_instance($p_arguments, &$p_errors)
{
	global $Campsite, $CampsiteVars;

	$p_errors = array();
	// read parameters
	if (!$defined_parameters = read_cmdline_parameters($p_arguments, $p_errors)) {
		print_usage();
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

	require_once($etc_dir . "/install_conf.php");
	require_once($etc_dir . "/parser_conf.php");
	require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");

	if (!is_array($CampsiteVars['install']) || !is_array($CampsiteVars['parser'])
		|| !is_array($Campsite)) {
		$p_errors = "Invalid configuration file(s) format";
		return false;
	}

	fill_missing_parameters($defined_parameters);

	if (!($res = create_configuration_files($defined_parameters)) == 0) {
		$p_errors[] = $res;
		return false;
	}

	if (!($res = create_site($defined_parameters)) == 0) {
		$p_errors[] = $res;
		return false;
	}

	$instance_etc_dir = $defined_parameters['--etc_dir'] . "/" . $defined_parameters['--db_name'];
	require_once($instance_etc_dir . "/database_conf.php");
	if (!$defined_parameters['--no_database']
		&& !($res = create_database($defined_parameters)) == 0) {
		$p_errors[] = $res;
		return false;
	}

	// create language links
	$html_dir = $Campsite['WWW_DIR'] . "/" . $Campsite['DATABASE_NAME'] . "/html";
	require_once("$html_dir/parser_utils.php");
	camp_create_language_links($html_dir);

	return true;
}


function create_configuration_files($p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	$global_etc_dir = $Campsite['ETC_DIR'];
	$instance_etc_dir = $global_etc_dir . "/" . $p_defined_parameters['--db_name'];
	if (!is_dir($instance_etc_dir))
		if (!mkdir($instance_etc_dir))
			return "Unable to create configuration directory $instance_etc_dir";

	$html_common_dir = $Campsite['WWW_COMMON_DIR'] . "/html";
	require_once($html_common_dir . "/classes/ModuleConfiguration.php");

	$db_module = new ModuleConfiguration;
	$db_variables = array('DATABASE_NAME'=>$p_defined_parameters['--db_name'],
		'DATABASE_SERVER_ADDRESS'=>$p_defined_parameters['--db_server_address'],
		'DATABASE_SERVER_PORT'=>$p_defined_parameters['--db_server_port'],
		'DATABASE_USER'=>$p_defined_parameters['--db_user'],
		'DATABASE_PASSWORD'=>$p_defined_parameters['--db_password']);
	$db_module->create('database', $db_variables);
	if (!($res = $db_module->save($instance_etc_dir)) == 0)
		return $res;

	$parser_module = new ModuleConfiguration;
	$parser_variables = array('PARSER_PORT'=>$p_defined_parameters['--parser_port'],
		'PARSER_MAX_THREADS'=>$p_defined_parameters['--parser_max_threads']);
	$parser_module->create('parser', $parser_variables);
	if (!($res = $parser_module->save($instance_etc_dir)) == 0)
		return $res;

	$smtp_module = new ModuleConfiguration;
	$smtp_variables = array(
		'SMTP_SERVER_ADDRESS'=>$p_defined_parameters['--smtp_server_address'],
		'SMTP_SERVER_PORT'=>$p_defined_parameters['--smtp_server_port']);
	$smtp_module->create('smtp', $smtp_variables);
	if (!($res = $smtp_module->save($instance_etc_dir)) == 0)
		return $res;

	$apache_module = new ModuleConfiguration;
	$apache_variables = array('APACHE_USER'=>$p_defined_parameters['--apache_user'],
		'APACHE_GROUP'=>$p_defined_parameters['--apache_group']);
	$apache_module->create('apache', $apache_variables);
	if (!($res = $apache_module->save($instance_etc_dir)) == 0)
		return $res;

	$cmd = "chown -R \"" . $Campsite['APACHE_USER'] . ":" . $Campsite['APACHE_GROUP']
		. "\" " . escape_shell_arg($instance_etc_dir) . " 2>&1";
	exec($cmd, $output, $res);
	if ($res != 0)
		return implode("\n", $output);

	$cmd = "chmod 640 " . escape_shell_arg($instance_etc_dir) . "/* 2>&1";
	exec($cmd, $output, $res);
	if ($res != 0)
		return implode("\n", $output);

	return 0;
}


function create_database($p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	$db_name = $p_defined_parameters['--db_name'];
	$db_dir = $Campsite['CAMPSITE_DIR'] . "/instance/database";

	$db_user = $Campsite['DATABASE_USER'];
	$db_password = $Campsite['DATABASE_PASSWORD'];
	$res = mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'] . ":"
		. $Campsite['DATABASE_SERVER_PORT'], $db_user, $db_password);
	if (!$res)
		return "Unable to connect to database server";

	$db_exists = database_exists($db_name);
	$db_is_empty = is_empty_database($db_name);
	if ($db_exists && !$db_is_empty) {
		if (!($res = backup_database_default($db_name, $p_defined_parameters)) == 0)
			return $res;
		if (!($res = upgrade_database($db_name, $p_defined_parameters)) == 0) {
			restore_database($db_name, $p_defined_parameters);
			return $res . "\nThere was an error when upgrading the database; "
				. "the old database was restored.\nA backup of the old database is in "
				. $Campsite['CAMPSITE_DIR'] . "/backup/$db_name directory.";
		}
	} else {
		if (!$db_exists && !mysql_query("CREATE DATABASE " . $db_name))
			return "Unable to create the database " . $db_name;
		$cmd = "mysql --user=$db_user --host=" . $Campsite['DATABASE_SERVER_ADDRESS']
			. " --port=" . $Campsite['DATABASE_SERVER_PORT'];
		if ($db_password != "")
			$cmd .= " --password=\"$db_password\"";
		$cmd .= " " . escape_shell_arg($db_name) . " < " . escape_shell_arg($db_dir)
			. "/campsite-db.sql 2>&1";
		exec($cmd, $output, $res);
		if ($res != 0)
			return implode("\n", $output);
	}

	return 0;
}


function upgrade_database($p_db_name, $p_defined_parameters)
{
	global $Campsite, $CampsiteVars;
	$campsite_dir = $Campsite['CAMPSITE_DIR'];
	$etc_dir = $Campsite['ETC_DIR'];
	$db_user = $p_defined_parameters['--db_user'];
	$db_password = $p_defined_parameters['--db_password'];

	if (!database_exists($p_db_name))
		return "Can't upgrade database $p_db_name: it doesn't exist";

	if (!($res = detect_database_version($p_db_name, $old_version)) == 0)
		return $res;

	$versions = array("2.0.x", "2.1.x", "2.2.x", "2.3.x");
	foreach ($versions as $index=>$db_version) {
		if ($old_version > $db_version)
			continue;
		$output = array();
		// create symlinks to configuration files
		$upgrade_dir = $campsite_dir . "/instance/database/upgrade/$db_version/";
		$db_conf_file = $etc_dir . "/$p_db_name/database_conf.php";
		$link = $upgrade_dir . "/database_conf.php";
		@unlink($link);
		if (!is_link($link) && !symlink($db_conf_file, $link))
			return "Unable to create link to database configuration file";
		$install_conf_file = $etc_dir . "/install_conf.php";
		$link = $upgrade_dir . "/install_conf.php";
		@unlink($link);
		if (!is_link($link) && !symlink($install_conf_file, $link))
			return "Unable to create link to install configuration file";

		// run upgrade scripts
		$cmd_prefix = "cd " . escape_shell_arg($upgrade_dir) . " && mysql --user=$db_user --host="
			. $Campsite['DATABASE_SERVER_ADDRESS']
			. " --port=" . $Campsite['DATABASE_SERVER_PORT'];
		if ($db_password != "")
			$cmd_prefix .= " --password=\"$db_password\"";
		$cmd_prefix .= " " . escape_shell_arg($p_db_name) . " < ";
		$sql_scripts = array("tables.sql", "data-required.sql", "data-optional.sql");
		foreach ($sql_scripts as $index=>$script) {
			if (!is_file($upgrade_dir . $script))
				continue;
			$cmd = $cmd_prefix . $script . " 2>&1";
			exec($cmd, $output, $res);
			if ($res != 0 && $script != "data-optional.sql")
				return "$script ($db_version): " . implode("\n", $output);
		}
	}

	return 0;
}


function detect_database_version($p_db_name, &$version)
{
	if (!mysql_select_db($p_db_name))
		return "Can't select the databae $p_db_name";

	if (!$res = mysql_query("SHOW TABLES"))
		return "Unable to query the database $p_db_name";

	$version = "2.0.x";
	while ($row = mysql_fetch_row($res)) {
		if (in_array($row[0], array("ArticleTopics", "Topics")))
			$version = $version < "2.1.x" ? "2.1.x" : $version;
		if (in_array($row[0], array("URLTypes", "TemplateTypes", "Templates", "Aliases",
				"ArticlePublish", "IssuePublish", "ArticleImages"))) {
			$version = "2.2.x";
			if (!$res2 = mysql_query("DESC UserTypes ManageReaders"))
				return "Unable to query the database $p_db_name";
			if (mysql_num_rows($res2) > 0) {
				$version = "2.3.x";
			}
			if (!$res2 = mysql_query("SHOW TABLES LIKE 'UserConfig'"))
				return "Unable to query the database $p_db_name";
			if (mysql_num_rows($res2) > 0) {
				$version = "2.4.x";
			}
		}
	}

	return 0;
}


function backup_database_default($p_db_name, $p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	if (!database_exists($p_db_name))
		return "Can't back up database $p_db_name: it doesn't exist";

	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$p_db_name";
	if (!is_dir($backup_dir) && !mkdir($backup_dir))
		return "Unable to create database backup directory $backup_dir";

	$cmd = "mysqldump --user=" . $Campsite['DATABASE_USER'] . " --host="
		. $Campsite['DATABASE_SERVER_ADDRESS'] . " --port="
		. $Campsite['DATABASE_SERVER_PORT'] . " --add-drop-table -e -Q";
	if ($Campsite['DATABASE_PASSWORD'] != "")
		$cmd .= " --password=\"" . $Campsite['DATABASE_PASSWORD'] . "\"";
	$cmd .= " " . escape_shell_arg($p_db_name) . " > "
		. escape_shell_arg($backup_dir) . "/" . escape_shell_arg($p_db_name) . "-database.sql";
	exec($cmd, $output, $res);
	if ($res != 0)
		return implode("\n", $output);

	return 0;
}


function restore_database($p_db_name, $p_defined_parameters)
{
	global $Campsite, $CampsiteVars;

	$backup_file = $Campsite['CAMPSITE_DIR'] . "/backup/$p_db_name/$p_db_name-database.sql";
	if (!is_file($backup_file))
		return "Can't restore database: backup file not found";

	if (database_exists($p_db_name)) {
		if (($clean = clean_database($p_db_name)) !== 0)
			return $clean;
	} else {
		if (!mysql_query("CREATE DATABASE $p_db_name"))
			return "Unable to restore database: can't create the database";
	}

	$cmd = "mysql --user=" . $Campsite['DATABASE_USER'] . " --host="
		. $Campsite['DATABASE_SERVER_ADDRESS']
		. " --port=" . $Campsite['DATABASE_SERVER_PORT'];
	if ($Campsite['DATABASE_PASSWORD'] != "")
		$cmd .= " --password=\"" . $Campsite['DATABASE_PASSWORD'] . "\"";
	$cmd .= " $p_db_name < " . escape_shell_arg($backup_file);
	exec($cmd, $output, $res);
	if ($res != 0)
		return implode("\n", $output);

	return 0;
}


function create_site($p_defined_parameters)
{
	global $Campsite, $CampsiteVars, $CampsiteOld;

	$db_name = $p_defined_parameters['--db_name'];
	$etc_dir = $p_defined_parameters['--etc_dir'];
	$instance_etc_dir = $etc_dir . "/$db_name";
	require_once($etc_dir . "/install_conf.php");

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
	foreach ($instance_dirs as $dir_type=>$dir_name)
		if (!is_dir($dir_name) && !mkdir($dir_name))
			return "Unable to create directory $dir_name";

	if (isset($CampsiteOld['.MODULES_HTML_DIR'])) {
		$cmd = "cp -fr " . escape_shell_arg($CampsiteOld['.MODULES_HTML_DIR']) . "/look "
			. escape_shell_arg($html_dir);
		exec($cmd);
	}
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
		"$common_html_dir/attachment.php"=>"$html_dir/attachment.php"
		);
	foreach ($link_files as $file=>$link) {
		if (!is_link($link) && !symlink($file, $link))
			return "Unable to create symbolic link to $file";
	}

	if (!($res = create_virtual_host($p_defined_parameters)) == 0)
		return $res;

	$cp_cmd = "cp -f " . escape_shell_arg($common_cgi_dir) . "/* " . escape_shell_arg($cgi_dir);
	exec($cp_cmd);

	$cmd = "chown -R " . $Campsite['APACHE_USER'] . ":" . $Campsite['APACHE_GROUP']
		. " " . escape_shell_arg($instance_www_dir);
	exec($cmd);
	$cmd = "chmod -R u+w " . escape_shell_arg($instance_www_dir);
	exec($cmd);
	$cmd = "chmod -R ug+r " . escape_shell_arg($instance_www_dir);
	exec($cmd);

	return 0;
}


function create_virtual_host(&$p_defined_parameters)
{
	global $Campsite, $info_messages;

	$etc_dir = $p_defined_parameters['--etc_dir'];
	$instance_name = $p_defined_parameters['--db_name'];
	$vhost_template = "$etc_dir/vhost-template.conf";
	$instance_vhost = "$etc_dir/$instance_name/$instance_name-vhost.conf";
	if (!is_file($vhost_template))
		return "Virtual host template file does not exist";

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

	if (!$res = fopen($instance_vhost, "w"))
		return "Can not create instance virtual host configuration file";
	fwrite($res, $new_file_content);
	fclose($res);
	chown($instance_vhost, $Campsite['APACHE_USER']);
	chgrp($instance_vhost, $Campsite['APACHE_GROUP']);

	$info_messages[] = "The apache virtual host configuration file:\n\t$instance_vhost\nwas created.";
	$info_messages[] = "Please edit it and replace \$SERVER_NAME with the appropriate value.";

	return 0;
}


function fill_missing_parameters(&$p_defined_parameters)
{
	global $Campsite, $CampsiteVars, $CampsiteOld;
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults;
	define_globals();

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
		foreach ($db_params as $cmd_param=>$conf_param)
			if (!isset($p_defined_parameters[$cmd_param]))
				$p_defined_parameters[$cmd_param] = $Campsite[$conf_param];
		$db_defined = true;
	}
	$parser_defined = false;
	if (file_exists("$instance_etc_dir/parser_conf.php")) {
		require_once("$instance_etc_dir/parser_conf.php");
		if (!isset($p_defined_parameters['--parser_port']))
			$p_defined_parameters['--parser_port'] = $Campsite['PARSER_PORT'];
		if (!isset($p_defined_parameters['--parser_max_threads']))
			$p_defined_parameters['--parser_max_threads'] = $Campsite['PARSER_MAX_THREADS'];
		$parser_defined = true;
	}
	$smtp_defined = false;
	if (file_exists("$instance_etc_dir/smtp_conf.php")) {
		require_once("$instance_etc_dir/smtp_conf.php");
		if (!isset($p_defined_parameters['--smtp_server_address']))
			$p_defined_parameters['--smtp_server_address'] =
				$Campsite['SMTP_SERVER_ADDRESS'];
		if (!isset($p_defined_parameters['--smtp_server_port']))
			$p_defined_parameters['--smtp_server_port'] = $Campsite['SMTP_SERVER_PORT'];
		$smtp_defined = true;
	}
	$apache_defined = false;
	if (file_exists("$instance_etc_dir/apache_conf.php")) {
		require_once("$instance_etc_dir/apache_conf.php");
		if (!isset($p_defined_parameters['--apache_user']))
			$p_defined_parameters['--apache_user'] = $Campsite['APACHE_USER'];
		if (!isset($p_defined_parameters['--apache_group']))
			$p_defined_parameters['--apache_group'] = $Campsite['APACHE_GROUP'];
		$apache_defined = true;
	}

	// read old configuration
	$old_conf_dir = "/etc/campsite.d/$db_name";
	if (is_dir("/usr/local/etc/campsite.d/$db_name"))
		$old_conf_dir = "/usr/local/etc/campsite.d/$db_name";

	$CampsiteOld = array();
	if (!$db_defined && read_old_config($old_conf_dir, 'database', $CampsiteOld)) {
		$p_defined_parameters['--db_server_address'] = $CampsiteOld['DATABASE_SERVER'];
		$p_defined_parameters['--db_server_port'] = $CampsiteOld['DATABASE_PORT'];
		$p_defined_parameters['--db_user'] = $CampsiteOld['DATABASE_USER'];
		$p_defined_parameters['--db_password'] = $CampsiteOld['DATABASE_PASSWORD'];
	}
	if (!$parser_defined && read_old_config($old_conf_dir, 'parser', $CampsiteOld)) {
		$p_defined_parameters['--parser_port'] = $CampsiteOld['PARSER_PORT'];
		$p_defined_parameters['--parser_max_threads'] = $CampsiteOld['PARSER_THREADS'];
	}
	if (!$smtp_defined && read_old_config($old_conf_dir, 'smtp', $CampsiteOld)) {
		$p_defined_parameters['--smtp_server_address'] = $CampsiteOld['SMTP_SERVER'];
	}
	if (!$apache_defined && read_old_config($old_conf_dir, 'parser', $CampsiteOld)) {
		$p_defined_parameters['--apache_user'] = $CampsiteOld['PARSER_USER'];
		$p_defined_parameters['--apache_group'] = $CampsiteOld['PARSER_GROUP'];
	}
	read_old_config("$old_conf_dir/install", '.modules', $CampsiteOld);

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
			generate_parser_port($p_defined_parameters);
	}
}


function generate_parser_port($p_defined_parameters)
{
	global $Campsite;

	$etc_dir = $p_defined_parameters['--etc_dir'];
	if (!is_dir($etc_dir))
		die("Invalid directory $etc_dir");

	$old_port_value = 0;
	$max_port_value = 0;
	$parser_port = 0;
	if (!$dh = opendir($etc_dir))
		die("Can't open $etc_dir\n");
	while (($file_name = readdir($dh)) !== false) {
		if ($file_name == "." || $file_name == ".." || !is_dir("$etc_dir/$file_name"))
			continue;
		require_once("$etc_dir/$file_name/parser_conf.php");
		if ($file_name == $p_defined_parameters['--db_name']
			&& $Campsite['PARSER_PORT'] != 0) {
			return $Campsite['PARSER_PORT'];
		}
		if ($max_port_value < $Campsite['PARSER_PORT'])
			$max_port_value = $Campsite['PARSER_PORT'];
	}
	require_once("$etc_dir/parser_conf.php");
	$start_port_value = 0 + $Campsite['PARSER_START_PORT'];
	if ($start_port_value > $max_port_value)
		return $start_port_value + 1;
	return $max_port_value + 1;
}


function read_cmdline_parameters($p_arguments, &$p_errors)
{
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults;
	define_globals();

	$defined_parameters = array();
	$p_errors = array();
	for ($arg_n = 1; $arg_n < sizeof($p_arguments); $arg_n++) {
		// read the parameter name
		$param_name = $p_arguments[$arg_n];
		if ($param_name == '--help') {
			print_usage();
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
		// read the parameter value
		$arg_n++;
		if ($arg_n >= sizeof($p_arguments)) {
			$p_errors[] = "Value not specified for argument '$param_name'";
			break;
		}
		$param_val = $p_arguments[$arg_n];

		// set the parameter value in $defined_parameters array
		$defined_parameters[$param_name] = $param_val;
		if (array_key_exists($param_name, $g_mandatory_parameters))
			$g_mandatory_parameters[$param_name] = true;
	}
	// check if all mandatory parameters were specified
	foreach ($g_mandatory_parameters as $mp_name=>$mp_value)
		if ($mp_value == false)
			$p_errors[] = "Mandatory parameter '$mp_name' was not specified";

	if (sizeof($p_errors) > 0)
		return false;
	return $defined_parameters;
}


function print_usage()
{
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults;

	define_globals();
	echo "Usage: campsite-create-instance [arguments]\nArguments may be:\n";
	foreach ($g_instance_parameters as $index=>$parameter) {
		if ($parameter != '--no_database')
			echo "\t$parameter [value]\n";
		else
			echo "\t$parameter\n";
	}
}


function define_globals()
{
	global $g_instance_parameters, $g_mandatory_parameters, $g_parameters_defaults;

	// global variables
	$g_instance_parameters = array('--etc_dir', '--db_server_address', '--db_server_port',
		'--db_name', '--db_user', '--db_password', '--parser_port',
		'--parser_max_threads', '--smtp_server_address', '--smtp_server_port',
		'--apache_user', '--apache_group', '--no_database');
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
		'--no_database'=>false
	);
}


function read_old_config($conf_dir, $module_name, &$variables)
{
	$conf_file = "$conf_dir/$module_name.conf";
	if (!is_file($conf_file))
		return false;
	if (!$lines = file($conf_file))
		return false;

	$module_name = strtoupper($module_name);
	foreach ($lines as $index=>$line) {
		$ids = explode(" ", $line);
		$var_name = trim($ids[0]);
		if ($var_name == "")
			continue;
		$value = @isset($ids[1]) ? trim($ids[1]) : "";
		if ($res = strpos($value, "{"))
			$value = trim(substr($value, 0, $res));
		$variables[$module_name . "_" . $var_name] = $value;
	}
	return true;
}


?>
