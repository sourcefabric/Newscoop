<?php
camp_load_translation_strings("templates");
require_once($GLOBALS['g_campsiteDir']."/classes/Language.php");
require_once($GLOBALS['g_campsiteDir']."/classes/User.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Template.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Log.php");
require_once($GLOBALS['g_campsiteDir']."/classes/FileTextSearch.php");

function camp_get_text_extensions()
{
	return array('tpl','php','htm','html','php3','php4','txt','css',
				 'xml','asp','py','java');
}


function camp_is_text_file($p_fileName)
{
	$extension = strtolower(pathinfo($p_fileName, PATHINFO_EXTENSION));
	return in_array($extension, camp_get_text_extensions());
} // fn camp_is_text_file


function camp_get_image_extensions()
{
	return array('jpg','jpe','jpeg','png','gif','tif','tiff');
}


function camp_is_image_file($p_fileName)
{
	$extension = strtolower(pathinfo($p_fileName, PATHINFO_EXTENSION));
	return in_array($extension, camp_get_image_extensions());
} // fn camp_is_image_file


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
	$dirs = preg_split("/", $p_path);
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
} // fn camp_template_path_crumbs

?>
