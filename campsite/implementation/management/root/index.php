<?php

global $_SERVER;
global $Campsite;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');

// read server parameters
$env_vars["HTTP_HOST"] = getenv("HTTP_HOST");
$env_vars["DOCUMENT_ROOT"] = getenv("DOCUMENT_ROOT");
$env_vars["REMOTE_ADDR"] = getenv("REMOTE_ADDR");
$env_vars["PATH_TRANSLATED"] = getenv("PATH_TRANSLATED");
$env_vars["REQUEST_METHOD"] = getenv("REQUEST_METHOD");
$env_vars["REQUEST_URI"] = getenv("REQUEST_URI");

// read parameters
$parameters = read_parameters($query_string);
$cookies = read_cookies($cookies_string);

// echo "<p>request method: " . getenv("REQUEST_METHOD") . "</p>\n";
// echo "<p>query string: $query_string</p>\n";
// echo "<p>cookies string: $cookies_string</p>\n";
// echo "<p>parameters:</p>\n";
// foreach ($parameters as $name=>$value)
// 	echo "<p>&nbsp;&nbsp;$name = $value</p>\n";
// echo "<p>cookies:</p>\n";
// foreach ($cookies as $name=>$value)
// 	echo "<p>&nbsp;&nbsp;$name = $value</p>\n";

$msg = create_url_request_message($env_vars, $parameters, $cookies);
// echo "<p>URL request message:</p>\n";
// echo "<pre>\n" . htmlspecialchars($msg) . "\n";

$socket = send_message_to_parser($msg);
$size_read = read_parser_output($socket);
// echo "<p>size read: $size_read</p>\n";

?>