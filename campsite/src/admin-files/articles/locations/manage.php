<?php
// TODO: no access rights are checked during development; will be added.

require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/LocationContents.php');

/*
if (!$g_user->hasPermission('geolocations_manage')) {
    camp_html_display_error(getGS("You do not have the right to manage geolocations."));
    exit();
}
*/

$security_problem = '{"status":"403","description":"Invalid security token!"}';
$unknown_request = '{"status":"404","description":"Unknown request!"}';

$f_article_number = Input::Get('f_article_number', 'int', 0, false);
$f_language_id = Input::Get('f_language_id', 'int', 0, false);

//echo "f_article_number: $f_article_number, f_language_id: $f_language_id<br />\n";
//exit();
if ((0 == $f_article_number) || (0 == $f_language_id)) {
    echo $unknown_request;
    exit();
}

// take input parameters, ask the manage class to load/store the locations, returns json
if (Input::Get('load')) {

    $found_list = Geo_LocationContents::ReadArticlePoints($f_article_number, $f_language_id);
    $poi_array = array("status" => "200", "pois" => $found_list);
    $poi_json = json_encode($poi_array);
    echo $poi_json;
    exit();
}

echo $unknown_request;
exit();

?>

