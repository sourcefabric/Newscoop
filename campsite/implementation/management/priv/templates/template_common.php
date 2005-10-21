<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("templates");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/User.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Template.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

function camp_template_path_crumbs($p_path) 
{
	global $ADMIN;
	$crumbs = array();
	$crumbs[] = array(getGS("Path").": ", "", false);
	$p_path = str_replace("//", "/", $p_path);
	if ($p_path == "/") {
		$crumbs[] = array("/", "/$ADMIN/templates/?Path=/");
		return $crumbs;
	}
	$dirs = split("/", $p_path);
	//echo "<pre>";print_r($dirs);echo "</pre>";
	$tmpPath = "";
	$numDirs = count($dirs);
	$count = 1;
	foreach ($dirs as $dir) {
		if ($dir == "") {
			$tmpPath = '/';
		} elseif ($tmpPath == '/') {
			$tmpPath .= $dir;
		} else {
			$tmpPath .= "/$dir";
		}
		$crumbs[] = array("$dir/", "/$ADMIN/templates/?Path=".urlencode($tmpPath), ($count++ == $numDirs));
	}
	return $crumbs;
}

?>