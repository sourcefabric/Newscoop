<?php

global $DEBUG;

$DEBUG = false;


/**
 * Send the request message to the template engine and print the result to output.
 *
 * @return void
 */
function camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies)
{
	$msg = camp_create_url_request_message($p_env_vars, $p_parameters, $p_cookies);
	for ($i = 1; $i <= 10; $i++) {
		$size_read = camp_read_parser_output(camp_send_message_to_parser($msg));
		if ($size_read > 0) {
			break;
		}
		usleep(200000);
	}
}


/**
 * Start the campsite parser daemon.
 *
 * @return void
 */
function camp_start_parser()
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
} // fn camp_start_parser


/**
 * Stop the campsite parser daemon.
 *
 * @return void
 */
function camp_stop_parser()
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
} // camp_stop_parser


/**
 * Send a message to the campsite parser.  Note to third-party developers:
 * use the ParserCom class for this.  These will be merged together in the
 * future.
 *
 * @param string $p_msg
 * @param boolean $p_closeSocket
 * @return mixed
 * 		If $p_closeSocket is TRUE, return NULL, if it is FALSE, return
 * 		a socket.
 */
function camp_send_message_to_parser($p_msg, $p_closeSocket = false)
{
	global $Campsite;

	camp_debug_msg("URL request message:");
	camp_debug_msg("<pre>\n" . htmlspecialchars($p_msg) . "</pre>", false);

	$size = sprintf("%04x", strlen($p_msg));
	camp_debug_msg("size: " . $size);
	camp_debug_msg("parser port: " . $Campsite['PARSER_PORT']);

	$errno = 0;
	$errstr = "";
	for ($i = 1; $i <= 3; $i++) {
		@$socket = fsockopen('127.0.0.1', $Campsite['PARSER_PORT'], $errno, $errstr, 30);
		if (!$socket) {
			camp_start_parser();
			usleep(100000);
		} else {
			camp_debug_msg("OK.");
		}
	}
	if (!$socket) {
		exit(0);
	}
	$final_msg = "0001 $size $p_msg";
	camp_debug_msg("final msg size: " . strlen($final_msg));
	$size_wrote = fwrite($socket, $final_msg);
	camp_debug_msg("wrote: $size_wrote");

	if ($p_closeSocket) {
		fclose($socket);
		return NULL;
	}
	return $socket;
} // fn camp_send_message_to_parser


/**
 * Read a response from the parser and discard it.
 *
 * @param socket $p_socket
 * @return int
 * 		The number of bytes read.
 */
function camp_read_parser_output($p_socket)
{
	$size_read = 0;
	stream_set_timeout($p_socket, 10);
	do {
		$str = fread($p_socket, 1000);
		$size_read += strlen($str);
		echo $str;
	} while ($str != "");
	fclose($p_socket);
	camp_debug_msg("size read: $size_read");
	return $size_read;
} // fn camp_read_parser_output


function camp_xmlescape($p_message)
{
	return htmlspecialchars($p_message);
} // fn camp_xmlescape


/**
 * Send a request for a web page to the parser.
 *
 * @param array $p_envVars
 * @param array $p_parameters
 * @param array $p_cookies
 * @return unknown
 */
function camp_create_url_request_message($p_envVars, $p_parameters, $p_cookies)
{
	camp_debug_msg("parameters:");
	foreach ($p_parameters as $name => $value) {
		camp_debug_msg("&nbsp;&nbsp;$name = $value");
	}
	camp_debug_msg("cookies:");
	foreach ($p_cookies as $name => $value) {
		camp_debug_msg("&nbsp;&nbsp;$name = $value");
	}

	$msg = "<CampsiteMessage MessageType=\"URLRequest\">\n";
	$msg .= "\t<HTTPHost>" . camp_xmlescape($p_envVars['HTTP_HOST']) . "</HTTPHost>\n";
	$msg .= "\t<DocumentRoot>" . camp_xmlescape($p_envVars['DOCUMENT_ROOT']) . "</DocumentRoot>\n";
	$msg .= "\t<RemoteAddress>" . camp_xmlescape($p_envVars['REMOTE_ADDR']) . "</RemoteAddress>\n";
	$msg .= "\t<PathTranslated>" . camp_xmlescape($p_envVars['PATH_TRANSLATED']) . "</PathTranslated>\n";
	$msg .= "\t<RequestMethod>" . camp_xmlescape($p_envVars['REQUEST_METHOD']) . "</RequestMethod>\n";
	$msg .= "\t<RequestURI>" . camp_xmlescape($p_envVars['REQUEST_URI']) . "</RequestURI>\n";
	$msg .= "\t<ServerPort>" . camp_xmlescape($p_envVars['SERVER_PORT']) . "</ServerPort>\n";
	$msg .= "\t<Parameters>\n";
	foreach ($p_parameters as $param_name=>$param_value) {
		if (is_array($param_value)) {
			foreach ($param_value as $list_param_value) {
				$msg .= "\t\t<Parameter Name=\"" . camp_xmlescape($param_name)
					. "\" Type=\"string\">" . camp_xmlescape($list_param_value) . "</Parameter>\n";
			}
		} else {
			$msg .= "\t\t<Parameter Name=\"" . camp_xmlescape($param_name)
				. "\" Type=\"string\">" . camp_xmlescape($param_value) . "</Parameter>\n";
		}
	}
	$msg .= "\t</Parameters>\n";
	$msg .= "\t<Cookies>\n";
	foreach ($p_cookies as $cookie => $value) {
		if ($value != "") {
			$msg .= "\t\t<Cookie Name=\"" . camp_xmlescape($cookie) . "\">"
			. camp_xmlescape($value) . "</Cookie>\n";
		}
	}
	$msg .= "\t</Cookies>\n";
	$msg .= "</CampsiteMessage>\n";
	return $msg;
} // fn camp_create_url_request_message


