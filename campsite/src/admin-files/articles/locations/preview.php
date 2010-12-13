<?php
// TODO: during development no access right checking; will be added.

//camp_load_translation_strings("article_files");
//require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
//require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/locations/country_codes.php");

require_once($GLOBALS['g_campsiteDir']."/classes/GeoPreferences.php");
require_once($GLOBALS['g_campsiteDir']."/classes/GeoMap.php");

camp_load_translation_strings("api");
camp_load_translation_strings("geolocation");

$f_language_id = Input::Get('f_language_selected', 'int', 0);
if (0 == $f_language_id) {
    $f_language_id = Input::Get('f_language_id', 'int', 0);
}
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}
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
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-popups.css" />
	<title><?php putGS("Setting Map Locations"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.6.custom.min.js"></script>

    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js" type="text/javascript"></script>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>

<?php
    $map_width = 0;
    $map_height = 0;
    echo Geo_Map::GetMapTagHeader($f_article_number, $f_language_id, $map_width, $map_height);
?>

</head>
<body onLoad="return false;">
<div class="map_preview">
<div class="map_sidepan">

<?php
    //$map_id = Geo_Map::GetMapIdByArticle($f_article_number);
    //$poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number);

    $map_suffix = "_" . $f_article_number . "_" . $f_language_id;

    $geo_info = Geo_Map::GetMapTagList($f_article_number, $f_language_id);

    $map_info = $geo_info["map"];
    $poi_info = $geo_info["pois"];

    echo "<div><dl class='map_preview_map_name'>\n";
    echo "<dt class='map_preview_map_name_label'>";
    putGS("map"); 
    echo ":</dt><dd class='map_preview_map_name_value'>" . $map_info["name"] . "</dd>\n";
    echo "</dl></div>\n";
    echo "<div id=\"side_info\" class=\"side_info\">\n";

    $pind = 0;

    foreach ($poi_info as $poi)
    {
        $cur_label = $poi["title"];
        $cur_perex = $poi["perex"];
        //$cur_lon = $poi["longitude"];
        //$cur_lat = $poi["latitude"];
        $cur_center = $poi["center"];
        $cur_open = $poi["open"];

        $descs_inner = "";
        $descs_inner .= "<div id=\"poi_seq_" . $pind . "\">";

        //$descs_inner .= "<a class='map_preview_poi_name' href=\"#\" onClick=\"geo_hook_on_map_feature_select(geo_object$map_suffix, " . $pind . "); return false;\" >" . $cur_label . "</a>";
        $descs_inner .= "<a class='map_preview_poi_name' href=\"#\" onClick=\"$cur_open return false;\" >" . $cur_label . "</a>";
        $descs_inner .= "<div class='map_preview_poi_perex'>" . $cur_perex . "</div>";

        //$descs_inner .= "<div class='map_preview_poi_center'><a href='#' onClick='geo_object.center_lonlat($cur_lon, $cur_lat); return false;'>";
        $descs_inner .= "<div class='map_preview_poi_center'><a href='#' onClick='$cur_center return false;'>";
        echo $descs_inner;
        putGS("center");
        $descs_inner = "</a></div>";

        $descs_inner .= "<div class='map_preview_poi_spacer'>&nbsp;</div>";
        $descs_inner .= "</div>\n";

        $pind += 1;
        echo $descs_inner;

    }

?>
</div><!--end of side_info -->
</div><!-- end of map_sidepan -->
<div class="map_mappart_outer">
<div class="map_mappart">
<div class="map_mapmenu">
<a href="#" onClick="<?php echo Geo_Map::GetMapTagCenter($f_article_number, $f_language_id); ?> return false;"><? putGS("show initial map view"); ?></a>
</div><!-- end of map_mapmenu -->
<?php echo Geo_Map::GetMapTagBody($f_article_number, $f_language_id); ?>
</div><!-- end of map_mappart -->
</div><!-- end of map_mappart_outer -->
</div><!-- end of map_preview -->
</body>
</html>

