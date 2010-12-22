<?php

require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/locations/country_codes.php");

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

$articleObj = new Article($f_language_id, $f_article_number);



$cnf_html_dir = $Campsite['HTML_DIR'];
$cnf_website_url = $Campsite['WEBSITE_URL'];

$geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url);
$geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info["incl_obj"]);
$geo_map_json = "";
$geo_map_json .= json_encode($geo_map_info["json_obj"]);

$geo_map_usage = Geo_Map::ReadMapInfo("article", $f_article_number);
$geo_map_usage_json = "";
$geo_map_usage_json .= json_encode($geo_map_usage);

$geo_icons_info = Geo_Preferences::GetIconsInfo($cnf_html_dir, $cnf_website_url);
$geo_icons_json = "";
$geo_icons_json .= json_encode($geo_icons_info["json_obj"]);


$geo_popups_info = Geo_Preferences::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
$geo_popups_json = "";
$geo_popups_json .= json_encode($geo_popups_info["json_obj"]);

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
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-picking.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-popups.css" />
	<title><?php putGS("Setting Map Locations"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.6.custom.min.js"></script>

    <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js" type="text/javascript"></script>
    <script type="text/javascript">
    <!--
        var website_url = "<?php echo $Campsite['WEBSITE_URL'];?>";
        var g_admin_url = '/<?php echo $ADMIN; ?>';
        var g_security_token = '<?php echo SecurityToken::GetToken(); ?>';
    //-->
    </script>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>
    <?php echo $geo_map_incl; ?>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/openlayers/OLlocals.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/location_chooser.js"></script>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/country_codes.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/geonames/search.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.dataTables.min.js"></script>

	<script type="text/javascript">
// prepare localized strings
var set_local_strings = function()
{
    var local_strings = {};

    local_strings["google_map"] = "<?php putGS("Google Map"); ?>";
    local_strings["mapquest_map"] = "<?php putGS("MapQuest Map"); ?>";
    local_strings["openstreet_map"] = "<?php putGS("OpenStreet Map"); ?>";
    local_strings["fill_in_map_name"] = "<?php putGS("fill in map name"); ?>";
    local_strings["point_markers"] = "<?php putGS("Point markers"); ?>";
    local_strings["this_should_not_happen_now"] = "<?php putGS("problem at point processing, please send error report"); ?>";
    local_strings["really_to_delete_the_point"] = "<?php putGS("Really to delete the point?"); ?>";
    local_strings["the_removal_is_from_all_languages"] = "<?php putGS("The removal is from all language versions of the article."); ?>";
    local_strings["point_number"] = "<?php putGS("Point no."); ?>";
    local_strings["fill_in_the_point_description"] = "<?php putGS("fill in the point description"); ?>";
    local_strings["edit"] = "<?php putGS("edit"); ?>";
    local_strings["center"] = "<?php putGS("center"); ?>";
    local_strings["enable"] = "<?php putGS("enable"); ?>";
    local_strings["disable"] = "<?php putGS("disable"); ?>";
    local_strings["remove"] = "<?php putGS("remove"); ?>";

    geo_locations.set_display_strings(local_strings);

    local_strings = {};

    local_strings["cc"] = "<?php putGS("cc"); ?>";
    local_strings["city"] = "<?php putGS("city"); ?>";
    local_strings["no_city_was_found"] = "<?php putGS("sorry, no city was found"); ?>";

    geo_names.set_display_strings(local_strings);

};
// prepare map settings
var useSystemParameters = function()
{
<?php
    $article_spec_arr = array("language_id" => $f_language_id, "article_number" => $f_article_number);
    $article_spec = json_encode($article_spec_arr);
?>
    geo_locations.set_article_spec(<?php echo $article_spec; ?>);
    geo_locations.set_map_info(<?php echo $geo_map_json; ?>);
    geo_locations.set_map_usage(<?php echo $geo_map_usage_json; ?>);
    geo_locations.set_icons_info(<?php echo $geo_icons_json; ?>);
    geo_locations.set_popups_info(<?php echo $geo_popups_json; ?>);
};

// city search start; if longitude/latitude provided, immediate results done
var findLocation = function()
{
    var city_obj = document.getElementById ? document.getElementById("search-city") : null;
    var cc_obj = document.getElementById ? document.getElementById("search-country") : null;
    
    var cc_code = cc_obj.options[cc_obj.selectedIndex].value;
    
    var cities_term = city_obj.value.replace(/\*/g, "%");
    cities_term = cities_term.replace(/^\s+|\s+$/g, '');
    if (0 == cities_term.length)
    {
        return;
    }
    
    var longitude = null;
    var latitude = null;
    
    var comma_sep = false;
    if ((-1 == cities_term.indexOf(';')) && (-1 == cities_term.indexOf(' ')))
    {
        comma_sep = true;
    }
    
    var test_list = [];
    if (comma_sep)
    {
        test_list = cities_term.split(/[,]+/);
    }
    else
    {
        test_list = cities_term.split(/[\s;][\s,;]*/);
    }
    
    geonames_dir = "<?php echo $Campsite['WEBSITE_URL']; ?>/admin/cities/";

    var direct_coords = true;
    if (2 <= test_list.length)
    {
        latitude = parseFloat(test_list[0].replace(",", "."));
        longitude = parseFloat(test_list[1].replace(",", "."));

        if ((isNaN(latitude)) || (isNaN(longitude)))
        {
            direct_coords = false;
        }
        if (direct_coords)
        {
            geo_locations.center_lonlat (longitude, latitude);
            geo_locations.insert_poi('EPSG:4326', null, longitude, latitude);

            var found_cits = geo_names.askForNearCities(longitude, latitude, geonames_dir, "search_results");
            return;
        }
    }
    
    var found_locs = geo_names.askForCityLocation(cities_term, cc_code, geonames_dir, "search_results");

};

// dispetching search results display
var showhideState = false;
var showhideLocation = function()
{
    if (showhideState)
    {
        hideLocation();
    }
    else
    {
        showLocation();
    }
};

// hides the city search results box
var hideLocation = function()
{
    showhideState = false;

    $("#search_results").addClass("hidden");

    var showhide_link = document.getElementById ? document.getElementById("showhide_link") : null;
    showhide_link.innerHTML = "+";

    $("#map_geo_showhide").removeClass("hidden");

    geo_locations.map_update_side_desc_height();

};

// shows the city search results box
var showLocation = function()
{
    showhideState = true;

    $("#map_sidedescs").addClass("hidden");

    $("#search_results").removeClass("hidden");

    var showhide_link = document.getElementById ? document.getElementById("showhide_link") : null;
    showhide_link.innerHTML = "x";

    $("#map_geo_showhide").removeClass("hidden");

    geo_locations.map_update_side_desc_height();
    $("#map_sidedescs").removeClass("hidden");

};

// if some search term initially provided, do the search
var init_search = function ()
{
    var city_obj = document.getElementById ? document.getElementById("search-city") : null;
    if ("" != city_obj.value)
    {
        findLocation();
    }
};

var on_load_proc = function()
{
    set_local_strings();
    $("#edit_tabs_all").tabs();
    //setTimeout(function() {
    geo_main_selecting_locations('<?php echo $geocodingdir; ?>', 'map_mapcanvas', 'map_sidedescs', '', '', true);
    init_search();
    //}, 1000);
};

// tthe map initialization itself does not work correctly via this; the other tasks put here
/*
(function($){
    $(document).ready(function()
    {
        //set_local_strings();
        //$("#edit_tabs_all").tabs();
        //on_load_proc();
        //init_search();
    });
})(jQuery);
*/
	</script>
</head>
<?php $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/'; ?>
<body onLoad="on_load_proc(); return false;">
<div class="map_editor">
<div class="map_sidepan">
<div id="map_save_part" class="map_save_part">
<a id="map_save_label" class="map_save_label map_save_off" href="#" onClick="geo_locations.map_save_all(); return false;"><?php putGS("save"); ?></a> 
<div id="map_save_info" class="map_save_info">&nbsp;<a href="#" class="map_name_display" id="map_name_display" onClick="geo_locations.map_edit_name(); return false;" title="setting map name helps with map search"><?php putGS("fill in map name"); ?></a><input id="map_name_input" class="map_name_input hidden" type="text" size="10" onChange="geo_locations.map_save_name(); return false;" onBlur="geo_locations.map_display_name(); return false;">&nbsp;</div>
</div><!-- end of map_save_part -->
<div class="map_menubar">
<select class="map_geo_ccselect" id="search-country" name="geo_cc" onChange="findLocation(); return false;">
<option value="" selected="true"><?php putGS("any country"); ?></option>
<?php
foreach ($country_codes_alpha_2 as $cc_name => $cc_value) {
    echo '<option value="' . $cc_value . '">' . $cc_name . '</option>' . "\n";
}
?>
</select>
<label class="map_geo_search"><a href="#" onClick="findLocation(); return false;"><?php putGS("Find"); ?></a>&nbsp;</label>
<label id="map_geo_showhide" class="hidden">[<a href="#" id="showhide_link" onClick="showhideLocation(); return false;">+</a>]</label>
</div><!-- end of map_menubar -->

<form class="map_geo_city_search" onSubmit="findLocation(); return false;">
<input class="map_geo_cityname" id="search-city" type="text">
</form>
<div id="side_info" class="side_info">
<div id="search_results" class="search_results hidden">&nbsp;</div>
<div id="map_sidedescs" class="map_sidedescs">&nbsp;</div>
</div><!--end of side_info -->
</div><!-- end of map_sidepan -->
<div class="map_mappart">
<div class="map_mapmenu">

<div class="map_mapinitview">
<a href="#" onClick="geo_locations.map_showview(); return false;"><?php putGS("show article view"); ?></a>
&nbsp;|&nbsp;
<a href="#" onClick="geo_locations.map_setview(); return false;"><?php putGS("set as the article view"); ?></a>
</div><!-- end of map initview -->
<div class="map_resizing">
&nbsp;<?php putGS("resize article view"); ?>:&nbsp;
<a href="#" onClick="geo_locations.map_width_change(-10); return false;">&lt;&lt;</a>
H
<a href="#" onClick="geo_locations.map_width_change(10); return false;">&gt;&gt;</a>
&nbsp;&nbsp;
<a href="#" onClick="geo_locations.map_height_change(-10); return false;">&lt;&lt;</a>
V
<a href="#" onClick="geo_locations.map_height_change(10); return false;">&gt;&gt;</a>
</div><!-- end of map resizing -->
<div id="map_view_size" class="map_resizing">600x400</div>
<!--
<div id="map_part_left" class="map_realsize part_left"></div>
<div id="map_part_right" class="map_realsize part_right"></div>
<div id="map_part_top" class="map_realsize part_top"></div>
<div id="map_part_bottom" class="map_realsize part_bottom"></div>
-->
<div id="map_mapedit" class="map_mapedit hidden">
<div class="map_editinner">
<div class="map_editpart1">

<form action="#" onSubmit="return false";>  
<fieldset>
<!--<legend class="map_editactions0"><a href="#" onClick="geo_locations.close_edit_window(); return false;">close</a></legend>-->

<div id="edit_tabs_all">
	<ul>
		<li><a href="#edit_basic"><?php putGS("basic"); ?></a></li>
		<li><a href="#edit_html"><?php putGS("text"); ?></a></li>
		<li><a href="#edit_image" id="image_edit_part"><?php putGS("image"); ?></a></li>
		<li><a href="#edit_video" id="video_edit_part"><?php putGS("video"); ?></a></li>
		<li><a href="#edit_marker"><?php putGS("icon"); ?></a></li>
	</ul>
	<div id="edit_basic" class="edit_tabs">
<ol>
<li class="edit_label_top">
<label class="edit_label" for="point_label"><?php putGS("Label"); ?>:</label>
<input id="point_label" name="point_label" class="text" type="text" onChange="geo_locations.store_point_label(); return false;" />
</li>
<li id="edit_part_link" class="">
<label class="edit_label" for="point_link"><?php putGS("Label link"); ?>:</label>
<input id="point_link" name="point_link" class="text" type="text" onChange="geo_locations.store_point_property('link', this.value); return false;" />
</li>
<li>
<label class="edit_label" for="point_perex"><?php putGS("Short description for points listing"); ?>:</label>
<textarea rows="2" cols="40" id="point_perex" name="point_perex" class="text" type="text" onChange="geo_locations.store_point_property('perex', this.value); return false;">
</textarea>
</li>
</ol>
	</div>
	<div id="edit_html" class="edit_tabs">
<ol>
<li class="edit_label_top">
<label class="edit_label" for="point_predefined"><?php putGS("Pop-up content"); ?>:</label>

<select class="text" id="point_predefined" name="point_predefined" onChange="geo_locations.store_point_direct(this.options[this.selectedIndex].value); return false;">
<option value="0" selected="true"><?php putGS("plain text"); ?></option>
<option value="1"><?php putGS("html content"); ?></option>
</select>

<input id="point_edit_mode_edit" name="point_edit_mode" class="text" type="radio" onChange="geo_locations.edit_set_mode('edit'); return false;" checked /><?php putGS("Edit"); ?>
<input id="point_edit_mode_view" name="point_edit_mode" class="text" type="radio" onChange="geo_locations.edit_set_mode('view'); return false;" /><?php putGS("View"); ?>
</li>

<li id="edit_part_text" class="">
<label class="edit_label" for="point_descr"><!--Textual description:-->&nbsp;</label>
<textarea rows="5" cols="40" id="point_descr" name="point_descr" class="text" type="text" onChange="geo_locations.store_point_property('text', this.value); return false;">
</textarea>
</li>
<li id="edit_part_content" class="hidden">
<label class="edit_label" for="point_content"><!--HTML pop-up content:-->&nbsp;</label>
<textarea rows="5" cols="40" id="point_content" name="point_content" class="text" type="text" onChange="geo_locations.store_point_property('content', this.value); return false;">
</textarea>
</li>
<li id="edit_part_preview_outer" class="hidden">
<div class="popup_preview hidden" id="edit_part_preview"> </div>
</li>
</ol>
	</div>
	<div id="edit_image" class="edit_tabs">
<ol>
<li class="edit_label_top">
<label class="edit_label" for="point_image"><?php putGS("Image URL"); ?>:</label>
<input id="point_image" name="point_image" class="text" type="text" onChange="geo_locations.store_point_property('image_source', this.value); return false;" />
</li>
<li class="poi_image_type_placehold">
&nbsp;
</li>
<li>

</li>
<li>
<label class="edit_label" for="point_image_height"><?php putGS("width"); ?>:</label>
<input id="point_image_width" name="point_image_height" class="text" type="text" onChange="geo_locations.store_point_property('image_width', this.value); return false;" />
</li>
<li>
<label class="edit_label" for="point_image_height"><?php putGS("height"); ?>:</label>
<input id="point_image_height" name="point_image_height" class="text" type="text" onChange="geo_locations.store_point_property('image_height', this.value); return false;" />
</li>
</ol>
	</div>
	<div id="edit_video" class="edit_tabs">
<ol>
<li class="edit_label_top">
<label class="edit_label" for="point_video"><span id="video_file_label_id"><?php putGS("Video ID"); ?>:</span><span id="video_file_label_file" class="hidden"><?php putGS("Video file"); ?>:</span></label>
<input id="point_video" name="point_video" class="text" type="text" onChange="geo_locations.store_point_property('video_id', this.value); return false;" />
</li>

<li>
<label class="edit_label" for="point_video_type"><?php putGS("source"); ?>:</label>
<select class="text poi_video_type_selection" id="point_video_type" name="point_video_type" onChange="geo_locations.store_point_property('video_type', this.options[this.selectedIndex].value); return false;">
<option value="none" selected="true"><?php putGS("None"); ?></option>
<option value="youtube">Youtube</option>
<option value="vimeo">Vimeo</option>
<option value="flash">Flash (sfw)</option>
<option value="flv">Flash (flv)</option>
</select>

</li>
<li>

</li>
<li>
<label class="edit_label" for="point_video_width"><?php putGS("width"); ?>:</label>
<input id="point_video_width" name="point_video_width" class="text" type="text" onChange="geo_locations.store_point_property('video_width', this.value); return false;" />
</li>
<li>
<label class="edit_label" for="point_video_height"><?php putGS("height"); ?>:</label>
<input id="point_video_height" name="point_video_height" class="text" type="text" onChange="geo_locations.store_point_property('video_height', this.value); return false;" />
</li>
</ol>
	</div>
	<div id="edit_marker" class="edit_tabs">
		<div id="edit_marker_selected" class="edit_marker_selected">
		<?php putGS("selected marker"); ?>:&nbsp;</div>
		<div><img id="edit_marker_selected_src" src="">
		</div>
		<div class="edit_marker_choices"><div id="edit_marker_choices">&nbsp;</div></div>
	</div>
</div>
</fieldset>  
</form>

</div><!-- end of map_editpart1 -->
</div><!-- end of map_editinner -->

<div class="map_editactions">

<a href="#" onClick="geo_locations.close_edit_window(); return false;"><?php putGS("close window"); ?></a>
</div><!-- end of map_editactions -->

</div><!-- end of map_mapedit -->

</div><!-- end of map_mapmenu -->
<div id="map_mapcanvas" class="map_mapcanvas"></div>
</div><!-- end of map_mappart -->
</div><!-- end of map_editor -->
<div id="error_messages" class="hidden" style="margin-top:200px">debug purposes</div>
</body>
</html>