/**
 * @param string $p_queryString
 */
function camp_read_parameters(&$p_queryString)
{
	switch (getenv("REQUEST_METHOD")) {
	case "GET":
		return camp_read_get_parameters($p_queryString);
		break;
	case "POST":
		return camp_read_post_parameters($p_queryString);
		break;
	default:
		echo "<p>Unable to process " . getenv("REQUEST_METHOD") . " request method</p>";
		exit(0);
	}
} // fn camp_read_parameters


/**
 * @param string $p_queryString
 */
function camp_read_get_parameters(&$p_queryString)
{
	if ($p_queryString == "") {
		$p_queryString = getenv("QUERY_STRING");
	}
	$parameters = array();
	$pairs = explode("&", $p_queryString);
	foreach ($pairs as $pair) {
		$pair_array = explode("=", $pair);
		$paramName = trim($pair_array[0]);
		$paramValue = trim(isset($pair_array[1]) ? $pair_array[1] : '');
		if ($paramName == "") {
			continue;
		}
		if (isset($parameters[$paramName])) {
			if (is_array($parameters[$paramName])) {
				$parameters[$paramName][] = $paramValue;
			} else {
				$parameters[$paramName] = array($parameters[$paramName], urldecode($paramValue));
			}
			continue;
		}
		$parameters[$paramName] = $paramValue;
	}
	return $parameters;
} // fn camp_read_get_parameters


/**
 * @param string $p_queryString
 */
function camp_read_post_parameters(&$p_queryString)
{
	global $_POST;
	$query_string = file_get_contents("php://stdin");
	if (trim($query_string) == "" && isset($_POST) && is_array($_POST)) {
		return $_POST;
	}
	camp_debug_msg("query string: $query_string");
	return camp_read_get_parameters($query_string);
} // fn camp_read_post_parameters


/**
 * @param string $p_cookiesString
 */
function camp_read_cookies(&$p_cookiesString)
{
	if ($p_cookiesString == "") {
		$p_cookiesString = getenv("HTTP_COOKIE");
	}
	$cookies = array();
	$pairs = explode(";", $p_cookiesString);
	foreach ($pairs as $pair) {
		$pair_array = explode("=", $pair);
		if (trim($pair_array[0]) != "") {
			$cookies[trim($pair_array[0])] = trim($pair_array[1]);
		}
	}
	return $cookies;
} // fn camp_read_cookies


/**
 * TODO: merge this into the Language class.
 *
 * @param unknown_type $p_document_root
 */
function camp_create_language_links($p_document_root = "")
{
	global $Campsite;

	$document_root = $p_document_root != "" ? $p_document_root : $_SERVER['DOCUMENT_ROOT'];
	require_once("$document_root/database_conf.php");

	// connect to database to read the image file name
	if (!mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'], $Campsite['DATABASE_PASSWORD'])) {
		exit(0);
	}
	if (!mysql_select_db($Campsite['DATABASE_NAME'])) {
		exit(0);
	}

	if (!$res = mysql_query("select Code from Languages")) {
		exit(0);
	}
	$index_file = "$document_root/index.php";
	while (($row = mysql_fetch_array($res)) != null) {
		$lang_code = $row["Code"];
		$link = "$document_root/$lang_code.php";
		if (file_exists($link) && !is_link($link)) {
			unlink($link);
		}
		if (!is_link($link)) {
			symlink($index_file, $link);
		}
		chown($link, $Campsite['APACHE_USER']);
		chgrp($link, $Campsite['APACHE_GROUP']);
	}
} // fn camp_create_language_links


function camp_debug_msg($msg, $format_html = true)
{
	global $DEBUG;

	if (!$DEBUG) {
		return;
	}

	if ($format_html) {
		echo "<p>";
	}

	echo $msg;
	if ($format_html) {
		echo "</p>\n";
	}
} // fn camp_debug_msg

?>