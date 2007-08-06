<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');

// read server parameters
$env_vars["HTTP_HOST"] = getenv("HTTP_HOST");
$env_vars["DOCUMENT_ROOT"] = getenv("DOCUMENT_ROOT");
$env_vars["REMOTE_ADDR"] = getenv("REMOTE_ADDR");
$env_vars["PATH_TRANSLATED"] = getenv("PATH_TRANSLATED");
$env_vars["REQUEST_METHOD"] = getenv("REQUEST_METHOD");
$env_vars["REQUEST_URI"] = getenv("REQUEST_URI");
$env_vars["SERVER_PORT"] = trim(getenv("SERVER_PORT"));
if ($env_vars["SERVER_PORT"] == "") {
    $env_vars["SERVER_PORT"] = 80;
}

// read parameters
// do we need to decode the parameter values?
// if run as CGI yes, otherwise no
$g_decodeURL = isset($argc) && $argc > 0;
$parameters = camp_read_parameters($query_string, $g_decodeURL);
if (isset($parameters["ArticleCommentSubmitResult"])) {
    unset($parameters["ArticleCommentSubmitResult"]);
}
$cookies = camp_read_cookies($cookies_string);

ob_start();
if (isset($parameters["submitComment"])
        && trim($parameters["submitComment"]) != "") {
    require_once($_SERVER['DOCUMENT_ROOT'].'/comment_lib.php');
    unset($parameters["submitComment"]);
    camp_submit_comment($env_vars, $parameters, $cookies);
} else {
    camp_send_request_to_parser($env_vars, $parameters, $cookies);
}
camp_debug_msg("query string: $query_string");

$output = ob_get_clean();
if ($g_evalPHPCode) {
    eval('?>'.$output);
} else {
    echo $output;
}

?>