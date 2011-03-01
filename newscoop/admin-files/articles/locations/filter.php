<?php

require_once($GLOBALS['g_campsiteDir']."/classes/GeoPreferences.php");
require_once($GLOBALS['g_campsiteDir']."/classes/GeoMap.php");

camp_load_translation_strings("api");
camp_load_translation_strings("geolocation");

//header("Content-Type: text/html; charset=utf-8");
?>
<?php
#echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n";
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Expires" content="now" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-preview.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/styles/map-info.css" />
	<title><?php putGS("Map Filter"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.6.custom.min.js"></script>
<!--
    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js" type="text/javascript"></script>
-->
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>

<style type="text/css">
.geo_map_show_initial {
    font-size: 12px;
}
.geo_filter_polygon_actions {
    font-size: 14px;
    color: #000000;
}
.geo_filter_unselected {
    /*color: #666666;*/
    /*background: #666666;*/
}
.geo_filter_selected {
    /*color: #666666;*/
    /*background: #d0d0ff;*/
    /*color: #000088;*/
    color: #663300;
}
.polygon_info {
    font-size: 12px;
}
.polygon_info_label {
    font-size: 14px;
    margin-top: 10px;
    margin-bottom: 10px;
}
</style>

<?php
    //$bbox_divs = array("tl_lon" => 'top_left_longitude', "tl_lat" => 'top_left_latitude', "br_lon" => 'bottom_right_longitude', "br_lat" => 'bottom_right_latitude');

    $map_width = 800;
    $map_height = 500;
    //echo Geo_Map::GetMapFilterHeader($map_width, $map_height, $bbox_divs);
    echo Geo_Map::GetMapFilterHeader($map_width, $map_height);
?>

</head>
<body onLoad="return false;">

<div class="map_preview clearfix">
<!--Toolbar-->
<div id="map_toolbar_part" class="toolbar clearfix map_preview_toolbar">

    <div id="map_preview_info" class="map_preview_info">
        <?php putGS("Filter Helper"); ?>
    </div>
  </div>
<!--END Toolbar-->
</div>


<div class="map_preview_filter" style="margin-left:auto;margin-right:auto;margin-top:10px;width:800px">
<div class="map_mappart_outer_filter">

<div class="map_mappart_filter">

<a href="#" class="geo_map_show_initial" onClick="<?php echo Geo_Map::GetMapFilterCenter(); ?> return false;"><?php putGS("show initial map view"); ?></a>
<div class="map_mapmenu_filter">
</div><!-- end of map_mapmenu_filter -->
<?php echo Geo_Map::GetMapFilterBody(); ?>
</div><!-- end of map_mappart_filter -->

<div style="float:right">
<!--
<span><input type="radio" name="polygon_action" onchange="into_method_pan();">pan map</span>
<span><input type="radio" name="polygon_action" onchange="into_method_new();" checked>create polygon</span>
-->
<span><a href="#" class="geo_filter_polygon_actions geo_filter_unselected" id="geo_filter_pan_map" onclick="into_method_pan(); return false;"><?php putGS("pan map"); ?></a></span>
<span><a href="#" class="geo_filter_polygon_actions geo_filter_selected" id="geo_filter_create_polygon" onclick="into_method_new(); return false;" checked><?php putGS("create polygon"); ?></a></span>

<!--<span><input type="radio" name="polygon_action" onchange="into_method_del();">delete</span>-->
</div>

</div><!-- end of map_mappart_outer_filter -->
<div class="polygon_info">
<div class="polygon_info_label"><?php putGS("Polygon coordinates"); ?>:</div>

<div id="geo_polygons_info">&nbsp;</div>
</div>
</div><!-- end of map_preview -->

</body>
</html>

