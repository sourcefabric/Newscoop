<?php

require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/locations/country_codes.php");

require_once($GLOBALS['g_campsiteDir'].'/classes/GeoPreferences.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMap.php');

camp_load_translation_strings('api');
camp_load_translation_strings('geolocation');
camp_load_translation_strings('home');

$f_language_id = Input::Get('f_language_selected', 'int', 0);
if (0 == $f_language_id) {
    $f_language_id = Input::Get('f_language_id', 'int', 0);
}
$f_article_number = Input::Get('f_article_number', 'int', 0);

$map_article_spec = '' . $f_article_number . '_' . $f_language_id;

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
    exit;
}

$articleObj = new Article($f_language_id, $f_article_number);



$cnf_html_dir = $Campsite['HTML_DIR'];
$cnf_website_url = $Campsite['WEBSITE_URL'];

$geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url);
$geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info['incl_obj']);
$geo_map_json = "";
$geo_map_json .= json_encode($geo_map_info['json_obj']);

$geo_map_usage = Geo_Map::ReadMapInfo('article', $f_article_number);
$geo_map_usage_json = "";
$geo_map_usage_json .= json_encode($geo_map_usage);

$geo_icons_info = Geo_Preferences::GetIconsInfo($cnf_html_dir, $cnf_website_url);
$geo_icons_json = "";
$geo_icons_json .= json_encode($geo_icons_info['json_obj']);


