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


function processDirScript($file, $langCode, &$missingStrings, &$errors)
{
	global $gStrings, $gLangCode;

	$fileContent = file_get_contents($file);
	$funcNames = array("getGS", "putGS", "DisplayError");
	foreach ($funcNames as $index=>$func) {
		$text = $fileContent;
		while (true) {
			if (($getPos = strpos($text, $func)) === false) {
				break;
			}
			$text = substr($text, $getPos + strlen($func));
			if (($endBracket = strpos($text, ")")) === false)
				continue;

			// remove end bracket from temporary message string
			$msg = substr($text, 0, $endBracket);
			// copy remaining text
			$text = substr($text, $endBracket + 1);
			// search for first quote/double-quote
			$quotePos = strcspn($msg, "\"'");
			$quote = $msg[$quotePos]; // remember if it was quote or double-quote
			$msg = substr($msg, 1 + $quotePos);
			if ($msg == "")
				continue; // if not found continue

			$endMsg = false;
			do {
				// search for next (double-)quote (maybe the end of string?)
				$tmpMsg = ($endMsg !== false) ? substr($msg, $endMsg) : $msg;
				if (($nextEndMsg = strpos($tmpMsg, $quote)) === false)
					break; // if not found exit this loop, continue in parent loop

				// set the end of message to the position of (double-)quote
				$nextEndMsg += (int)$endMsg;
				$endMsg = $nextEndMsg;

				// if the (double-)quote was not escaped exit the loop
				if ($msg[$endMsg - 1] != "\\")
					break;
				$endMsg++; // set the end of the string past the last (double-)quote
			} while (true);
			if ($endMsg === false)
				continue; // if end of message not set continue

			$msg = stripslashes(substr($msg, 0, $endMsg));
			if (!array_key_exists($msg, $gStrings) && !in_array($msg, $missingStrings))
				$missingStrings[] = $msg;
		}
	}
}


function printMissingStrings($target, $missingStrings)
{
	$header = false;
	foreach ($missingStrings as $index=>$string) {
		if (!$header) {
			$header = true;
			printMsg("Missing strings in $target");
		}
		printMsg($string, 1);
	}
}


function processDirScripts($startDir, $gLangCode, $gErrors, $rootDir = "")
{
	global $gStrings, $gLangCode;

	if (!($dirH = opendir($startDir))) {
		$errors[] = "Unable to open directory $startDir";
		return;
	}
	if ($rootDir == "") {
		$rootDir = $startDir;
		require_once("$rootDir/globals.$gLangCode.php");
	}

	if (is_file("$startDir/locals.$gLangCode.php")) {
		require_once("$startDir/locals.$gLangCode.php");
	}

	$dirs = array();
	$missingStrings = array();
	while (false !== ($file = readdir($dirH))) {
		if ($file == "." || $file == ".." || $file == "CVS" || $file == "lib_campsite.php")
			continue;
		if (is_dir("$startDir/$file")) {
			$dirs[] = $file;
			continue;
		}
		if (($dotPos = strrpos($file, ".")) === false)
			continue;
		if (($extension = substr($file, $dotPos + 1)) != "php")
			continue;
		if (strncmp($file, "locals.", strlen("locals.")) == 0)
			continue;
		if (strncmp($file, "globals.", strlen("globals.")) == 0)
			continue;

		if (is_file("$startDir/locals.$gLangCode.php"))
			processDirScript("$startDir/$file", $gLangCode, $missingStrings, $errors);
	}
	closedir($dirH);
	$localDir = substr($startDir, strlen($rootDir) + 1);
	if ($localDir == "")
		$localDir = "/";
	printMissingStrings($localDir, $missingStrings);

	foreach ($dirs as $index=>$dirName)
		processDirScripts("$startDir/$dirName", $gLangCode, $gErrors, $rootDir);
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
