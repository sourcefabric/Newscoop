<?php
// TODO: during development no access right checking; will be added.

camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/locations/country_codes.php");

require_once($GLOBALS['g_campsiteDir']."/classes/GeoLocations.php");

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

$articleObj = new Article($f_language_id, $f_article_number);



$cnf_html_dir = $Campsite['HTML_DIR'];
$cnf_website_url = $Campsite['WEBSITE_URL'];

$geo_map_info = Geo_Locations::GetMapInfo($cnf_html_dir, $cnf_website_url);
$geo_map_incl = Geo_Locations::PrepareMapIncludes($geo_map_info["incl_obj"]);
$geo_map_json = "";
//$geo_map_json .= "geo_locations.set_map_info(";
$geo_map_json .= json_encode($geo_map_info["json_obj"]);
//$geo_map_json .= ");";


$geo_icons_info = Geo_Locations::GetIconsInfo($cnf_html_dir, $cnf_website_url);
$geo_icons_json = "";
//$geo_icons_json .= "geo_locations.set_icons_info(";
$geo_icons_json .= json_encode($geo_icons_info["json_obj"]);
//$geo_icons_json .= ");";


$geo_popups_info = Geo_Locations::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
$geo_popups_json = "";
//$geo_popups_json .= "geo_locations.set_icons_info(";
$geo_popups_json .= json_encode($geo_popups_info["json_obj"]);
//$geo_popups_json .= ");";


//header("Content-Type: text/html; charset=utf-8");
?>
<?php
#echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n";
?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<LINK rel="stylesheet" type="text/css" href="map-picking.css">
<!--
	<title><?php putGS("Setting Map Locations"); ?></title>
-->
	<title>Setting Map Locations</title>
<!--
	<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
-->

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/all/css/ui-lightness/jquery-ui-1.8.5.custom.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/all/js/jquery-ui-1.8.5.custom.min.js"></script>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>
    <?php echo $geo_map_incl; ?>
<!--
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
-->
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/location_chooser.js"></script>

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/country_codes.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/geonames/search.js"></script>
<!--
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/flexigrid/css/flexigrid/flexigrid.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/flexigrid/flexigrid.js"></script>
-->
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.dataTables.min.js"></script>

	<script type="text/javascript">
    // prepare map settings
    geo_locations.set_map_info(<?php echo $geo_map_json; ?>);
    geo_locations.set_icons_info(<?php echo $geo_icons_json; ?>);
    geo_locations.set_popups_info(<?php echo $geo_popups_json; ?>);

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
        //var direct_coords = true;
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
    
    //geonames_dir = "<?php echo $Campsite['WEBSITE_URL']; ?>/admin/cities/";
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

    //var display_obj = document.getElementById ? document.getElementById("search_results") : null;
    $("#search_results").addClass("hidden");

    //var hide_obj = document.getElementById ? document.getElementById("map_geo_hide") : null;
    //var show_obj = document.getElementById ? document.getElementById("map_geo_show") : null;
    var showhide_link = document.getElementById ? document.getElementById("showhide_link") : null;
    showhide_link.innerHTML = "+";
    //showhide_link.onClick = function(ev) {showLocation(); return false;};

    $("#map_geo_showhide").removeClass("hidden");

/*
    display_obj.className += " hidden";
    hide_obj.className += " hidden";

    var hidden_class = "hidden";
    var removal = new RegExp("(^|\\s)" + hidden_class + "(\\s" + hidden_class + ")*" + "(\\s|$)", "g");

    new_className = show_obj.className.replace(removal, " ");
    new_className = new_className.replace(/\s\s+/g, " ");
    show_obj.className = new_className;
*/

    geo_locations.map_update_side_desc_height();

};

