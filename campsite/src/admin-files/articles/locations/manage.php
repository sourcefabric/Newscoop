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
$data_wrong = '{"status":"404","description":"Wrong data."}';

$f_map_id = Input::Get('f_map_id', 'int', -1, false);
$f_article_number = Input::Get('f_article_number', 'int', 0, false);
$f_language_id = Input::Get('f_language_id', 'int', 0, false);

if ((-1 == $f_map_id) || (0 == $f_article_number) || (0 == $f_language_id)) {
    echo $unknown_request;
    exit();
}

// take input parameters, ask the manage class to load/store the locations, returns json
if (Input::Get('load')) {

    $found_list = Geo_LocationContents::ReadMapPoints($f_map_id, $f_language_id);

    $geo_map_usage = Geo_LocationContents::ReadMapInfo("map", $f_map_id);

    $poi_array = array("status" => "200", "pois" => $found_list, "map" => $geo_map_usage);
    $poi_json = json_encode($poi_array);
    echo $poi_json;
    exit();
}

if (Input::Get('store')) {

    $status = true;

    $f_map = Input::Get('f_map', 'string', "", false);

    if ("" != $f_map)
    {
        $map_data = array();
        try
        {
            $f_map = str_replace("%2B", "+", $f_map);
            $f_map = str_replace("%2F", "/", $f_map);
            $map_json = base64_decode($f_map);

            $map_data = json_decode($map_json);
        }
        catch (Exception $exc)
        {
            $status = false;
        }
        if ($status)
        {
            $status = Geo_LocationContents::UpdateMap($f_map_id, $f_article_number, $map_data);
        }
    }

    if (!$status)
    {
        echo $data_wrong;
        exit();
    }

    $f_remove = Input::Get('f_remove', 'string', 0, false);
    if ("" != $f_remove)
    {
        $remove_data = array();
        try
        {
            $remove_data = str_replace("%2B", "+", $remove_data);
            $remove_data = str_replace("%2F", "/", $remove_data);
            $remove_json = base64_decode($f_remove);
            $remove_data = json_decode($remove_json);
        }
        catch (Exception $exc)
        {
            $status = false;
        }
        if ($status)
        {

            $status = Geo_LocationContents::RemovePoints($f_map_id, $remove_data);

        }
    }

    if (!$status)
    {
        echo $data_wrong;
        exit();
    }

    $new_ids = array();
    $f_insert = Input::Get('f_insert_new', 'string', 0, false);
    if ("" != $f_insert)
    {
        $insert_data = array();
        try
        {
            $insert_json = str_replace("%2B", "+", $insert_json);
            $insert_json = str_replace("%2F", "/", $insert_json);
            $insert_json = base64_decode($f_insert);

            $insert_data = json_decode($insert_json);
        }
        catch (Exception $exc)
        {
            $status = false;
        }
        if ($status)
        {
            $status = Geo_LocationContents::InsertPoints($f_map_id, $f_language_id, $f_article_number, $insert_data, $new_ids);

        }
    }


    if (!$status)
    {
        echo $data_wrong;
        exit();
    }


    $f_locations = Input::Get('f_update_loc', 'string', 0, false);
    if ("" != $f_locations)
    {
        $locations_data = array();
        try
        {
            $locations_json = str_replace("%2B", "+", $locations_json);
            $locations_json = str_replace("%2F", "/", $locations_json);
            $locations_json = base64_decode($f_locations);
            $locations_data = json_decode($locations_json);
        }
        catch (Exception $exc)
        {
            $status = false;
        }
        if ($status)
        {

            $status = Geo_LocationContents::UpdateLocations($f_map_id, $locations_data);
        }
    }

    if (!$status)
    {
        echo $data_wrong;
        exit();
    }

    $f_contents = Input::Get('f_update_con', 'string', "", false);

    if ("" != $f_contents)
    {
        $contents_data = array();
        try
        {

            $contents_json = str_replace("%2B", "+", $contents_json);
            $contents_json = str_replace("%2F", "/", $contents_json);
            $contents_json = base64_decode($f_contents);

            $contents_data = json_decode($contents_json);
        }
        catch (Exception $exc)
        {
            $status = false;
        }
        if ($status)
        {
            $status = Geo_LocationContents::UpdateContents($f_map_id, $contents_data);
        }
    }

    if (!$status)
    {
        echo $data_wrong;
        exit();
    }

    $f_order = Input::Get('f_order', 'string', "", false);

    if ("" != $f_order)
    {
        $order_data = array();
        try
        {
            $order_json = str_replace("%2B", "+", $order_json);
            $order_json = str_replace("%2F", "/", $order_json);
            $order_json = base64_decode($f_order);
            $order_data = json_decode($order_json);
        }
        catch (Exception $exc)
        {
            $status = false;
        }
        if ($status)
        {
            $status = Geo_LocationContents::UpdateOrder($f_map_id, $order_data, $new_ids);
        }
    }

    if (!$status)
    {
        echo $data_wrong;
        exit();
    }

    $geo_map_usage = Geo_LocationContents::ReadMapInfo("map", $f_map_id);


    $found_list = Geo_LocationContents::ReadMapPoints($f_map_id, $f_language_id);

    $poi_array = array("status" => "200", "pois" => $found_list, "map" => $geo_map_usage);
    $poi_json = json_encode($poi_array);

    echo $poi_json;
    exit();
}

echo $unknown_request;
exit();

?>

