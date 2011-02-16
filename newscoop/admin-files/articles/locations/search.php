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
	<title><?php putGS("Map Search Example"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.6.custom.min.js"></script>

    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js" type="text/javascript"></script>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>

<?php
    $bbox_divs = array("tl_lon" => 'top_left_longitude', "tl_lat" => 'top_left_latitude', "br_lon" => 'bottom_right_longitude', "br_lat" => 'bottom_right_latitude');

    $map_width = 600;
    $map_height = 400;
    echo Geo_Map::GetMapSearchHeader($map_width, $map_height, $bbox_divs);
?>

</head>
<body onLoad="return false;">
<div class="map_preview_serach">
<div class="map_mappart_outer_serach">
<div class="map_mappart_serach">
<div class="map_mapmenu_serach">
<a href="#" onClick="<?php echo Geo_Map::GetMapSearchCenter(); ?> return false;"><?php putGS("show initial map view"); ?></a>
</div><!-- end of map_mapmenu -->
<?php echo Geo_Map::GetMapSearchBody(); ?>
</div><!-- end of map_mappart -->
</div><!-- end of map_mappart_outer -->
</div><!-- end of map_preview -->
<div class="map_search_rectangle">
<ol>Top left
<li>longitude: <span id="top_left_longitude"> </span></li>
<li>latitude: <span id="top_left_latitude"> </span></li>
</ol>
</div>
<div>
<ol>Bottom right
<li>longitude: <span id="bottom_right_longitude"> </span></li>
<li>latitude: <span id="bottom_right_latitude"> </span></li>
</ol>
</div><!-- end of map_search_rectangle -->
</body>
</html>