// shows the city search results box
var showLocation = function()
{
    showhideState = true;

    $("#map_sidedescs").addClass("hidden");

    //var display_obj = document.getElementById ? document.getElementById("search_results") : null;
    $("#search_results").removeClass("hidden");

    //var hide_obj = document.getElementById ? document.getElementById("map_geo_hide") : null;
    //var show_obj = document.getElementById ? document.getElementById("map_geo_show") : null;
    var showhide_link = document.getElementById ? document.getElementById("showhide_link") : null;
    showhide_link.innerHTML = "x";
    //showhide_link.onClick = function(ev) {hideLocation(); return false;};

    $("#map_geo_showhide").removeClass("hidden");

/*
    var new_className = "";

    var hidden_class = "hidden";
    var removal = new RegExp("(^|\\s)" + hidden_class + "(\\s" + hidden_class + ")*" + "(\\s|$)", "g");

    new_className = display_obj.className.replace(removal, " ");
    new_className = new_className.replace(/\s\s+/g, " ");
    display_obj.className = new_className;

    show_obj.className += " hidden";

    new_className = hide_obj.className.replace(removal, " ");
    new_className = new_className.replace(/\s\s+/g, " ");
    hide_obj.className = new_className;
*/

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

// tthe map initialization itself does not work correctly via this; the other tasks put here
(function($){
    $(document).ready(function()
    {
        init_search();
        $("#edit_tabs_all").tabs();
    });
})(jQuery);
	</script>
</head>
<?php $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/'; ?>
<body onLoad="geo_main_selecting_locations('<?php echo $geocodingdir; ?>', 'map_mapcanvas', 'map_sidedescs', '', '', true); return false;">
<div class="map_editor">
<div class="map_sidepan">
<div id="map_save_part" class="map_save_part">
<a id="map_save_label" class="map_save_label map_save_off" href="#" onClick="geo_locations.map_save_all(); return false;">save</a> 
<div class="map_save_info">&nbsp;no change yet</div>
</div><!-- end of map_save_part -->
<div class="map_menubar">
<select class="map_geo_ccselect" id="search-country" name="geo_cc" onChange="findLocation(); return false;">
<option value="" selected="true">any country</option>
<?php
foreach ($country_codes_alpha_2 as $cc_name => $cc_value) {
    echo '<option value="' . $cc_value . '">' . $cc_name . '</option>' . "\n";
}
?>
</select>
<label class="map_geo_search"><a href="#" onClick="findLocation(); return false;">Find</a>&nbsp;</label>
<label id="map_geo_showhide" class="hidden">[<a href="#" id="showhide_link" onClick="showhideLocation(); return false;">+</a>]</label>
<!--
<label id="map_geo_show" class="hidden">[<a href="#" onClick="showLocation(); return false;">+</a>]</label>
<label id="map_geo_hide" class="hidden">[<a href="#" onClick="hideLocation(); return false;">x</a>]</label>
-->
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
<a href="#" onClick="geo_locations.map_showview(); return false;">show article view</a>
&nbsp;|&nbsp;
<a href="#" onClick="geo_locations.map_setview(); return false;">set as the article view</a>
</div><!-- end of map initview -->
<div class="map_resizing">
&nbsp;resize article view:&nbsp;
<a href="#" onClick="geo_locations.map_width_change(-10); return false;">&lt;&lt;</a>
H
<a href="#" onClick="geo_locations.map_width_change(10); return false;">&gt;&gt;</a>
&nbsp;&nbsp;
<a href="#" onClick="geo_locations.map_height_change(-10); return false;">&lt;&lt;</a>
V
<a href="#" onClick="geo_locations.map_height_change(10); return false;">&gt;&gt;</a>
</div><!-- end of map resizing -->
<div id="map_view_size" class="map_resizing">600x400</div>

<div id="map_part_left" class="map_realsize part_left"></div>
<div id="map_part_right" class="map_realsize part_right"></div>
<div id="map_part_top" class="map_realsize part_top"></div>
<div id="map_part_bottom" class="map_realsize part_bottom"></div>

<div id="map_mapedit" class="map_mapedit hidden">
<div class="map_editinner">
<div class="map_editpart1">

<form action="#" onSubmit="return false";>  
<fieldset>
<!--<legend class="map_editactions0"><a href="#" onClick="geo_locations.close_edit_window(); return false;">close</a></legend>-->

<div id="edit_tabs_all">
	<ul>
		<li><a href="#edit_basic">basic</a></li>
		<li><a href="#edit_html">text</a></li>
		<li><a href="#edit_image">image</a></li>
		<li><a href="#edit_video">video</a></li>
		<li><a href="#edit_audio">audio</a></li>
		<li><a href="#edit_marker">icon</a></li>
	</ul>
	<div id="edit_basic" class="edit_tabs">
<ol>
<li>
<label class="edit_label" for="point_label">Label:</label>
<input id="point_label" name="point_label" class="text" type="text" onChange="geo_locations.store_point_label(); return false;" />
</li>
<li>
<label class="edit_label" for="point_clickable">Clickable:</label>
<input id="point_clickable" name="point_clickable" class="text" type="checkbox" onChange="store_point_clickable(this.checked); return false;" />
</li>
<li>
<label class="edit_label" for="point_perex">Perex:</label>
<textarea rows="4" cols="40" id="point_perex" name="point_perex" class="text" type="text" onChange="geo_locations.store_point_perex(); return false;">
</textarea>
</li>
</ol>
	</div>
	<div id="edit_html" class="edit_tabs">
<ol>
<li>
<label class="edit_label" for="point_direct">Use HTML:</label>
<input id="point_direct" name="point_direct" class="text" type="checkbox" onChange="geo_locations.store_point_direct(this.checked); return false;" />
</li>
<li id="edit_part_description" class="">
<label class="edit_label" for="point_descr">Description:</label>
<textarea rows="4" cols="40" id="point_descr" name="point_descr" class="text" type="text" onChange="storePointDescr(); return false;">
</textarea>
</li>
<li id="edit_part_content" class="hidden">
<label class="edit_label" for="point_content">Content:</label>
<textarea rows="4" cols="40" id="point_content" name="point_content" class="text" type="text" onChange="storePointContent(); return false;">
</textarea>
</li>
</ol>
	</div>
	<div id="edit_image" class="edit_tabs">
<ol>
<li>
<label class="edit_label" for="point_image_usage">Use image:</label>
<input id="point_image_usage" name="point_image_usage" class="text" type="checkbox" onChange="store_point_image('usage', this.checked); return false;" />
</li>
<li>
<label class="edit_label" for="point_image">Image source:</label>
<input id="point_image" name="point_image" class="text" type="text" onChange="geo_locations.store_point_image(); return false;" />
</li>
<li>
<label class="edit_label" for="point_image_height">width:</label>
<input id="point_image_height" name="point_image_height" class="text" type="text" onChange="geo_locations.store_point_image(); return false;" />
</li>
<li>
<label class="edit_label" for="point_image_height">height:</label>
<input id="point_image_height" name="point_image_height" class="text" type="text" onChange="geo_locations.store_point_image(); return false;" />
</li>
</ol>
	</div>
	<div id="edit_video" class="edit_tabs">
<ol>
<li>
<label class="edit_label" for="point_video_usage">Use video:</label>
<input id="point_video_usage" name="point_video_usage" class="text" type="checkbox" onChange="store_point_video('usage', this.checked); return false;" />
</li>
<li>
<label class="edit_label" for="point_video_type">Video type:</label>
<input id="point_video_type_youtube" name="point_video_type" class="text" type="radio" onChange="storePointVideo(); return false;" />Youtube
<input id="point_video_type_vimeo" name="point_video_type" class="text" type="radio" onChange="storePointVideo(); return false;" />Vimeo
<input id="point_video_type_local" name="point_video_type" class="text" type="radio" onChange="storePointVideo(); return false;" />Local
</li>
<li>
<label class="edit_label" for="point_video">Video ID:</label>
<input id="point_video" name="point_video" class="text" type="text" onChange="storePointVideo(); return false;" />
</li>
<li>
<label class="edit_label" for="point_video_height">width:</label>
<input id="point_video_height" name="point_video_height" class="text" type="text" onChange="store_point_video(); return false;" />
</li>
<li>
<label class="edit_label" for="point_video_height">height:</label>
<input id="point_video_height" name="point_video_height" class="text" type="text" onChange="store+point_video(); return false;" />
</li>
</ol>
	</div>
	<div id="edit_audio" class="edit_tabs">
<ol>
<li>
<li>
<label class="edit_label" for="point_audio_usage">Use audio:</label>
<input id="point_audio_usage" name="point_audio_usage" class="text" type="checkbox" onChange="store_point_audio('usage', this.checked); return false;" />
</li>
<!--
<li>
<label class="edit_label" for="point_audio">Audio:</label>
<input id="point_audio" name="point_audio" class="text" type="text" onChange="storePointAudio(); return false;" />
</li>
-->
<li>
<label class="edit_label" for="point_audio_directory">directory:</label>
<input id="point_audio_directory" name="point_audio_directory" class="text" type="text" onChange="store_point_audio(); return false;" />
</li>
<li>
<label class="edit_label" for="point_audio_track">track:</label>
<input id="point_audio_track" name="point_audio_track" class="text" type="text" onChange="store_point_audio(); return false;" />
</li>
<li>
<label class="edit_label" for="point_audio_type">type:</label>
<input id="point_audio_type" name="point_audio_type" class="text" type="text" onChange="store_point_audio(); return false;" />
</li>
</ol>
	</div>
	<div id="edit_marker" class="edit_tabs">
		<div id="eidt_marker_selected" class="eidt_marker_selected">
		selected marker:<br /><img id="eidt_marker_selected_src" src="">
		</div>
		<div id="eidt_marker_choices" class="eidt_marker_choices">&nbsp;</div>
	</div>
</div>
</fieldset>  
</form>

</div><!-- end of map_editpart1 -->
</div><!-- end of map_editinner -->

<div class="map_editactions">
<a href="#" onClick="geo_locations.save_edit_window(); return false;">save this point</a>
&nbsp;
<a href="#" onClick="geo_locations.close_edit_window(); return false;">close window</a>
</div><!-- end of map_editactions -->

</div><!-- end of map_mapedit -->

</div><!-- end of map_mapmenu -->
<div id="map_mapcanvas" class="map_mapcanvas"></div>
</div><!-- end of map_mappart -->
</div><!-- end of map_editor -->

</body>
</html>
