<?php

$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once("$cs_dir/conf/database_conf.php");
require_once("$cs_dir/conf/install_conf.php");
if (!is_array($Campsite)) {
    echo "Invalid configuration file(s)";
    exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
    die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
    die("Unable to use the database " . $db_name . ".\n");
}
mysql_query("SET NAMES 'utf8'");

//
// read the id of the article object type
//
$sql = "SELECT id FROM ObjectTypes WHERE name = 'article'";
if (!($res = mysql_query($sql))) {
	die("Unable to read from the database.\n");
}
$row = mysql_fetch_array($res, MYSQL_ASSOC);
if (!$row) {
    die("Unable to read from the database.\n");
}
$articleObjectTypeId = $row['id'];

$sql = "SELECT Id FROM Languages WHERE Code = 'en'";
if (!($res = mysql_query($sql))) {
    die("Unable to read from the database.\n");
}
$row = mysql_fetch_array($res, MYSQL_ASSOC);
if (!$row) {
    die("Unable to read from the database.\n");
}
$englishLanguageId = $row['Id'];

$sql = "UPDATE AutoId SET translation_phrase_id=LAST_INSERT_ID(translation_phrase_id + 1)";
if (!($res = mysql_query($sql))) {
    die("Unable to write to the database.\n");
}
$sql = "SELECT LAST_INSERT_ID() AS id";
if (!($res = mysql_query($sql))) {
    die("Unable to read from the database.\n");
}
$row = mysql_fetch_array($res, MYSQL_ASSOC);
if (!$row) {
    die("Unable to read from the database.\n");
}
$newPhraseId = $row['id'];

$sql = "INSERT INTO Translations (phrase_id, fk_language_id, translation_text)
		VALUES ($newPhraseId, $englishLanguageId, 'article')";
if (!($res = mysql_query($sql))) {
    die("Unable to write to the database.\n");
}

?>
