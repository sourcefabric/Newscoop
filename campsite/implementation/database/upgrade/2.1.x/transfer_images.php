<?php

require_once("database_conf.php");
require_once("install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];
$images_dir = $Campsite['WWW_DIR'] . "/$db_name/html/images";
$classes_dir = $Campsite['WWW_DIR'] . "/$db_name/html/classes";

// include Image class definition
require_once("$classes_dir/Image.php");

if ($argc > 1) {
	$usage = "Usage: transfer_templates.php [-d <db_name>] [-u <user>]\n"
		. "\t[-p <passwd>] [-h <host>] [-i <images_dir>]\n";
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
			case '-i':
				$i++;
				$images_dir = $argv[$i];
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

transfer_images($images_dir);
$sql = "CREATE TABLE TransferImages(a int)";
mysql_query($sql);
echo "Images transfered successfuly\n";


function transfer_images($dst_dir)
{
// *********************************************************************
//
// Following is the code from the old shell script; the idea is to dump
// the image into a file using "select Image into dumpfile"
//
// *********************************************************************
// mcommand="$mclient -u $muser -h $mserver -N"
// if [ "$mpasswd" != "" ]; then
// 	mcommand="$mcommand -p=\"$mpasswd\""
// fi
// mcommand="$mcommand $dbname"
// 
// sql="select Id from ImagesDup"
// ids=`$mcommand -e "$sql"`
// 
// mkdir -p "$image_dir"
// for id in $ids; do
// 	image_file="/tmp/cms-image-$id"
// 	rm -f "$image_file"
// 	sql="select Image into dumpfile '$image_file' from ImagesDup where Id = $id"
// 	$mcommand -e "$sql"
// 	mv -f "$image_file" "$image_dir"
// done
// $mcommand -e "create table TransferImages(a int)"

}

?>
