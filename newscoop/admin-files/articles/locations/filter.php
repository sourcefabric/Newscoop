<?php

require_once($GLOBALS['g_campsiteDir']."/classes/GeoPreferences.php");
require_once($GLOBALS['g_campsiteDir']."/classes/GeoMap.php");

camp_load_translation_strings("api");
camp_load_translation_strings("geolocation");

header("Content-Type: text/html; charset=utf-8");
?>
<?php
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n";
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Expires" content="now" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-filter.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/styles/map-info.css" />
	<title><?php putGS("Map Filter"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.6.custom.min.js"></script>

    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js" type="text/javascript"></script>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>

<?php
    $map_width = 800;
    $map_height = 500;
    echo Geo_Map::GetMapFilterHeader($map_width, $map_height);
?>

</head>
<body onLoad="return false;">

<div class="map_preview clearfix">
<!--Toolbar-->
<div id="map_toolbar_part" class="toolbar clearfix map_filter_toolbar">

    <div id="map_filter_info" class="map_filter_info">
        <?php putGS("Filter Helper"); ?>
    </div>
  </div>
<!--END Toolbar-->
</div>

<div class="map_show_filter">

<div class="map_mappart_outer_filter">

<div class="map_mappart_filter">
<a href="#" class="geo_map_show_initial" onClick="<?php echo Geo_Map::GetMapFilterCenter(); ?> return false;"><?php putGS("show initial map view"); ?></a>
<?php echo Geo_Map::GetMapFilterBody(); ?>
</div><!-- end of map_mappart_filter -->

<div class="geo_filter_map_actions">
<span><a href="#" class="geo_filter_polygon_actions geo_filter_unselected" id="geo_filter_pan_map" onclick="<?php echo Geo_Map::GetMapFilterObjName(); ?>.into_method_pan(); return false;"><?php putGS("pan map"); ?></a></span>
<span><a href="#" class="geo_filter_polygon_actions geo_filter_selected" id="geo_filter_create_polygon" onclick="<?php echo Geo_Map::GetMapFilterObjName(); ?>.into_method_new(); return false;" checked><?php putGS("create polygon"); ?></a></span>
</div><!-- end of geo_filter_map_actions -->

</div><!-- end of map_mappart_outer_filter -->

<div class="polygon_info">
<div class="polygon_info_label">&nbsp;</div>
<div id="geo_polygons_info">&nbsp;</div>
</div><!-- end of polygon_info -->

</div><!-- end of map_show_filter -->

</body>
</html>

