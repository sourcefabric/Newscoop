<?php

//echo "Content-type: text/html\n\n";

global $_SERVER;
global $Campsite;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");
$query_string = getenv("QUERY_STRING");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');

// read parameters
$parameters = array();
$pairs = explode("&", $query_string);
foreach ($pairs as $index=>$pair) {
	$pair_array = explode("=", $pair);
	$parameters[$pair_array[0]] = $pair_array[1];
}

$articleNr = $parameters["NrArticle"];
$imageNr = $parameters["NrImage"];

// connect to database to read the image file name
if (!mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'],
	               $Campsite['DATABASE_PASSWORD']))
	exit(0);
if (!mysql_select_db($Campsite['DATABASE_NAME']))
	exit(0);

$sql = "select ImageFileName, ContentType from ArticleImages as ai left join Images as i"
     . " on ai.IdImage = i.Id where ai.NrArticle = $articleNr and Number = $imageNr";
$res = mysql_query($sql);
if (!$res)
	exit(0);
if (!$row = mysql_fetch_array($res))
	exit(0);
$imageFileName = $row["ImageFileName"];
$contentType = $row["ContentType"];
$imageFilePath = $Campsite['HTML_DIR'] . "/images";

// Expire one day from now.
$secondsTillExpired = 86400;
$currentTime = time();
$expireTime = $currentTime + $secondsTillExpired;

// echo "<p>article: $articleNr</p>\n";
// echo "<p>image: $imageNr</p>\n";
// echo "<p>image file name: $imageFileName</p>\n";
// echo "<p>image file path: $imageFilePath</p>\n";

// send image headers
echo "Content-type: $contentType\n";
echo "Expires: " . gmdate("D, d M Y H:i:s", $expireTime) . " GMT\n";
echo "Last-Modified: " . gmdate("D, d M Y H:i:s", $currentTime) . " GMT\n";
echo "Cache-Control: private, max-age=" . $secondsTillExpired . ", must-revalidate, pre-check=" . $secondsTillExpired . "\n\n";

// send the image file
readfile("$imageFilePath/$imageFileName");

?>