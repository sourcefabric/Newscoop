<?php

global $DEBUG;

$DEBUG = false;

function start_parser()
{
	global $Campsite;
	
	$binFile = $Campsite['BIN_DIR'] . "/campsite_server";
	$args = " -i " . $Campsite['DATABASE_NAME'];
	if (!file_exists($binFile)) {
		$p_output[] = "Can't find the campsite_server binary; please check your Campsite install.";
		return -1;
	}
	$childOutput = popen("$binFile$args", "r");
	usleep(300000);
	pclose($childOutput);
}

function stop_parser()
{
	global $Campsite;
	
	$instanceName = $Campsite['DATABASE_NAME'];
	$cmd = "ps -o pid=pid,cmd=command -C campsite_server";
	exec($cmd, $output, $returnValue);
	foreach ($output as $line) {
		$line = trim($line);
		if (strncmp($line, "pid", 3) == 0) {
			continue;
		}
		$elements = explode(" ", $line);
		$elements = array_map('trim', $elements);
		for ($i = 0; $i < sizeof($elements); $i++) {
			if ($i == 0) {
				$processId = $elements[$i];
			}
			if ($elements[$i] == '-i') {
				$currentInstance = $elements[$i + 1];
				if ($instanceName == $currentInstance) {
					return posix_kill($processId, 15);
				}
				break;
			}
		}
	}
	return true;
}

function send_message_to_parser($msg, $close_socket = false)
{
	global $Campsite;

	debug_msg("URL request message:");
	debug_msg("<pre>\n" . htmlspecialchars($msg) . "</pre>", false);

	$size = sprintf("%04x", strlen($msg));
	debug_msg("size: " . $size);
	debug_msg("parser port: " . $Campsite['PARSER_PORT']);

	$errno = 0;
	$errstr = "";
	for ($i = 1; $i <= 3; $i++) {
		@$socket = fsockopen('127.0.0.1', $Campsite['PARSER_PORT'], $errno, $errstr, 30);
		if (!$socket) {
			start_parser();
			usleep(100000);
		} else {
			debug_msg("OK.");
		}
	}
	if (!$socket) {
		exit(0);
	}
	$final_msg = "0001 $size $msg";
	debug_msg("final msg size: " . strlen($final_msg));
	$size_wrote = fwrite($socket, $final_msg);
	debug_msg("wrote: $size_wrote");

	if ($close_socket) {
		fclose($socket);
		return NULL;
	}
	return $socket;
}

function read_parser_output($socket)
{
	$size_read = 0;
	stream_set_timeout($socket, 10);
	do {
		$str = fread($socket, 1000);
		$size_read += strlen($str);
		echo $str;
	} while ($str != "");
	fclose($socket);
	debug_msg("size read: $size_read");
	return $size_read;
}

function xmlescape($message)
{
	return htmlspecialchars($message);
}

function create_url_request_message($env_vars, $parameters, $cookies)
{
	debug_msg("parameters:");
	foreach ($parameters as $name=>$value)
		debug_msg("&nbsp;&nbsp;$name = $value");
	debug_msg("cookies:");
	foreach ($cookies as $name=>$value)
		debug_msg("&nbsp;&nbsp;$name = $value");

	$msg = "<CampsiteMessage MessageType=\"URLRequest\">\n";
	$msg .= "\t<HTTPHost>" . xmlescape($env_vars['HTTP_HOST']) . "</HTTPHost>\n";
	$msg .= "\t<DocumentRoot>" . xmlescape($env_vars['DOCUMENT_ROOT']) . "</DocumentRoot>\n";
	$msg .= "\t<RemoteAddress>" . xmlescape($env_vars['REMOTE_ADDR']) . "</RemoteAddress>\n";
	$msg .= "\t<PathTranslated>" . xmlescape($env_vars['PATH_TRANSLATED']) . "</PathTranslated>\n";
	$msg .= "\t<RequestMethod>" . xmlescape($env_vars['REQUEST_METHOD']) . "</RequestMethod>\n";
	$msg .= "\t<RequestURI>" . xmlescape($env_vars['REQUEST_URI']) . "</RequestURI>\n";
	$msg .= "\t<Parameters>\n";
	foreach ($parameters as $param_name=>$param_value)
		$msg .= "\t\t<Parameter Name=\"" . xmlescape($param_name)
			. "\" Type=\"string\">" . xmlescape($param_value) . "</Parameter>\n";
	$msg .= "\t</Parameters>\n";
	$msg .= "\t<Cookies>\n";
	foreach ($cookies as $cookie=>$value)
		if ($value != "")
			$msg .= "\t\t<Cookie Name=\"" . xmlescape($cookie) . "\">"
			. xmlescape($value) . "</Cookie>\n";
	$msg .= "\t</Cookies>\n";
	$msg .= "</CampsiteMessage>\n";
	return $msg;
}

function read_parameters(&$query_string)
{
	switch (getenv("REQUEST_METHOD")) {
	case "GET":
		return read_get_parameters($query_string);
		break;
	case "POST":
		return read_post_parameters($query_string);
		break;
	default:
		echo "<p>Unable to process " . getenv("REQUEST_METHOD") . " request method</p>";
		exit(0);
	}
}

function read_get_parameters(&$query_string)
{
	if ($query_string == "")
		$query_string = getenv("QUERY_STRING");
	$parameters = array();
	$pairs = explode("&", $query_string);
	foreach ($pairs as $pair) {
		$pair_array = explode("=", $pair);
		if (trim($pair_array[0]) != "")
			$parameters[trim($pair_array[0])] = urldecode(trim($pair_array[1]));
	}
	return $parameters;
}

function read_post_parameters(&$query_string)
{
	if (is_array($_POST))
		return $_POST;
	$query_string = file_get_contents("php://stdin");
	debug_msg("query string: $query_string");
	return read_get_parameters($query_string);
}

function read_cookies(&$cookies_string)
{
	if ($cookies_string == "")
		$cookies_string = getenv("HTTP_COOKIE");
	$cookies = array();
	$pairs = explode(";", $cookies_string);
	foreach ($pairs as $pair) {
		$pair_array = explode("=", $pair);
		if (trim($pair_array[0]) != "")
			$cookies[trim($pair_array[0])] = trim($pair_array[1]);
	}
	return $cookies;
}

function create_language_links($p_document_root = "")
{
	global $Campsite;

	$document_root = $p_document_root != "" ? $p_document_root : $_SERVER['DOCUMENT_ROOT'];
	require_once("$document_root/database_conf.php");

	// connect to database to read the image file name
	if (!mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'],
					$Campsite['DATABASE_PASSWORD']))
		exit(0);
	if (!mysql_select_db($Campsite['DATABASE_NAME']))
		exit(0);

	if (!$res = mysql_query("select Code from Languages"))
		exit(0);
	$index_file = "$document_root/index.php";
	while (($row = mysql_fetch_array($res)) != null) {
		$lang_code = $row["Code"];
		$link = "$document_root/$lang_code.php";
		if (file_exists($link) && !is_link($link))
			unlink($link);
		if (!is_link($link))
			symlink($index_file, $link);
		chown($link, $Campsite['APACHE_USER']);
		chgrp($link, $Campsite['APACHE_GROUP']);
	}
}

function debug_msg($msg, $format_html = true)
{
	global $DEBUG;

	if (!$DEBUG)
		return;

	if ($format_html)
		echo "<p>";
	echo $msg;
	if ($format_html)
		echo "</p>\n";
}

?>