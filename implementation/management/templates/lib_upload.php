<?php 

function bufferFilesystemResult($s, $level = 0)
{
	global $FSresult;
	$FSresult .= $s;
}

function eliminLB($v, $enters = "false")
{
	$v = str_replace("&", "&amp;", $v);
	$v = str_replace("<", "&lt;", $v);
	$v = str_replace(">", "&gt;", $v);
	$v = str_replace("\"", "&quot;", $v);
	if ($enters == "true")
		$v = str_replace("\n", "<BR>\n", $v);
	return $v;
}

function printDH($t)
{
	global $debugLevelHigh;
	if ($debugLevelHigh) {
		$t = eliminLB($t);
		print"<P><SPAN class=debugh>$t</SPAN>\n";
	}
}

function printDL($t)
{
	global $debugLevelLow;
	if ($debugLevelLow) {
		$t = eliminLB($t);
		print"<P><SPAN class=debugl>$t</SPAN>\n";
	}
}

function printI($t)
{
	print"<P><SPAN class=info>$t</SPAN>";
}

function doUpload($fileNameStr, $charset, $baseupload, $desiredName = null)
{
	$baseupload = decURL($baseupload);
	$fileName = $GLOBALS["$fileNameStr"];
	printDL("The distant filename:$fileName");

	if (trim($fileName) == "") {
		bufferFilesystemResult('You didn\'t select a file for uploading.',2);
		return false;
	}

	$fninForm = $GLOBALS["$fileNameStr"."_name"];
	printDL("The filename in the form:$fninForm");

	$dotpos = strrpos($fninForm, ".");
	$name = substr ($fninForm, 0, $dotpos);
	$ext = substr ($fninForm, $dotpos + 1);

	if ($desiredName != "")
		$fninForm = "$desiredName.$ext";

	// strip out the &, because when transmitting filename list over the todolist,
	// the & sign will be interpreted as separator, and this will destroy the
	// consistency of the todolist
	$fninForm = str_replace('&', '', $fninForm);
	$newname = "$baseupload/".$fninForm;
	printDL("Moving from: $fileName to $newname");
	if(file_exists($newname) && !is_dir($newname)) {
		unlink($newname);
		bufferFilesystemResult(getGS("File $1 already exists. Old version deleted !", $fninForm)."<br>", 1);
	}

	$origFile = $newname.".orig";
	$renok = move_uploaded_file($fileName, $origFile);
	printDL("Moving result:$renok");
	if ($renok == false){
		bufferFilesystemResult(getGS("File $1 already exists.", $fninForm), 1);
		return false;
	}

	$fType = $GLOBALS["$fileNameStr"."_type"];
	printDH("MIME Type: $fType");
	if (strncmp($fType, "text", 4) == 0)
	{
		$command = "iconv -f $charset -t UTF-8 \"$origFile\" > \"$newname\"";
		printDH("Command: $command");
		$res_out = system($command, $status);
		unlink($origFile);
		printDL("Converting from $charset to UTF-8: $status");
		if ($status != 0) {
			unlink($newname);
			bufferFilesystemResult(getGS("Error converting the template to UTF-8 charset.", $fninForm));
			return false;
		}
	} else {
		rename($origFile, $newname);
	}
	bufferFilesystemResult(getGS("The upload of $1 was successful !", $fninForm));

	return true;
}

?>
