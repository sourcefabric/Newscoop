<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");

require_once($GLOBALS['g_campsiteDir'].'/classes/GeoPreferences.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMap.php');

camp_load_translation_strings('api');
camp_load_translation_strings('geolocation');

header('Content-Type: text/html; charset=utf-8');
?>
<?php
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n";
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Expires" content="now" />

    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-filter.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/styles/map-info.css" />
    <title><?php putGS('Geo-filtering'); ?></title>

    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/base64.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/json2.js"></script>

<?php
    $map_width = 800;
    $map_height = 500;
    echo Geo_Map::GetMapFilterHeader($map_width, $map_height);
?>

</head>
<body onLoad="return false;">

<?php
camp_html_content_top(getGS('Geo Filtering'), null);
?>

<div class="map_show_filter">

<div class="map_mappart_outer_filter">

<div class="map_mappart_filter">
<a href="#" class="geo_map_show_initial" onClick="<?php echo Geo_Map::GetMapFilterCenter(); ?> return false;"><?php putGS('show initial map view'); ?></a>
<?php echo Geo_Map::GetMapFilterBody(); ?>
</div><!-- end of map_mappart_filter -->

</div><!-- end of map_mappart_outer_filter -->

<div class="polygon_info">
<div id="geo_polygons_info">&nbsp;</div>
</div><!-- end of polygon_info -->


<div class="polygon_append">
<form name="polygon_spec_new" action="#" onSubmit="<?php echo Geo_Map::GetMapFilterObjName(); ?>.add_polygon(polygon_spec_new.geo_polygon_new.value); return false;">
<a style="float:left" href='#' onclick='<?php echo Geo_Map::GetMapFilterObjName(); ?>.add_polygon(polygon_spec_new.geo_polygon_new.value); return false;'><span class="geo_add_polygon ui-icon ui-icon-plusthick"></span></a>
<input id="geo_polygon_new" class="geo_polygon_new" name="geo_polygon_new" size="80">
</form>
</div><!-- end of polygon_append -->


</div><!-- end of map_show_filter -->

<?php camp_html_copyright_notice(); ?>

</body>
</html>

