<?php

global $gStrings, $gLangCode, $gErrors;

$gStrings = array();
$gLangCode = "en";
$gErrors = array();

if (($startDir = $GLOBALS['argv'][1]) == "")
	exitWithError("Start directory not specified");
if (!is_dir($startDir))
	exitWithError("$startDir is not a valid directory");


processDirTranslation($startDir, $gLangCode, $gErrors);
if (sizeof($gErrors) > 0) {
	printMsg("There were the following errors:");
	foreach ($gErrors as $index=>$error)
		printMsg($error, 1);
}


//         end of main
// *****************************************************
//    functions implementation


function searchTranslationStrings($fileName, $strings, &$stringsMatch)
{
	$fileContent = file_get_contents($fileName);
	foreach ($strings as $key=>$value) {
		$found = 0;
		$found += substr_count($fileContent, "<*$key*>");
		$found += substr_count($fileContent, "\"$key\"");
		$found += substr_count($fileContent, "'$key'");
		$key = addslashes($key);
		$found += substr_count($fileContent, "<*$key*>");
		$found += substr_count($fileContent, "\"$key\"");
		$found += substr_count($fileContent, "'$key'");
		$stringsMatch[$key] = $found + $stringsMatch[$key];
	}
}


function searchStringsInScripts($dirName, $dirH, $strings)
{
	$stringsMatch = array();

	rewinddir($dirH);
	while (false !== ($file = readdir($dirH))) {
		if ($file == "." || $file == ".." || is_dir("$dirName/$file") || $file == "CVS")
			continue;
		if (($dotPos = strrpos($file, ".")) === false)
			continue;
		$extension = substr($file, $dotPos + 1);
		if ($extension != "m4" && $extension != "php")
			continue;
		if (strncmp($file, "locals.", strlen("locals.")) == 0 && $extension == "php")
			continue;

		searchTranslationStrings("$dirName/$file", $strings, $stringsMatch);
	}

	return $stringsMatch;
}


function printStringUsage($target, $stringsMatch, $defUsageCount = null)
{
	$header = false;
	foreach ($stringsMatch as $string=>$usageCount) {
		if (($defUsageCount !== null && $usageCount == $defUsageCount)
			|| $defUsageCount === null) {
			if (!$header) {
				$header = true;
				printMsg("Translation strings usage count for $target");
			}
			printMsg("$string :: $usageCount", 1);
		}
	}
}


function processDirTranslation($startDir, $langCode, &$errors, $rootDir = "")
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
		require_once("$startDir/locals.$langCode.php");
		$stringsMatch = searchStringsInScripts($startDir, $dirH, $gStrings);
		$localDir = substr($startDir, strlen($rootDir) + 1);
		printStringUsage("$localDir/locals.$langCode.php", $stringsMatch, 0);
	}

	rewinddir($dirH);
	while (false !== ($file = readdir($dirH))) {
		if ($file == "." || $file == ".." || !is_dir("$startDir/$file") || $file == "CVS")
			continue;
		processDirTranslation("$startDir/$file", $langCode, $errors, $rootDir);
	}
	closedir($dirH);
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
