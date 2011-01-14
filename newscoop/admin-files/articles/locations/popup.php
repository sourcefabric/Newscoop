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

$map_article_spec = "" . $f_article_number . "_" . $f_language_id;

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Expires" content="now" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-picking.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-popups.css" />
	<title><?php putGS("Setting Map Locations"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css" />
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
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/country_cens.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/geocoding/geonames/search.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.dataTables.min.js"></script>

	<script type="text/javascript">
// prepare localized strings
var set_local_strings = function()
{
    var local_strings = {};

    local_strings["google_map"] = "<?php putGS("Google Streets"); ?>";
    local_strings["mapquest_map"] = "<?php putGS("MapQuest Map"); ?>";
    local_strings["openstreet_map"] = "<?php putGS("OpenStreet Map"); ?>";
    local_strings["fill_in_map_name"] = "<?php putGS("fill in map name"); ?>";
    local_strings["point_markers"] = "<?php putGS("Point markers"); ?>";
    local_strings["this_should_not_happen_now"] = "<?php putGS("problem at point processing, please send error report"); ?>";
    local_strings["really_to_delete_the_point"] = "<?php putGS("Really to delete the point?"); ?>";
    local_strings["the_removal_is_from_all_languages"] = "<?php putGS("The removal is from all language versions of the article."); ?>";
    local_strings["point_number"] = "<?php putGS("Point no."); ?>";
    local_strings["fill_in_the_point_description"] = "<?php putGS("fill in the point description"); ?>";
    local_strings["edit"] = "<?php putGS("Edit"); ?>";
    local_strings["center"] = "<?php putGS("Center"); ?>";
    local_strings["enable"] = "<?php putGS("Enable"); ?>";
    local_strings["disable"] = "<?php putGS("Disable"); ?>";
    local_strings["remove"] = "<?php putGS("Remove"); ?>";
    local_strings["longitude"] = "<?php putGS("Longitude"); ?>";
    local_strings["latitude"] = "<?php putGS("Latitude"); ?>";
    local_strings["locations_updated"] = "<?php putGS("List of locations updated"); ?>";

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
var findLocation = function(forced)
{
    var city_obj = document.getElementById ? document.getElementById("search-city") : null;
    var cc_obj = document.getElementById ? document.getElementById("search-country") : null;
    
    var cc_code = cc_obj.options[cc_obj.selectedIndex].value;
    
    var cities_term = city_obj.value.replace(/\*/g, "%");
    cities_term = cities_term.replace(/^\s+|\s+$/g, '');
    if (0 == cities_term.length)
    {
        if (forced)
        {
            if (cc_code in country_centers)
            {
                var cclon = country_centers[cc_code]['lon'];
                var cclat = country_centers[cc_code]['lat'];
                geo_locations.center_lonlat (cclon, cclat);
            }
        }

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

    $("#search_results").addClass("map_hidden");

    var showhide_link = document.getElementById ? document.getElementById("showhide_link") : null;
    showhide_link.innerHTML = "+";

    $("#map_geo_showhide").removeClass("map_hidden");

    geo_locations.map_update_side_desc_height();

    $("#map_geo_showhide .round-delete").removeClass("map_hidden");
    $("#map_geo_showhide .round-delete").addClass("round-plus");
};

// shows the city search results box
var showLocation = function()
{
    showhideState = true;

    $("#map_sidedescs").addClass("map_hidden");

    $("#search_results").removeClass("map_hidden");

    var showhide_link = document.getElementById ? document.getElementById("showhide_link") : null;
    showhide_link.innerHTML = "x";

    $("#map_geo_showhide").removeClass("map_hidden");

    geo_locations.map_update_side_desc_height();
    $("#map_sidedescs").removeClass("map_hidden");

    $("#map_geo_showhide .round-delete").removeClass("map_hidden");
    $("#map_geo_showhide .round-delete").removeClass("round-plus");
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
    var close_question = "<?php p(getGS("If you want to save your current changes, cancel this unloading first. Otherwise your unsaved changes will be lost.")); ?>";

    // does not work for opera (window.opera), no known workaround
    window.onbeforeunload = function ()
    {
        if (geo_locations.something_to_save)
        {
            return close_question;
        }
    }

    set_local_strings();
    $("#edit_tabs_all").tabs();
    //setTimeout(function() {
    geo_main_selecting_locations('<?php echo $geocodingdir; ?>', 'map_mapcanvas', 'map_sidedescs', '', '', true);
    init_search();
    //}, 1000);
    window.focus();
    window.geomap_art_spec_popup = "" + '<?php echo $map_article_spec; ?>';
    try {
        if (undefined !== window.opener.geomap_art_spec_popup)
        {
            window.opener.geomap_art_spec_popup = window.geomap_art_spec_popup;
        }
    }
    catch(e) {}

    set_to_opener = function()
    {
        try {
            if (undefined !== window.opener.geomap_art_spec_main)
            {
                if (window.opener.geomap_art_spec_main == window.geomap_art_spec_popup)
                {
                    if (null === window.opener.geomap_popup_editing)
                    {
                        window.opener.geomap_art_spec_popup = window.geomap_art_spec_popup;
                        window.opener.geomap_popup_editing = window;
                    }
                }
            }
        }
        catch(e) {}
        return;
    };

    var opener_sets = self.setInterval("set_to_opener()", 1000);
};

on_close_request = function()
{
    if (!geo_locations.something_to_save)
    {
        parent.$.fancybox.close();
        return;
    }

    var unsaved_question = "<?php p(getGS("You have unsaved changes. Should the changes be saved?")); ?>";
    var to_save = confirm(unsaved_question);

    if (to_save)
    {
        geo_locations.map_save_all();
        parent.$.fancybox.reload = true;
    }

    window.onbeforeunload = null;
    parent.$.fancybox.close();
}

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

<script type="text/javascript">

    $(function(){
		

		$('.icon-button').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);
		$('.text-button').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);
    });

	
</script>

</head>
<?php $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/'; ?>
<body onLoad="on_load_proc(); return false;" id="geolocation">
<div class="map_editor clearfix">
<!--Toolbar-->
<div id="map_save_part" class="toolbar clearfix">

    <div class="save-button-bar">
        <input id="map_button_save" type="submit" onclick="geo_locations.map_save_all(); parent.$.fancybox.reload = true; return false;" class="save-button-small" disabled="disabled" value="<?php putGS("Save"); ?>" name="save" />
        <input id="map_button_close" type="submit" onClick="on_close_request(); return false;" class="default-button" value="<?php putGS("Close"); ?>" name="close" />
    </div>
	<div id="map_save_info" class="map_save_info">
      <a href="#" class="map_name_display" id="map_name_display" onClick="geo_locations.map_edit_name(); return false;" title="<?php putGS("Setting the map name helps with map search"); ?>"><?php putGS("fill in map name"); ?></a>
        <input id="map_name_input" class="map_name_input map_hidden" type="text" size="40" maxlength="255" onChange="geo_locations.map_save_name(); return false;" onBlur="geo_locations.map_display_name(); return false;">
     </div>
    <!-- end of map_save_part -->
     
  </div>
<!--END Toolbar-->
<div class="clear" style="height:10px;"></div>
<div class="map_sidepan" id="map_sidepan">
<div class="map_menubar">
    <fieldset class="plain">
    <ul>
    	<li>
<select class="input_select map_geo_ccselect" id="search-country" name="geo_cc" onChange="findLocation(); return false;">
<option value="" selected="true"><?php putGS("any country"); ?></option>
<?php
foreach ($country_codes_alpha_2 as $cc_name => $cc_value) {
    echo '<option value="' . $cc_value . '">' . $cc_name . '</option>' . "\n";
}
?>
</select>
        </li>
    	<li>
        <form class="map_geo_city_search" onSubmit="findLocation(); return false;">
          <input class="map_geo_cityname input_text" id="search-city" type="text"><a href="#" class="ui-state-default icon-button no-text" onClick="findLocation(true); return false;"><span class="ui-icon ui-icon-triangle-1-e"></span></a><span id="map_geo_showhide" class=""><a href="#" id="showhide_link" class="round-delete map_hidden" onclick="showhideLocation(); return false;"></a></span>
        </form>
        </li>
    </ul>
    </fieldset>
</div><!-- end of map_menubar -->
<div id="side_info" class="side_info">
<div id="search_results" class="search_results map_hidden">&nbsp;</div>
<div id="map_sidedescs" class="map_sidedescs">&nbsp;</div>
</div><!--end of side_info -->
</div><!-- end of map_sidepan -->
<div class="map_mappart">
<div class="map_mapmenu">

<div class="map_mapinitview">
      <a class="ui-state-default text-button" href="#" onClick="geo_locations.map_showview(); return false;"><?php putGS("Last Saved Map View"); ?></a> </div>
<!-- end of map initview -->
<div class="map_resizing">
          <a href="#" class="ui-state-default icon-button no-text right-floated clear-margin" onClick="geo_locations.map_height_change(10); return false;"><span class="ui-icon ui-icon-triangle-1-e"></span></a>
          <div class="resize-label">V</div>
          <a href="#" class="ui-state-default icon-button no-text right-floated clear-margin" onClick="geo_locations.map_height_change(-10); return false;"><span class="ui-icon ui-icon-triangle-1-w"></span></a>
          <div id="map_view_size" class="map-resizing-text">600 x 400</div>
          <a href="#" class="ui-state-default icon-button no-text right-floated clear-margin" onClick="geo_locations.map_width_change(10); return false;"><span class="ui-icon ui-icon-triangle-1-e"></span></a>
          <div class="resize-label">H</div>
          <a href="#" class="ui-state-default icon-button no-text right-floated" onClick="geo_locations.map_width_change(-10); return false;"><span class="ui-icon ui-icon-triangle-1-w"></span></a>
</div><!-- end of map resizing -->
<div id="map_mapedit" class="map_mapedit map_hidden">
    <div class="map_editinner">
        <a onclick="geo_locations.close_edit_window(); return false;" href="#" class="ui-state-default icon-button no-text" style="position:absolute; top:16px; right:16px; z-index:3000;" ><span class="ui-icon ui-icon-closethick"></span></a>
        <div class="map_editpart1">
            <form action="#" onSubmit="return false";>
              <fieldset>

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
                    
                    <li>
                    <div id="edit_part_text" class="">
                    <label class="edit_label" for="point_descr"><!--Textual description:-->&nbsp;</label>
                    <textarea rows="5" cols="40" id="point_descr" name="point_descr" class="text" type="text" onChange="geo_locations.store_point_property('text', this.value); return false;">
</textarea>
                    </div>
                    <div id="edit_part_content" class="map_hidden">
                    <label class="edit_label" for="point_content"><!--HTML pop-up content:-->&nbsp;</label>
                    <textarea rows="5" cols="40" id="point_content" name="point_content" class="text" type="text" onChange="geo_locations.store_point_property('content', this.value); return false;">
</textarea>
                    </div>
                    <div id="edit_part_preview_outer" class="map_hidden">
                    <div class="popup_preview map_hidden" id="edit_part_preview"> </div>
                    </div>
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
                    <label class="edit_label" for="point_video"><span id="video_file_label_id"><?php putGS("Video ID"); ?>:</span><span id="video_file_label_file" class="map_hidden"><?php putGS("Video file"); ?>:</span></label>
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
      </div><!-- end of map_mapedit -->

    </div><!-- end of map_mapmenu -->
<div id="map_mapcanvas" class="map_mapcanvas"></div>
</div><!-- end of map_mappart -->
</div><!-- end of map_editor -->
</body>
</html>
