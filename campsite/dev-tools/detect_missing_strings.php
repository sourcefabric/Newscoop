<?php

global $gStrings, $gLangCode, $gErrors;

$gStrings = array();
$gLangCode = "en";
$gErrors = array();

if (($startDir = $GLOBALS['argv'][1]) == "")
	exitWithError("Start directory not specified");
if (!is_dir($startDir))
	exitWithError("$startDir is not a valid directory");


processDirScripts($startDir, $gLangCode, $gErrors);
if (sizeof($gErrors) > 0) {
	printMsg("There were the following errors:");
	foreach ($gErrors as $index=>$error)
		printMsg($error, 1);
}


//         end of main
// *****************************************************
//    functions implementation


function processDirScripts($startDir, $gLangCode, $gErrors, $rootDir = "")
{
	global $gStrings, $gLangCode;

	if (!($dirH = opendir($startDir))) {
		$errors[] = "Unable to open directory $startDir";
		return;
	}
	if ($rootDir == "")
		$rootDir = $startDir;

	if (is_file("$startDir/locals.$langCode.php")) {
		$gStrings = array();
		require_once("$rootDir/globals.$langCode.php");
		require_once("$startDir/locals.$langCode.php");
	}

	$dirs = array();
	while (false !== ($file = readdir($dirH))) {
		if ($file == "." || $file == ".." || $file == "CVS")
			continue;
		if (is_dir("$startDir/$file")) {
			$dirs[] = $file;
			continue;
		}
		if (($dotPos = strrpos($file, ".")) === false)
			continue;
		$extension = substr($file, $dotPos + 1);
		if ($extension != "m4" && $extension != "php")
			continue;
		if (strncmp($file, "locals.", strlen("locals.")) == 0 && $extension == "php")
			continue;

		$missingStrings = processDirScript("$startDir/$file", $langCode, $errors);
		printMissingStrings($missingStrings);
	}
	closedir($dirH);

	if (sizeof($stringMatch) == 0)
		return;
	print_msg("Usage for directory $startDir");
	foreach ($stringMatch as $key=>$useCount)
		print_msg("$key:$langCode - $useCount", 1);
}


function regGS($key, $value)
{
	global $gStrings, $gLangCode;

	if (isset($gStrings[$key])) {
		if ($key != '') {
			return false;
		}
	} else {
		if (substr($value, strlen($value) - strlen(":$gLangCode")) == ":$gLangCode") {
			$value = substr($value, 0, strlen($value) - strlen(":$gLangCode"));
		}
		$gStrings[$key] = $value;
	}
	return true;
}


function exitWithError($error_str)
{
	if (is_array($error_str))
		$error_str = implode("\n", $error_str);
	echo "$error_str\n";
	exit(1);
}


function printMsg($msg, $indent = 0, $newLine = true)
{
	for ($i = 0; $i < $indent; $i++)
		echo "\t";
	echo $msg;
	if ($newLine)
		echo "\n";
}

?>
