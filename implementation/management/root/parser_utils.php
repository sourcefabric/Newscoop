<?php

function send_message_to_parser($msg, $close_socket = false)
{
	global $Campsite;

	$size = sprintf("%04x", strlen($msg));
// 	echo "size: " . $size . "\n";
// 	echo "<p>parser port: " . $Campsite['PARSER_PORT'] . "</p>\n";

	@$socket = fsockopen('127.0.0.1', $Campsite['PARSER_PORT'], $errno, $errstr, 30);
	if (!$socket) {
		die("$errstr ($errno)\n");
	} else {
		// echo "OK.\n</pre>\n";
	}
	$final_msg = "0001 $size $msg";
// 	echo "<p>final msg size: " . strlen($final_msg) . "</p>\n";
	$size_wrote = fwrite($socket, $final_msg);
// 	echo "<p>wrote: $size_wrote</p>\n";

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
	return $size_read;
}

function xmlescape($message)
{
	return htmlspecialchars($message);
}

function create_url_request_message($env_vars, $parameters, $cookies)
{
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
		return read_get_parameters(&$query_string);
		break;
	case "POST":
		return read_post_parameters(&$query_string);
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
	foreach ($pairs as $index=>$pair) {
		$pair_array = explode("=", $pair);
		if (trim($pair_array[0]) != "")
			$parameters[trim($pair_array[0])] = trim($pair_array[1]);
	}
	return $parameters;
}

function read_post_parameters(&$query_string)
{
	$query_string = file_get_contents("php://stdin");
	return read_get_parameters($query_string);
}

function read_cookies(&$cookies_string)
{
	if ($cookies_string == "")
		$cookies_string = getenv("HTTP_COOKIE");
	$cookies = array();
	$pairs = explode(";", $cookies_string);
	foreach ($pairs as $index=>$pair) {
		$pair_array = explode("=", $pair);
		if (trim($pair_array[0]) != "")
			$cookies[trim($pair_array[0])] = trim($pair_array[1]);
	}
	return $cookies;
}

?>