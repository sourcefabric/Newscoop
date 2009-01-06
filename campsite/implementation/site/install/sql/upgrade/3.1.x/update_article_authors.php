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
    die("Unable to use the database `$db_name`.\n");
}

$sql = "SELECT lu.Name, lu.EMail, a.Number, a.IdLanguage "
     . "FROM liveuser_users AS lu, Articles AS a "
     . "WHERE lu.id = a.IdUser";
if (!($res = mysql_query($sql))) {
    die("Unable to read from the database. $sql\n");
}
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$firstName = '';
	$lastName = '';
    $fullName = $row['Name'];
	$email = $row['EMail'];
	$articleNumber = $row['Number'];
	$languageId = $row['IdLanguage'];

	preg_match('/([^,]+),([^,]+)/', $fullName, $matches);
	if (count($matches) > 0) {
		$lastName = trim($matches[1]);
		$firstName = isset($matches[2]) ? trim($matches[2]) : '';
		write_author($firstName, $lastName, $email, $articleNumber, $languageId);
	}
	preg_match_all('/[^\s]+/', $fullName, $matches);
	if (isset($matches[0])) {
		$matches = $matches[0];
	}
	if (count($matches) > 1) {
		$lastName = array_pop($matches);
		$firstName = implode(' ', $matches);
        write_author($firstName, $lastName, $email, $articleNumber, $languageId);
	}
	if (count($matches) == 1) {
		$firstName = $matches[0];
        write_author($firstName, $lastName, $email, $articleNumber, $languageId);
	}
}

function write_author($p_first_name, $p_last_name, $p_email,
                      $p_article_number, $p_language_id)
{
	$p_first_name = mysql_real_escape_string($p_first_name);
    $p_last_name = mysql_real_escape_string($p_last_name);
    $p_email = mysql_real_escape_string($p_email);
    $p_article_number = mysql_real_escape_string($p_article_number);
    $p_language_id = mysql_real_escape_string($p_language_id);
    
    $sql = "SELECT id FROM Authors WHERE "
         . "first_name = '$p_first_name' AND last_name = '$p_last_name'";
    $res = mysql_query($sql);
    if ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
    	$author_id = $row['id'];
    } else {
    	$sql = "INSERT IGNORE INTO Authors (first_name, last_name, email) "
    	. "VALUES('$p_first_name', '$p_last_name', '$p_email')";
    	$res = mysql_query($sql);
    	if (!$res) {
    		return false;
    	}

    	$sql = "SELECT LAST_INSERT_ID() AS id";
    	$res = mysql_query($sql);
    	$row = mysql_fetch_array($res, MYSQL_ASSOC);
    	$author_id = $row['id'];
    }

	$sql = "UPDATE Articles SET fk_default_author_id = $author_id WHERE "
	     . "Number = $p_article_number AND IdLanguage = $p_language_id";
	return mysql_query($sql);
}

?>
