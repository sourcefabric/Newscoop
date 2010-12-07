<?php
// TODO: no access rights are checked during development; will be added.

require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
//require_once($GLOBALS['g_campsiteDir'].'/classes/GeoLocationContent.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMap.php');

/*
if (!$g_user->hasPermission('geolocations_manage')) {
    camp_html_display_error(getGS("You do not have the right to manage geolocations."));
    exit();
}
*/

//$security_problem = '{"status":"403","description":"Invalid security token!"}';
$unknown_request = '{"status":"404","description":"Unknown request!"}';
//$data_wrong = '{"status":"404","description":"Wrong data."}';

$f_map_id = Input::Get('f_map_id', 'int', -1, false);
$f_article_number = Input::Get('f_article_number', 'int', 0, false);
$f_language_id = Input::Get('f_language_id', 'int', 0, false);

if ((-1 == $f_map_id) || (0 == $f_article_number) || (0 == $f_language_id)) {
    echo $unknown_request;
    exit();
}

// take input parameters, ask the manage class to load/store the locations, returns json
if (Input::Get('load')) {

    $poi_array = Geo_Map::LoadMapData($f_map_id, $f_language_id, $f_article_number);

    $poi_json = json_encode($poi_array);
    echo $poi_json;
    exit();
}

if (Input::Get('store')) {
    // here we shall ask for article_change/edit permissions


    $f_map = Input::Get('f_map', 'string', "", false);
    $f_remove = Input::Get('f_remove', 'string', "", false);
    $f_insert = Input::Get('f_insert_new', 'string', "", false);
    $f_locations = Input::Get('f_update_loc', 'string', "", false);
    $f_contents = Input::Get('f_update_con', 'string', "", false);
    $f_order = Input::Get('f_order', 'string', "", false);

    $poi_array = Geo_Map::StoreMapData($f_map_id, $f_language_id, $f_article_number, $f_map, $f_remove, $f_insert, $f_locations, $f_contents, $f_order);

    $poi_json = json_encode($poi_array);

    echo $poi_json;
    exit();
}

echo $unknown_request;
exit();

?>