$geo_popups_info = Geo_Preferences::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
$geo_popups_json = "";
$geo_popups_json .= json_encode($geo_popups_info['json_obj']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Expires" content="now" />
    <title><?php putGS('Setting Map Locations'); ?></title>

        <?php include dirname(__FILE__) . '/../../html_head.php'; ?>

    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-picking.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/form.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/styles/map-info.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/openlayers/theme/default/style.css" />

<?php
    $include_files = Geo_Preferences::GetIncludeCSS($cnf_html_dir, $cnf_website_url);
    $include_files_css = $include_files["css_files"];
    $include_files_tags = "";
    foreach ($include_files_css as $css_file)
    {
        $include_files_tags .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_file\" />\n";
    }
    echo $include_files_tags;
?>

    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/base64.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/json2.js"></script>
        <?php echo $geo_map_incl; ?>

    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/map_popups.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/openlayers/OpenLayers.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/openlayers/OLlocals.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/location_chooser.js"></script>

    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/country_codes.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/country_cens.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/geocoding/geonames/search.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/jquery/jquery.dataTables.min.js"></script>

    <script type="text/javascript">

// prepare localized strings
var local_strings_map = {};

var set_local_strings = function()
{

    local_strings_map["fill_in_map_name"] = "<?php putGS('fill in map name'); ?>";
    local_strings_map["point_markers"] = "<?php putGS('Point markers'); ?>";
    local_strings_map["this_should_not_happen_now"] = "<?php putGS('problem at point processing, please send error report'); ?>";
    local_strings_map["really_to_delete_the_point"] = "<?php putGS('Really delete this point?'); ?>";
    local_strings_map["the_removal_is_from_all_languages"] = "<?php putGS('The point will be removed from all translations of the article.'); ?>";
    local_strings_map["point_number"] = "<?php putGS('Point no.'); ?>";
    local_strings_map["fill_in_the_point_description"] = "<?php putGS('Describe the location...'); ?>";
    local_strings_map["edit"] = "<?php putGS('Edit'); ?>";
    local_strings_map["edit_advanced"] = "<?php putGS('Advanced editing'); ?>";
    local_strings_map["center"] = "<?php putGS('Center'); ?>";
    local_strings_map["enable"] = "<?php putGS('Show'); ?>";
    local_strings_map["disable"] = "<?php putGS('Hide'); ?>";
    local_strings_map["remove"] = "<?php putGS('Delete location'); ?>";
    local_strings_map["remove_short"] = "<?php putGS('Delete'); ?>";
    local_strings_map["coordinates"] = "<?php putGS('Coordinates'); ?>";
    local_strings_map["longitude"] = "<?php putGS('Longitude'); ?>";
    local_strings_map["latitude"] = "<?php putGS('Latitude'); ?>";
    local_strings_map["locations_updated"] = "<?php putGS('List of locations updated'); ?>";
    local_strings_map["empty_label_show"] = "<?php putGS('Fill in location label'); ?>";

    geo_locations.set_display_strings(local_strings_map);

    local_strings_nam = {};

    local_strings_nam["cc"] = "+";
    local_strings_nam["city"] = "<?php putGS('Center map on location'); ?>";
    local_strings_nam["add_city"] = "<?php putGS('add location to map'); ?>";
    local_strings_nam["no_city_was_found"] = "<?php putGS('Sorry, that place was not found. Check your spelling or search again.'); ?>";

    geo_names.set_display_strings(local_strings_nam);

};
// prepare map settings
var useSystemParameters = function() {
<?php
    $article_spec_arr = array('language_id' => $f_language_id, 'article_number' => $f_article_number);
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

    $("#map_geo_showhide").removeClass("map_hidden");
    $("#map_geo_showhide").addClass("toggle_link_block");

    geo_locations.map_update_side_desc_height();

    $("#map_geo_showhide .round-delete").removeClass("map_hidden");
    $("#map_geo_showhide .round-delete").addClass("round-plus");

    $("#showhide_link").attr("title", "<?php putGS('Show search results'); ?>");
    $("#showhide_link").removeClass("map_hidden");
    $("#showhide_link").addClass("toggle_link_block");

    var showhide_link_label = document.getElementById ? document.getElementById("showhide_link_label") : null;
    if (showhide_link_label) {
        showhide_link_label.innerHTML = "<?php putGS('Show search results'); ?>";
    }

};

// shows the city search results box
var showLocation = function()
{
    showhideState = true;

    $("#map_sidedescs").addClass("map_hidden");

    $("#search_results").removeClass("map_hidden");

    $("#map_geo_showhide").removeClass("map_hidden");
    $("#map_geo_showhide").addClass("toggle_link_block");

    geo_locations.map_update_side_desc_height();
    $("#map_sidedescs").removeClass("map_hidden");

    $("#map_geo_showhide .round-delete").removeClass("map_hidden");
    $("#map_geo_showhide .round-delete").removeClass("round-plus");

    $("#showhide_link").attr("title", "<?php putGS('Hide search results'); ?>");
    $("#showhide_link").removeClass("map_hidden");
    $("#showhide_link").addClass("toggle_link_block");

    var showhide_link_label = document.getElementById ? document.getElementById("showhide_link_label") : null;
    if (showhide_link_label) {
        showhide_link_label.innerHTML = "<?php putGS('Hide search results'); ?>";
    }

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

var map_close_question = "<?php p(getGS('Are you sure you want to quit without saving your changes?')); ?>";

var set_pois_action = function(value)
{
    if (value) {
        geo_locations.ignore_select_event = true;
        geo_locations.show_edit_on_select = true;
    }
    else {
        geo_locations.ignore_select_event = false;
        geo_locations.show_edit_on_select = false;
    }
};

window.point_perex_focus = function()
{
    var point_perex = document.getElementById ? document.getElementById("point_perex") : null;
    if (point_perex) {
        point_perex.focus();
    }
}

var on_load_proc = function()
{
    geo_locations = new geo_locations_edit();
    geo_locations.set_obj_name("geo_locations");

    // does not work for opera (window.opera), no known workaround
    window.onbeforeunload = function ()
    {
        if (geo_locations.something_to_save)
        {
            return map_close_question;
        }
    }

    set_local_strings();
    $("#edit_tabs_all").tabs();
    useSystemParameters();
    geo_locations.main_openlayers_init('map_mapcanvas', 'map_sidedescs');

    set_pois_action(false);
    init_search();
    window.focus();
    window.geomap_art_spec_popup = "" + '<?php echo $map_article_spec; ?>';

};

on_close_request = function()
{
    if (geo_locations.something_to_save)
    {
        var to_close = confirm(map_close_question);
        if (!to_close)
        {
            return;
        }
        window.onbeforeunload = null;
    }

    try {
        if (parent.$.fancybox.reload) {
            parent.$.fancybox.message = '<?php putGS('Locations updated.'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
    return;
}

var map_show_preview = function(forced)
{
    if (!forced) {
        if (geo_locations.something_to_save) {
            geo_locations.go_to_preview_page = true; 
            geo_locations.map_save_all(); 
            return;
        }
    }

    if (!geo_locations.map_id) {return;}

    if (geo_locations.something_to_save)
    {
        var to_redirect = confirm(map_close_question);
        if (!to_redirect)
        {
            return;
        }
        window.onbeforeunload = null;
    }

    var cur_location = window.location.href;
    var new_location = cur_location.replace("popup.php", "preview.php");
    var new_location = new_location.replace("loader=article", "loader=map");
    new_location += "&focus=default";
    try {
    window.location.replace(new_location);
    } catch (e) {}
}

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
<?php $geocodingdir = $Campsite['WEBSITE_URL'] . '/js/geocoding/'; ?>
<body onLoad="on_load_proc(); return false;" id="geolocation">
<div class="map_editor clearfix">
<!--Toolbar-->
<div id="map_save_part" class="toolbar clearfix">

    <div class="save-button-bar">
        <input id="map_button_save" type="submit" onclick="geo_locations.map_save_all(); try {parent.$.fancybox.reload = true;} catch (e) {} return false;" class="save-button-small" disabled="disabled" value="<?php putGS('Save'); ?>" name="save" />
        <input id="map_button_preview" type="submit" onClick="try {parent.$.fancybox.reload = true;} catch (e) {} map_show_preview(); return false;" class="default-button" value="<?php putGS('Save'); ?> &amp; <?php putGS('Preview'); ?>" name="preview" />
        <input id="map_button_close" type="submit" onClick="on_close_request(); return false;" class="default-button" value="<?php putGS('Close'); ?>" name="close" />
    </div>
    <div id="map_save_info" class="map_save_info">
      <a href="#" class="map_name_display inline_editable" id="map_name_display" onClick="geo_locations.map_edit_name(); return false;" title="<?php putGS('Setting the map name helps with map search'); ?>"><?php putGS('fill in map name'); ?></a>
        <input id="map_name_input" class="map_name_input map_hidden" type="text" size="40" maxlength="255" onChange="geo_locations.map_save_name(); return false;" onBlur="geo_locations.map_display_name(); return false;" onKeyUp="geo_locations.map_save_name(); geo_locations.map_update_name_state(this.value); return true;">
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
            <label><span class="map_search_label"><?php putGS('Search for place or coordinate'); ?></span></label>
        </li>
        <li>
        <form class="map_geo_city_search" onSubmit="findLocation(); return false;">
          <input class="map_geo_cityname input_text" id="search-city" type="text"><a href="#" title="<?php putGS('Search for place or coordinate'); ?>" class="ui-state-default icon-button no-text" onClick="findLocation(true); return false;"><span class="ui-icon ui-icon-search"></span></a>
        </form>
        </li>
        <li>
<select class="input_select map_geo_ccselect" id="search-country" name="geo_cc" onChange="findLocation(); return false;">
<option value="" selected="true"><?php putGS('Specify country (optional)'); ?></option>
<?php
foreach ($country_codes_alpha_2 as $cc_name => $cc_value) {
    echo '<option value="' . $cc_value . '">' . $cc_name . '</option>' . "\n";
}
?>
</select>
        </li>
    </ul>
    </fieldset>
</div><!-- end of map_menubar -->
<div id="side_info" class="side_info">

<a class="toggle_link map_hidden" onclick="showhideLocation(); return false;" title="" id="showhide_link" href="#">
<span class="ui-icon ui-icon-triangle-2-n-s"></span>
<span id="showhide_link_label"><?php putGS('Hide search results'); ?></span></a>
<div id="search_results" class="search_results map_hidden">&nbsp;</div>
<div id="map_sidedescs" class="map_sidedescs">&nbsp;</div>
</div><!--end of side_info -->
</div><!-- end of map_sidepan -->
<div class="map_mappart">
<div class="map_mapmenu">

<div class="map_mapinitview">
      <a class="ui-state-default text-button" href="#" onClick="geo_locations.map_showview(); return false;"><?php putGS('Last Saved Map View'); ?></a> </div>
<!-- end of map initview -->
<div class="map_resizing">
          <a href="#" class="ui-state-default icon-button no-text right-floated clear-margin" onClick="geo_locations.map_height_change(10); return false;"><span class="ui-icon ui-icon-plus"></span></a>
          <div class="resize-label"><span class="geo_resize_hor ui-icon ui-icon-arrowthick-2-n-s">V</span></div>
          <a href="#" class="ui-state-default icon-button no-text right-floated clear-margin" onClick="geo_locations.map_height_change(-10); return false;"><span class="ui-icon ui-icon-minus"></span></a>
          <div id="map_view_size" class="map-resizing-text">600 x 400</div>
          <a href="#" class="ui-state-default icon-button no-text right-floated clear-margin" onClick="geo_locations.map_width_change(10); return false;"><span class="ui-icon ui-icon-plus"></span></a>
          <div class="resize-label"><span class="geo_resize_ver ui-icon ui-icon-arrowthick-2-e-w">H</span></div>
          <a href="#" class="ui-state-default icon-button no-text right-floated" onClick="geo_locations.map_width_change(-10); return false;"><span class="ui-icon ui-icon-minus"></span></a>
    <div class="map-resizing-text map_resizing_label"><?php putGS('Map size'); ?>:</div>
</div><!-- end of map resizing -->
<div id="map_mapedit" class="map_mapedit map_hidden">
    <div class="map_editinner">
        <a onclick="geo_locations.close_edit_window(); geo_locations.preview_edited(); return false;" href="#" class="ui-state-default icon-button button geo_close_edit_window"><span class=""><?php putGS('OK'); ?></span></a>
        <div class="map_editpart1">
            <form action="#" onSubmit="return false;">
              <fieldset>

                <div id="edit_tabs_all">
                    <ul>
                    <li><a href="#edit_basic"><?php putGS('text'); ?></a></li>
                    <li><a href="#edit_image" id="image_edit_part"><?php putGS('image'); ?></a></li>
                    <li><a href="#edit_video" id="video_edit_part"><?php putGS('video'); ?></a></li>
                    <li><a href="#edit_marker"><?php putGS('icon'); ?></a></li>
                </ul>
                <div id="edit_basic" class="edit_tabs">
                    <div class="edit_label_long map_poi_edit_intro"><?php putGS('Name and describe this location'); ?>.</div>

                    <ol>
                    <li class="edit_label_top">
                    <label class="edit_label" for="point_label"><?php putGS('Location label'); ?>:</label>
                    <input id="point_label" name="point_label" class="text" type="text" onChange="geo_locations.store_point_label(); return false;" onKeyUp="geo_locations.store_point_label(); return true;" />
                    </li>
                    <li id="edit_part_link" class="">
                    <label class="edit_label" for="point_link"><?php putGS('Label url'); ?>:</label>
                    <input id="point_link" name="point_link" class="text" type="text" onChange="geo_locations.store_point_property('link', this.value); return false;" onKeyUp="geo_locations.store_point_property('link', this.value); return true;" />
                    </li>

                    <li>
                    <label class="edit_label" for="point_descr"><?php putGS('Location description:'); ?></label>
                    <select class="text" id="point_predefined" name="point_predefined" onChange="geo_locations.store_point_direct(this.options[this.selectedIndex].value); return false;" onKeyUp="geo_locations.store_point_direct(this.options[this.selectedIndex].value); return true;">
                    <option value="0" selected="true"><?php putGS('plain text'); ?></option>
                    <option value="1"><?php putGS('html content'); ?></option>
                    </select>
                    <div id="edit_part_text" class="">
                    <textarea rows="5" cols="40" id="point_descr" name="point_descr" class="text geo_edit_textarea_text" type="text" title="<?php putGS('Describe the location...'); ?>" onChange="geo_locations.store_point_property('text', this.value); return false;" onKeyUp="geo_locations.store_point_property('text', this.value); return true;" onClick="if(local_strings_map['fill_in_the_point_description'] == this.value) {this.value = '';}" onBlur="if ('' == this.value) {this.value = local_strings_map['fill_in_the_point_description'];}">
</textarea>
                    </div>
                    <div id="edit_part_content" class="map_hidden">
                    <textarea rows="5" cols="40" id="point_content" name="point_content" class="text geo_edit_textarea_text" type="text" title="<?php putGS('Describe the location...'); ?>" onChange="geo_locations.store_point_property('content', this.value); return false;"  onKeyUp="geo_locations.store_point_property('content', this.value); return true;">
</textarea>
                    </div>
                    </li>
                    </ol>
                        </div>
                        <div id="edit_image" class="edit_tabs">
                            <div class="edit_label_long map_poi_edit_intro"><?php putGS('Add an image to this location'); ?>.</div>
<?php
    $image_desc_other = getGS('Fill in image link, like') . " " . "http://www.example.net/image.png";
?>
                    <ol>
                    <li class="edit_label_top">
                    <label class="edit_label" for="point_image"><?php putGS('Image URL'); ?>:</label>
                    <input id="point_image" name="point_image" class="text" type="text" onChange="geo_locations.store_point_property('image_source', this.value); return false;" onKeyUp="geo_locations.store_point_property('image_source', this.value); return true;" title="<?php echo $image_desc_other; ?>" />
                    <div class="poi_edit_description" id="geo_image_desc"><?php echo $image_desc_other; ?></div>
                    </li>

                    <li class="edit_label_long map_poi_edit_intro_sub"><?php putGS('Change image display size'); ?>.</li>

                    <li>
                    <label class="edit_label" for="point_image_height"><?php putGS('width'); ?>:</label>
                    <input id="point_image_width" name="point_image_height" class="text" type="text" onChange="geo_locations.store_point_property('image_width', this.value); return false;" onKeyUp="geo_locations.store_point_property('image_width', this.value); return true;" />
                    </li>
                    <li>
                    <label class="edit_label" for="point_image_height"><?php putGS('height'); ?>:</label>
                    <input id="point_image_height" name="point_image_height" class="text" type="text" onChange="geo_locations.store_point_property('image_height', this.value); return false;" onKeyUp="geo_locations.store_point_property('image_height', this.value); return true;" />
                    </li>
                    </ol>
                        </div>
                        <div id="edit_video" class="edit_tabs">
                            <div class="edit_label_long map_poi_edit_intro"><?php putGS('Add a video to this location'); ?>.</div>
<?php
    $video_desc_other = getGS('Fill in video ID, link or file name, for YouTube, Vimeo, or flash video.');
    $video_desc_youtube = getGS('Fill in youtube ID or link, e.g.') . ' ' . 'http://youtu.be/c9WzlvLn3X0';
    $video_desc_vimeo = getGS('Fill in vimeo ID or link, e.g') . ' ' . 'http://vimeo.com/21757310';
    $video_desc_local_swf = getGS('Fill in local swf flash file name or link, e.g.') . ' ' . 'http://www.example.net/video.swf';
    $video_desc_local_flv = getGS('Fill in local flv flash file name or link, e.g.') . ' ' . 'example.flv';
?>
                    <ol>
                    <li>
                    <label class="edit_label edit_label_top" for="point_video_type"><?php putGS('Video source'); ?>:</label>
                    <select class="text poi_video_type_selection" id="point_video_type" name="point_video_type" onChange="geo_locations.store_point_property('video_type', this.options[this.selectedIndex].value); return false;">
                    <option value="none" selected="true"><?php putGS('None'); ?></option>
                    <option value="youtube">Youtube</option>
                    <option value="vimeo">Vimeo</option>
                    <option value="flash">Flash (sfw)</option>
                    <option value="flv">Flash (flv)</option>
                    </select>
                    </li>

                    <li class="edit_label">
                    <label class="edit_label" for="point_video"><span id="video_file_label_id"><?php putGS('Video ID'); ?>:</span><span id="video_file_label_file" class="map_hidden"><?php putGS('Video file'); ?>:</span></label>
                    <input id="point_video" name="point_video" class="text" type="text" onChange="geo_locations.store_point_property('video_id', this.value); return false;" onKeyUp="geo_locations.store_point_property('video_id', this.value); return true;" title="<?php echo $video_desc_other; ?>" />
                    <div id="geo_video_desc_other" class="poi_edit_description"><?php echo $video_desc_other; ?></div>
                    <div id="geo_video_desc_youtube" class="map_hidden poi_edit_description"><?php echo $video_desc_youtube; ?></div>
                    <div id="geo_video_desc_vimeo" class="map_hidden poi_edit_description"><?php echo $video_desc_vimeo; ?></div>
                    <div id="geo_video_desc_local_swf" class="map_hidden poi_edit_description"><?php echo $video_desc_local_swf; ?></div>
                    <div id="geo_video_desc_local_flv" class="map_hidden poi_edit_description"><?php echo $video_desc_local_flv; ?></div>
                    </li>

                    <li class="edit_label_long map_poi_edit_intro_sub"><?php putGS('Change video display size'); ?>.</li>

                    <li>
                    <label class="edit_label" for="point_video_width"><?php putGS('width'); ?>:</label>
                    <input id="point_video_width" name="point_video_width" class="text" type="text" onChange="geo_locations.store_point_property('video_width', this.value); return false;" onKeyUp="geo_locations.store_point_property('video_width', this.value); return true;" />
                    </li>
                    <li>
                    <label class="edit_label" for="point_video_height"><?php putGS('height'); ?>:</label>
                    <input id="point_video_height" name="point_video_height" class="text" type="text" onChange="geo_locations.store_point_property('video_height', this.value); return false;" onKeyUp="geo_locations.store_point_property('video_height', this.value); return true;" />
                    </li>
                    </ol>
                        </div>
                        <div id="edit_marker" class="edit_tabs">
                            <div class="edit_label_long map_poi_edit_intro_icon"><?php putGS('Change icon of this location'); ?>.</div>

                        <div class="edit_marker_icons_all">
                        <ol>
                            <li>
                                <div id="edit_marker_selected" class="edit_marker_selected">
                                <?php putGS('selected marker icon'); ?>:&nbsp;
                                <img id="edit_marker_selected_src" src="">
                                </div>
                            </li>
                            <li>
                                <div class="edit_marker_choices" id="edit_marker_choices">&nbsp;</div>
                            </li>
                        </ol>
                        </div>

                        </div>
                </div>
              </fieldset>
            </form>

          </div><!-- end of map_editpart1 -->
        </div><!-- end of map_editinner -->
      </div><!-- end of map_mapedit -->

    </div><!-- end of map_mapmenu -->
<div id="map_mapcanvas" class="map_mapcanvas geo_map_mapcanvas"></div>
</div><!-- end of map_mappart -->
</div><!-- end of map_editor -->

</body>
</html>
