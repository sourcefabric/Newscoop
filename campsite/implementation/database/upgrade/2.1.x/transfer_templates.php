#!/usr/bin/php

<?php

$db_name = 'campsite';
$db_user = 'root';
$db_passwd = '';
$db_host = 'localhost';

$templates_dir = '/var/www/html/look/';

if ($argc == 1)
	echo "Running with default configuration: \n"
		. "\tdb_name = " . $db_name . "\n"
		. "\tdb_user = " . $db_user . "\n"
		. "\tdb_passwd = " . $db_passwd . "\n"
		. "\tdb_host = " . $db_host . "\n"
		. "\ttemplates_dir = " . $templates_dir . "\n";

if ($argc > 1) {
	$usage = "Usage: transfer_templates.php [-d <db_name>] [-u <user>]\n"
		. "\t[-p <passwd>] [-h <host>] [-t <templates_dir>]\n";
	$i = 1;
	while ($i < $argc) {
		$arg = $argv[$i];
		switch ($arg) {
			case '-d':
				$i++;
				$db_name = $argv[$i];
				break;
			case '-u':
				$i++;
				$db_user = $argv[$i];
				break;
			case '-p':
				$i++;
				$db_paswd = $argv[$i];
				break;
			case '-h':
				$i++;
				$db_host = $argv[$i];
				break;
			case '-t':
				$i++;
				$templates_dir = $argv[$i];
				break;
			default:
				echo "ERROR! Invalid argument " . $arg . "\n" . $usage;
		}
		$i++;
	}
}

if (!is_dir($templates_dir))
	die("ERROR! " . $templates_dir . " is not a directory.\n");

if ($templates_dir[strlen($templates_dir) - 1] == '/') {
	$len = strlen($templates_dir) - 1;
	$templates_dir = substr($templates_dir, 0, $len);
}

if (!mysql_connect($db_host, $db_user, $db_passwd))
	die("Unable to connect to the database.\n");
if (!mysql_select_db($db_name))
	die("Unable to use the database " . $db_name . ".\n");

transfer_templates($templates_dir, $templates_dir);
update_issues();
$sql = "CREATE TABLE TransferTemplates (Done int NOT NULL);";
mysql_query($sql);


function transfer_templates($dir, $root_dir, $level = 0)
{
	if (!$dh = @opendir($dir))
		die("ERROR! Unable to open directory " . $dir . ".\n");
	while ($file = readdir($dh)) {
		if ($file == "." || $file == "..")
			continue;

		$full_path = $dir . "/" . $file;
		$filetype = filetype($full_path);
		if ($filetype == "dir") {
			transfer_templates($full_path, $root_dir, $level + 1);
			continue;
		}

		if ($filetype != "file") // ignore special files and links
			continue;
		$ending = substr($file, strlen($file) - 4);
		if ($ending != ".tpl") // ignore files that are not templates (end in .tpl)
			continue;

		$rel_path = substr($full_path, strlen($root_dir) + 1);
		$sql = "INSERT IGNORE INTO Templates (Name, Level) values('"
		     . $rel_path . "', " . $level . ")";
		if (!mysql_query($sql))
			die("Unable to insert template " . $rel_path . ".\n");
	}
}

function update_issues()
{
	$sql = "SELECT * FROM Issues";
	if (!($res = mysql_query($sql)))
		die("Unable to read from the database.\n");
	while ($row = mysql_fetch_array($res)) {
		$issue_tpl = substr($row['FrontPage'], strlen('/look/'));
		$article_tpl = substr($row['SingleArticle'], strlen('/look/'));
		$id_pub = $row['IdPublication'];
		$issue = $row['Number'];
		$id_lang = $row['IdLanguage'];

		$sql = "SELECT * FROM Templates WHERE Name = '" . $issue_tpl . "'";
		set_template_type($issue_tpl, 'issue');
		set_template_type($article_tpl, 'article');

		$sql = "UPDATE Issues SET FrontPage = '" . $issue_tpl . "', SingleArticle = '"
		     . $article_tpl . "' WHERE IdPublication = " . $id_pub . " AND Number = " . $issue
		     . " AND IdLanguage = " . $id_lang;
		mysql_query($sql);
	}
}

function set_template_type($tpl, $type)
{
	$id_type = get_template_type_id($type);
	$sql = "UPDATE Templates SET Type = " . $id_type . " WHERE Name = '" . $tpl . "'";
	mysql_query($sql);
}

$template_types = array();

function get_template_type_id($type)
{
	global $template_types;

	if (!isset($template_types))
		$template_types = array();

	if (!array_key_exists($type, $template_types)) {
		$sql = "SELECT * FROM TemplateTypes WHERE Name = '" . $type . "'";
		if (!($res = mysql_query($sql)))
			die("Unable to read from the database.\n");
		if (!($row = mysql_fetch_array($res)))
			die("Invalid template type " . $type . ".\n");
		$template_types[$type] = $row['Id'];
	}
	return $template_types[$type];
}

?>
