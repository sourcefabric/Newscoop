<?php
/**
 * This script is for upgrading from 2.1.X to 2.2.0.
 * This will transfer all the images out of the database into the file system.
 * Note: we do NOT want to use the Image class to do the work here, because
 * the user may be upgrading from 2.1.X to something > 2.2, in which case the
 * Image class might be changed since 2.2, which may cause this script to fail.
 * So this script *re-implements* things in the Image class as they were in 2.2.
 *
 */
 
require_once("database_conf.php");
require_once("install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}
$_SERVER['DOCUMENT_ROOT'] = $Campsite['WWW_DIR'] . "/" . $Campsite['DATABASE_NAME'] . "/html";
require_once($Campsite['WWW_COMMON_DIR'] . "/html/configuration.php");

//$db_name = "testcampsite";
//$db_user = "root";
//$db_passwd = "";
//$db_host = "localhost";
//$images_dir = "/home/paul/campsite/images";

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];
$apacheUser = $Campsite['APACHE_USER'];
$apacheGroup = $Campsite['APACHE_GROUP'];
$images_dir = $Campsite['IMAGE_DIRECTORY'];

if (isset($argc) && ($argc > 1)) {
	$usage = "Usage: transfer_images.php [-d <db_name>] [-u <user>]\n"
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

if (!is_dir($images_dir)) {
	die("ERROR! " . $images_dir. " is not a directory.\n");
}

// Cut off trailing slash
if ($images_dir[strlen($images_dir) - 1] == '/') {
	$len = strlen($images_dir) - 1;
	$images_dir = substr($images_dir, 0, $len);
}

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
	die("Unable to connect to the database.\n");
}
if (!mysql_select_db($db_name)) {
	die("Unable to use the database " . $db_name . ".\n");
}
mysql_query("SET NAMES 'utf8'");

transfer_images($images_dir, $apacheUser, $apacheGroup);

// Signal to our parent script that we ran successfully.
$sql = "CREATE TABLE TransferImages(a int)";
mysql_query($sql);
echo "Images transfered successfuly\n";


function transfer_images($p_destDir, $apacheUser, $apacheGroup) {
	// TESTING STUFF
	//	$dropTableQuery = "DROP TABLE IF EXISTS ImagesDup";
//	mysql_query($dropTableQuery);
//	$createTableQuery =
//	"CREATE TABLE IF NOT EXISTS `ImagesDup` (
//    `Id` int(10) unsigned NOT NULL auto_increment,
//    `Description` varchar(255) NOT NULL default '',
//    `Photographer` varchar(255) NOT NULL default '',
//    `Place` varchar(255) NOT NULL default '',
//    `Caption` varchar(255) NOT NULL default '',
//    `Date` date NOT NULL default '0000-00-00',
//    `ContentType` varchar(64) NOT NULL default '',
//    `Location` enum('local','remote') NOT NULL default 'local',
//    `URL` varchar(255) NOT NULL default '',
//    `NrArticle` int(10) unsigned NOT NULL default '0',
//    `Number` int(10) unsigned NOT NULL default '0',
//    `Image` mediumblob NOT NULL,
//    `ThumbnailFileName` varchar(50) NOT NULL default '',
//    `ImageFileName` varchar(50) NOT NULL default '',
//    `UploadedByUser` int(11) default NULL,
//    `LastModified` timestamp(14) NOT NULL,
//    `TimeCreated` timestamp(14) NOT NULL default '00000000000000',
//    PRIMARY KEY (`Id`)
//	)";
//	mysql_query($createTableQuery);
//	$transferQuery = "INSERT INTO ImagesDup (Description, Photographer, Place, Date, ContentType, Location, URL, NrArticle, Number, Image) SELECT Description, Photographer, Place, Date, ContentType, 'local', '', NrArticle, Number, Image FROM Images";
//	mysql_query($transferQuery);
	
	$thumbnailCommand = 'convert -sample 64x64';
	$queryStr = "SELECT Id, Image FROM ImagesDup";
	$query = mysql_query($queryStr);
	//mkdir($p_destDir, 0755);
	while ($row = mysql_fetch_assoc($query)) {
		$imageFileName = 'cms-image-'.sprintf('%09d', $row['Id']);
		$tmpImageFile = '/tmp/'.$imageFileName;
		// Make sure that the file doesnt already exist.
		if (file_exists($tmpImageFile)) {
			@unlink($tmpImageFile);
		}
		$handle = fopen($tmpImageFile, 'a');
		fwrite($handle, $row['Image']);
		fclose($handle);

		// Figure out the image type
		$imageInfo = getimagesize($tmpImageFile);
		switch($imageInfo[2]) {
           case 1: $extension = 'gif'; break;
           case 2: $extension = 'jpg'; break;
           case 3: $extension = 'png'; break;
           case 4: $extension = 'swf'; break;
           case 5: $extension = 'psd'; break;
           case 6: $extension = 'bmp'; break;
           case 7: $extension = 'tiff'; break;
           case 8: $extension = 'tiff'; break;
           case 9: $extension = 'jpc'; break;
           case 10: $extension = 'jp2'; break;
           case 11: $extension = 'jpx'; break;
           case 12: $extension = 'jb2'; break;
           case 13: $extension = 'swc'; break;
           case 14: $extension = 'aiff'; break;
           case 15: $extension = 'wbmp'; break;
           case 16: $extension = 'xbm'; break;
        }
        $destFileName = $imageFileName.'.'.$extension;
		$destFilePath = $p_destDir.'/'.$destFileName;
		copy($tmpImageFile, $destFilePath);
		chown($destFilePath, $apacheUser);
		chgrp($destFilePath, $apacheGroup);
		$contentType = "";
		if (isset($imageInfo["mime"])) {
			$contentType = ", ContentType='".$imageInfo["mime"]."'";
		}
		$queryStr3 = "UPDATE ImagesDup "
			." SET TimeCreated=NULL, "
			." LastModified=NULL, "
			." ImageFileName='".$destFileName."' "
			.$contentType
			." WHERE Id=".$row["Id"];
		mysql_query($queryStr3);
		$thumbnailFileName = 'cms-thumb-'.sprintf('%09d', $row['Id']).'.'.$extension;
		$thumbnailFilePath = $p_destDir.'/thumbnails/'.$thumbnailFileName;
        $cmd = $thumbnailCommand.' '.$destFilePath.' '.$thumbnailFilePath;
        system($cmd);
		chown($thumbnailFilePath, $apacheUser);
		chgrp($thumbnailFilePath, $apacheGroup);
        if (file_exists($thumbnailFilePath)) {
        	chmod($thumbnailFilePath, 0644);
			$queryStr4 = "UPDATE ImagesDup "
				." SET ThumbnailFileName='".$thumbnailFileName."'"
				." WHERE Id=".$row["Id"];
			mysql_query($queryStr4);
        }
	} // while
} // fn transfer_images

?>