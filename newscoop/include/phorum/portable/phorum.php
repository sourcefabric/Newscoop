<?php 

// THIS IS AN EXAMPLE OF HOW YOU WOULD WRAP PHORUM
// IT IS NOT A DROP IN SOLUTION.

// Phorum wrapper to create a portable, dynamic Phorum with a single code base
// and to safely wrap Phorum to protect it from other applications. 

include_once "./phorum_settings.php";

chdir($PHORUM_DIR);

// set a default page

// we set $PHORUM["CUSTOM_QUERY_STRING"] so Phorum will parse it instead of
// the servers QUERY_STRING.
if(preg_match("/^([a-z]+)(,|$)/", $_SERVER["QUERY_STRING"], $match)){
    $GLOBALS["PHORUM_CUSTOM_QUERY_STRING"] = str_replace($match[0], "", $_SERVER["QUERY_STRING"]);
	$page = basename($match[1]);
} elseif(isset($_REQUEST["page"])){
    $page = basename($_REQUEST["page"]);
    $getparts = array();
    foreach (explode("&", $_SERVER["QUERY_STRING"]) as $q) {
        if (substr($q, 0, 5) != "page=") {
            $getparts[] = $q;
        }
    }
    $GLOBALS["PHORUM_CUSTOM_QUERY_STRING"] = implode(",", $getparts);
} else {
    $page="index";
}



if(file_exists("./$page.php")){
    phorum_namespace($page);
}

// create a namespace for Phorum
function phorum_namespace($page)
{
    global $PHORUM;  // globalize the $PHORUM array
    include_once("./$page.php");
}

?>
