
// the main object to hold geo-things
function geo_locations_edit () {

this.obj_name = null;
this.editing = true;

// object for popup preview button
this.popup_prev_button = null;

this.set_obj_name = function(name) {
    this.obj_name = name;
    if (this.map && this.map.geo_obj) {
        this.map.geo_obj.obj_name = name;
    }
};

this.get_obj_name = function() {
    return this.obj_name;
};

// localization strings
this.display_strings = {
    google_map: "Google&nbsp;Maps",
    mapquest_map: "MapQuest&nbsp;Open",
    openstreet_map: "OpenStreetMap",
    fill_in_map_name: "fill in map name",
    point_markers: "Point markers",
    this_should_not_happen_now: "problem at point processing, please send error report",
    really_to_delete_the_point: "Really delete this point?",
    the_removal_is_from_all_languages: "The removal is from all language versions of the article.",
    point_number: "Point no.",
    fill_in_the_point_description: "Describe the location...",
    edit: "Edit",
    edit_advanced: "Advanced editing",
    center: "Center",
    enable: "Show",
    disable: "Hide",
    remove: "Delete location",
    remove_short: "Delete",
    coordinates: "Coordinates",
    longitude: "Longitude",
    latitude: "Latitude",
    locations_updated: "Locations updated",
    not_filled: "Some locations do not have filled description",
    empty_label_show: "Fill in location label",
    save_anyway: "Save anyway",
    back_to_editing: "Back to editing"
};

// flag saved state
this.something_to_save = false;
this.main_page_upload = false;

// specifying the article that the map is for
this.article_number = 0;
this.language_id = 0;

// flags for what has been changed
this.map_spec_changed = false;
this.poi_order_changed = false;

// marker icons paths and names, set during initialization
this.marker_src_base = "";
this.marker_src_default = "";
this.marker_src_default_ind = 0;
this.marker_src_labels = [];
this.marker_src_icons = {};

// what map provider should be used, and map position
this.map_view_layer_google = "googlev3";
this.map_view_layer_osm = "osm";
this.map_view_layer_mapquest = "mapquest";
this.map_view_layer_providers = {};
this.map_view_layer_providers[this.map_view_layer_google] = false;
this.map_view_layer_providers[this.map_view_layer_osm] = false;
this.map_view_layer_providers[this.map_view_layer_mapquest] = false;
// basic map display info
this.map_view_layer_names_all = {};
this.map_view_layer_default = "";
this.map_view_layer_name = "";
this.map_view_layer_name_saved = "";
this.map_view_layer_center_ini = {};
this.map_view_layer_center = null;
this.map_view_layer_center_saved = null;
this.map_view_layer_zoom = 0;
this.map_view_layer_zoom_saved = 0;
this.map_art_view_width_default = 0;
this.map_art_view_height_default = 0;

// values for popup style properties
this.popup_width = 0;
this.popup_height = 0;
this.popup_video_labels = [];
this.popup_video_props = {};

// values for the lines to display the proposed map size
this.map_art_view_width = 600;
this.map_art_view_height = 400;
this.map_art_view_top = 70;
this.map_art_view_right = 105;
this.map_art_view_width_display = 600;
this.map_art_view_height_display = 400;
this.map_art_view_top_display = 70;
this.map_art_view_right_display = 105;
this.map_border_zindex_on = 900;
this.map_border_zindex_off = -1000;
this.map_border_background = "#8080ff";
// basic map info
this.map_label_name = "";
this.map_id = 0;
this.map_name_max_length = 50;

// currently edited (via the edit link) point
this.edited_point = 0;
this.edit_text_mode = 'plain';
this.edit_view_mode = 'edit';


// the order of the pois done by drag-n-drop; we do not reorder pois in the layer
this.poi_order_user = [];

// map controls
this.select_control = null

// the pan zoom-and-bar control
this.pzb_ctrl = null;
this.not_to_pan_update = true;

// for ids of pop-ups
this.cur_pop_rank = 0;

// tha map layer
this.map = null;
// the markers layer
this.layer = null;

// saving info on markers that should be deleted from db
this.poi_deletion = [];

// info on markers, with the original ids, so that we can push changes into db
this.poi_markers = [];

// whether map is shown, used at the initial version
this.map_shown = false;
this.map_obj = null

// auxiliary index for accordion selection
this.poi_rank_out = -1;

// auxiliary for POI side-bar updates
this.descs_elm = null;
this.descs_elm_name = "";
this.descs_inner = "";

// count of POIs, with/without counting removals
this.descs_count = 0;
this.descs_count_inc = 0;

// not to make new POI on closing a pop-up
this.ignore_click = false;
// the used pop-up window
this.popup = null;

// for the accordion purposes
this.list_shown_header = 0;

// for inline editing purposes
this.poi_label_value_inline = null;
this.poi_content_value_inline = null;
this.poi_text_value_inline = null;
// for the save-and-preview as one action
this.go_to_preview_page = false;
// whether to show coordinates at poi listing
this.coordinates_to_show = false;

// setting the localized strings
this.set_display_strings = function(local_strings)
{
    if (!local_strings) {return;}

    var display_string_names = [
        "fill_in_map_name",
        "point_markers",
        "this_should_not_happen_now",
        "really_to_delete_the_point",
        "the_removal_is_from_all_languages",
        "point_number",
        "fill_in_the_point_description",
        "edit",
        "edit_advanced",
        "center",
        "enable",
        "disable",
        "remove",
        "remove_short",
        "coordinates",
        "longitude",
        "latitude",
        "locations_updated",
        "not_filled",
        "empty_label_show",
        "save_anyway",
        "back_to_editing"
    ];

    var str_count = display_string_names.length;
    for (var sind = 0; sind < str_count; sind++)
    {
        var cur_str_name = display_string_names[sind];

        if (undefined !== local_strings[cur_str_name])
        {
            this.display_strings[cur_str_name] = local_strings[cur_str_name];
        }
    }

};

// setting the article info
this.set_article_spec = function(params)
{
    this.article_number = parseInt(params.article_number);
    this.language_id = parseInt(params.language_id);
};

// max map sizes
this.map_limit_width_display = 800;
this.map_limit_height_display = 500;
this.map_limit_width_view = 1200;
this.map_limit_height_view = 1200;

// setting the map width
this.set_map_width = function(width, set_view)
{

    var partial_change = false;
    if (this.map_limit_width_display > this.map_art_view_width)
    {
        if (this.map_limit_width_display < width) {partial_change = true;}
    }
    if (this.map_limit_width_display < this.map_art_view_width)
    {
        if (this.map_limit_width_display > width) {partial_change = true;}
    }
    if (partial_change)
    {
        this.map_width_change(this.map_limit_width_display - this.map_art_view_width, false);
    }

    var width_diff = this.map_art_view_width_default - this.map_art_view_width;

    this.map_width_change(width_diff, false);

};

// setting the map height
this.set_map_height = function(height, set_view)
{

    var partial_change = false;
    if (this.map_limit_height_display > this.map_art_height_width)
    {
        if (this.map_limit_height_display < height) {partial_change = true;}
    }
    if (this.map_limit_height_display < this.map_art_view_height)
    {
        if (this.map_limit_height_display > height) {partial_change = true;}
    }
    if (partial_change)
    {
        this.map_height_change(this.map_limit_height_display - this.map_art_view_height, false);
    }

    var height_diff = this.map_art_view_height_default - this.map_art_view_height;

    this.map_height_change(height_diff, false);

};

// setting the db based default info
this.set_map_info = function(params)
{
    this.map_view_layer_default = params['default'];
    var prov_len = params.providers.length;
    for (var pind = 0; pind < prov_len; pind++)
    {
        this.map_view_layer_providers[params.providers[pind]] = true;
    }

    this.map_view_layer_center_ini = {"longitude": params.longitude, "latitude": params.latitude};
    this.map_view_layer_zoom = parseInt(params.resolution);
    this.map_view_layer_zoom_saved = this.map_view_layer_zoom;

    this.map_art_view_width_default = parseInt(params.width);
    this.map_art_view_height_default = parseInt(params.height);

};

// setting the basic map info for the current map
this.set_map_usage = function(params, set_view)
{
    this.map_id = params["id"];
    if (0 == this.map_id) {return;}

    var longitude = params.lon;
    var latitude = params.lat;

    this.map_view_layer_center_ini = {"longitude": longitude, "latitude": latitude};
    this.map_view_layer_zoom = parseInt(params.res);
    this.map_view_layer_zoom_saved = this.map_view_layer_zoom;

    this.map_label_name = params.name;
    // set the map name to divs
    this.map_load_name();

    this.map_art_view_width_default = parseInt(params.width);
    this.map_art_view_height_default = parseInt(params.height);

    if (set_view)
    {
        this.set_map_width(this.map_art_view_width_default, set_view);
        this.set_map_height(this.map_art_view_height_default, set_view);
    }

    this.map_view_layer_default = params.prov;

    if (this.map)
    {
        var layer_name = this.map_view_layer_names_all[this.map_view_layer_default];
        if (layer_name && ("" != layer_name))
        {
            this.map_view_layer_name = layer_name;
            this.map_view_layer_name_saved = layer_name;
        }
        this.map_view_layer_center = new OpenLayers.LonLat(longitude, latitude).transform(
            new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject()
        );
        this.map_view_layer_center_saved = new OpenLayers.LonLat(longitude, latitude).transform(
            new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject()
        );
    }

    if (set_view)
    {
        this.map_showview();
    }
};

// setting info on available marker icons
this.set_icons_info = function(params)
{
    this.marker_src_base = params.webdir;
    this.marker_src_default = params['default'];
    this.marker_src_labels = [];
    this.marker_src_icons = {};

    var icon_len = params.icons.length;
    for (var iind = 0; iind < icon_len; iind++)
    {
        var cur_icon = params.icons[iind];
        var cur_label = cur_icon.label;

        if (cur_icon['name'] == this.marker_src_default)
        {
            this.marker_src_default_ind = iind;
        }

        this.marker_src_labels.push(cur_label);
        this.marker_src_icons[cur_label] = {
            "name": cur_icon["name"],
            "path": cur_icon["path"],
            "width": parseFloat(cur_icon["width"]),
            "height": parseFloat(cur_icon["height"]),
            "width_off": parseFloat(cur_icon["width_off"]),
            "height_off": parseFloat(cur_icon["height_off"])
        };
    }

};

// setting popups properties
this.set_popups_info = function(params)
{

    this.popup_width = params.width;
    this.popup_height = params.height;

    var video = params.video;

    var video_len = video.labels.length;
    for (var vind = 0; vind < video_len; vind++)
    {
        var cur_video = video.labels[vind];
        var cur_label = cur_video["label"].toLowerCase();
        this.popup_video_labels.push(cur_label);
        this.popup_video_props[cur_label] = {
            "source": cur_video["source"],
            "width": cur_video["width"],
            "height": cur_video["height"],
            "path": cur_video["path"]
        };
    }

};

// moving point to a position
this.update_poi_position = function(index, coordinate, value, input)
{
    var feature = this.layer.features[index];
    if ((undefined === feature) || (undefined === feature.attributes))
    {
      return;
    }

    this.set_save_state(true);

    var cur_poi_info = this.poi_markers[index];

    var longitude = cur_poi_info['lon'];
    var latitude = cur_poi_info['lat'];

    if ('longitude' == coordinate)
    {
        longitude = parseFloat(value);
        if ( isNaN(longitude))
        {
            input.value = cur_poi_info['lon'];
            return;
        }
    }
    if ('latitude' == coordinate)
    {
        latitude = parseFloat(value);
        if (isNaN(latitude))
        {
            input.value = cur_poi_info['lat'];
            return;
        }
    }

    var lonlat = new OpenLayers.LonLat(longitude, latitude);

    if (cur_poi_info.in_db)
    {
        if ((lonlat.lon != cur_poi_info['lon']) || (lonlat.lat != cur_poi_info['lat']))
        {
            cur_poi_info.location_changed = true;
        }
    }

    cur_poi_info['lon'] = lonlat.lon;
    cur_poi_info['lat'] = lonlat.lat;

    lonlat.transform(new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject());

    cur_poi_info['map_lon'] = lonlat.lon;
    cur_poi_info['map_lat'] = lonlat.lat;

    var pixel = this.map.getViewPortPxFromLonLat(lonlat);
    feature.move(pixel);
    if (this.popup && (feature == this.popup.feature)) {
        pixel = this.map.getLayerPxFromLonLat(lonlat);
        this.popup.moveTo(pixel);
    }

    OpenLayers.HooksLocal.map_check_pois(this);
};

// closes (pre)view popup, if of specified poi
this.close_popup = function(index)
{
    var geo_obj = this;

    if (this.popup)
    {
        var feature = (this.popup.feature) ? this.popup.feature : null;
        var m_rank = this.popup.m_rank;
        if ((undefined !== m_rank) && (index == m_rank)) {
            // this pop-up removal seems to be sometimes strange
            try {
                geo_obj.select_control.unselect(geo_obj.popup.feature);
                if (feature) {
                    feature.popup = null;
                }
                if (this.popup) {
                    this.map.removePopup(this.popup);
                    this.popup.destroy();
                }
            }
            catch (e) {alert(JSON.stringify(e));}
            this.popup = null;
        }
    }
};

// setting the edit window for the requested POI (bound on the 'edit' link)
this.edit_poi = function(index)
{
    this.close_popup(index);

    this.edited_point = index;
    this.load_point_data();
    this.open_edit_window();

    return;
};

// to center the map view on the requested position
this.center_lonlat = function(longitude, latitude)
{
    var lonLat = new OpenLayers.LonLat(longitude, latitude).transform(
        new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
        this.map.getProjectionObject() // to Spherical Mercator Projection
    );

    this.map.setCenter (lonLat);
};

// sets map center onto the requested POI (bound on the 'center' link)
this.center_poi = function(index)
{
    var mlon = this.poi_markers[index].map_lon;
    var mlat = this.poi_markers[index].map_lat;
    var lonLat = new OpenLayers.LonLat(mlon, mlat);

    this.map.setCenter (lonLat);

};

// this function was used during development; probably will be removed
this.display_index = function(index)
{
    var real_index = -1;

    for (var rind = 0; rind <= index; rind++)
    {
        if (this.poi_markers[rind]['usage']) {real_index += 1;}
        else
        {
            alert(this.display_strings.this_should_not_happen_now + ": " + rind + " / " + index);
        }
    }
    return real_index;
};

// setting the point visibility state
this.set_usage_poi = function(index, usage, div_ids)
{
    this.set_save_state(true);

    var to_disable = !usage;

    var layer_index = this.display_index(index);

    var feature = this.layer.features[layer_index];
    var attrs = feature.attributes;

    var cur_poi_info = this.poi_markers[layer_index];
    if (cur_poi_info.in_db)
    {
        if (to_disable != attrs.m_disabled)
        {
            cur_poi_info.content_changed = true;
            cur_poi_info.state_changed = true;
            this.main_page_upload = true;
        }
    }
    attrs.m_disabled = to_disable;

    var div_objs = [];
    var div_count = div_ids.length;
    for (var dind = 0; dind < div_count; dind++)
    {
        var one_obj = document.getElementById ? document.getElementById(div_ids[dind]) : null;
        div_objs.push(one_obj);
    }

    var lon_id = div_ids[0];
    var lat_id = div_ids[1];
    var dis_id = div_ids[2];
    var ena_id = div_ids[3];
    var voi_id = div_ids[4];
    var rem_id = div_ids[5];

    var lon_obj = div_objs[0];
    var lat_obj = div_objs[1];
    var dis_obj = div_objs[2];
    var ena_obj = div_objs[3];
    var voi_obj = div_objs[4];
    var rem_obj = div_objs[5];

    var marker_type = 2 * attrs.m_type;
    if (to_disable) {marker_type += 1;}
    attrs.type = marker_type;
    this.layer.redraw();

    if (usage)
    {
        lon_obj.disabled = false;
        lat_obj.disabled = false;

        $(dis_obj).removeClass('map_hidden');
        $(voi_obj).removeClass('map_hidden');

        $(ena_obj).addClass('map_hidden');
        $(rem_obj).addClass('map_hidden');
    }
    else
    {
        lon_obj.disabled = true;
        lat_obj.disabled = true;

        $(ena_obj).removeClass('map_hidden');
        $(rem_obj).removeClass('map_hidden');

        $(dis_obj).addClass('map_hidden');
        $(voi_obj).addClass('map_hidden');
    }
};

// removal of the requested POI (bound on the 'remove' link)
this.remove_poi = function(index)
{
    var confirm_string = this.display_strings.really_to_delete_the_point;
    confirm_string += "\n\n";
    confirm_string += this.display_strings.the_removal_is_from_all_languages;

    var really = confirm(confirm_string);
    if (!really) {return;}

    var layer_index = this.display_index(index);

    var feature_rem = this.layer.features[layer_index];
    if (this.popup && (feature_rem == this.popup.feature))
    {
        // this pop-up removal seems to be sometimes strange
        try {
            this.map.removePopup(this.popup);
            this.popup.destroy();
        }
        catch (e) {}
        this.popup = null;
    }

    this.close_edit_window();

    this.set_save_state(true);
    this.poi_order_changed = true;

    var to_remove = [];
    to_remove.push(this.layer.features[layer_index])
    this.layer.removeFeatures(to_remove);

    this.poi_markers[index].usage = false;
    var cur_marker = this.poi_markers[index];
    if (cur_marker.in_db)
    {
        this.poi_deletion.push({'content_id': cur_marker.con_index, 'location_id': cur_marker.loc_index});
    }

    this.poi_markers.splice(index, 1);

    var features = this.layer.features;
    var feature_count = features.length;
    for (var find = 0; find < feature_count; find++)
    {
        var one_feature = features[find];
        var f_rank = one_feature.attributes.m_rank;
        if (f_rank > index)
        {
            one_feature.attributes.m_rank = f_rank - 1;
        }
    }

    var poi_order_new = [];
    var pind_count = this.poi_order_user.length;
    for (var pind = 0; pind < pind_count; pind++)
    {
        var one_ord = this.poi_order_user[pind];
        if (one_ord < index)
        {
            poi_order_new.push(one_ord);
            continue;
        }
        if (one_ord > index)
        {
            poi_order_new.push(one_ord - 1);
            continue;
        }
    }
    this.poi_order_user = poi_order_new;

    this.descs_count -= 1;

    this.update_poi_descs();
};

this.set_inline_label_value = function(index)
{
    var geo_obj = this;
    var erase_inline_value = false;

    var label_edit_elm = document.getElementById ? document.getElementById('geo_edit_label_inline') : null;
    if (label_edit_elm) {
        if (null !== this.poi_label_value_inline) {
            label_edit_elm.value = this.poi_label_value_inline;
        }
        erase_inline_value = true;
    } else {
        if (this.popup) {
            setTimeout(geo_obj.obj_name + ".set_inline_label_value(" + index + ");", 100);
        } else {
            erase_inline_value = true;
        }
    }

    if (erase_inline_value) {
        this.poi_label_value_inline = null;
    }

};

this.set_inline_desc_value = function(form, index)
{
    var geo_obj = this;
    var erase_inline_value = false;

    var dom_elm_name = "";
    var known_dom_names = {"content": "geo_edit_content_inline", "text": "geo_edit_text_inline"};
    if (form in known_dom_names) {
        dom_elm_name = known_dom_names[form];
    } else {
        return;
    }

    var desc_value = null;
    if ("content" == form) {
        desc_value = this.poi_content_value_inline;
    }
    if ("text" == form) {
        desc_value = this.poi_text_value_inline;
    }

    var desc_edit_elm = document.getElementById ? document.getElementById(dom_elm_name) : null;
    if (desc_edit_elm) {
        if (null !== desc_value) {
            desc_edit_elm.value = desc_value;
        }
        erase_inline_value = true;
    } else {
        if (this.popup) {
            setTimeout(geo_obj.obj_name + ".set_inline_desc_value(\"" + form + "\", " + index + ");", 100);
        } else {
            erase_inline_value = true;
        }
    }

    if (erase_inline_value) {
        this.poi_label_value_inline = null;
        if ("content" == form) {
            this.poi_content_value_inline = null;
        }
        if ("text" == form) {
            this.poi_text_value_inline = null;
        }
    }

};

this.is_poi_visible = function(index)
{
    var poi_visible = false;

    if (this.poi_markers && this.poi_markers[index])
    {
        var cur_poi_info = this.poi_markers[index];
        var longitude = cur_poi_info['map_lon'];
        var latitude = cur_poi_info['map_lat'];
        var poi_lonlat = new OpenLayers.LonLat(longitude, latitude);

        var view_box = this.map.calculateBounds();
        if (view_box.containsLonLat(poi_lonlat, false)) {
            poi_visible = true;
        }
    }

    return poi_visible;
};

this.preview_edited = function()
{
    var index = this.edited_point;
    this.close_popup(index);

    var poi_visible = this.is_poi_visible(index);
    if (!poi_visible) {
        this.center_poi(index);
    }

    OpenLayers.HooksPopups.on_map_feature_select(this, index);
};

this.select_poi_on_list = function(index)
{
    var poi_visible = this.is_poi_visible(index);
    if (!poi_visible) {
        this.center_poi(index);
    }

    OpenLayers.HooksPopups.on_map_feature_select(this, index);
};

// updates the permuation of POIs (via UI 'sortable', or after a POI removal)
this.poi_order_update = function(poi_order_new)
{
    this.poi_order_user = [];
    var transed = false;

    var poi_order_length = poi_order_new.length;
    for (var pind = 0; pind < poi_order_length; pind++)
    {
        var cur_poi_desc = poi_order_new[pind];
        var cur_poi_list = cur_poi_desc.split("_");
        var cur_poi_ind = parseInt(cur_poi_list[cur_poi_list.length - 1]);
        this.poi_order_user.push(cur_poi_ind);
        if (pind != cur_poi_ind) {transed = true;}
    }

    if (transed)
    {
        this.set_save_state(true);
        this.poi_order_changed = true;
    }
};

// finds the 'sorted' position of the requested POI
this.poi_order_revert = function(index)
{
    var rev_index = 0;
    var found = false;

    var poi_count = this.poi_order_user.length;
    for (var pind = 0; pind < poi_count; pind++)
    {
        if (index == this.poi_order_user[pind])
        {
            rev_index = pind;
            found = true;
            break;
        }
    }

    if (!found) {alert(this.display_strings.this_should_not_happen_now + " - " + "reversion");}

    return rev_index;
};

// for updating the side-bar with POI links
this.update_poi_descs = function(active, index_type)
{
    this.list_shown_header = 0;

    var geo_obj = this;
    var obj_name = this.get_obj_name();

    if (0 == this.poi_markers.length)
    {
        this.descs_elm.innerHTML = "<div class='map_poi_side_list' id='map_poi_side_list'>" + " " + "</div>";
        this.map_update_side_desc_height();
        return;
    }

    if (undefined === active) {active = 0;}
    else
    {
        active = this.poi_order_revert(active);
    }

    var view_index = 0;
    if ((undefined === index_type) || ('view' != index_type))
    {
        view_index = this.display_index(active);
    }
    this.list_shown_header = view_index;

    var max_ind = this.poi_order_user.length - 1;

    var descs_inner = "";
    var disp_index = 1;
    var pind = 0; // real initial poi index

    for(var sind = 0; sind <= max_ind; sind++)
    {
        pind = this.poi_order_user[sind];
        disp_index = pind + 1;

        var cur_poi = this.poi_markers[pind];
        if (!cur_poi.usage) {alert(this.display_strings.this_should_not_happen_now); continue;}

        // these two helper classes are not used now
        var use_class = "";
        var class_show = "";

        var cur_label = "";
        var cur_marker = null;
        if (disp_index <= this.layer.features.length)
        {
            cur_marker = this.layer.features[disp_index - 1];
            cur_label = cur_marker.attributes.m_title;
        }

        var cur_label_sep = "";
        if (0 < cur_label.length)
        {
            cur_label_sep = ": ";
        }

        var max_len = 20;
        if (max_len < cur_label.length)
        {
            cur_label = cur_label.substr(0, max_len) + "...";
        }

        cur_label = cur_label.replace(/&/gi, "&amp;");
        cur_label = cur_label.replace(/</gi, "&lt;");
        cur_label = cur_label.replace(/>/gi, "&gt;");

        descs_inner += "<div id=\"poi_seq_" + pind + "\">";
        descs_inner += "<h3 class=\"" + use_class + class_show + " map_poi_side_one\">";
        descs_inner += "<a href=\"#\" class='poi_name' onClick='" + obj_name + ".select_poi_on_list(" + pind + "); return false;'>" + disp_index + cur_label_sep + cur_label + "</a>";
        descs_inner += "<a onclick='" + obj_name + ".remove_poi(" + pind + ");return false;' href=\"#\" class=\"corner-button\" title=\"" + this.display_strings.remove + "\"><span class=\"ui-icon ui-icon-closethick\"></span></a>";
        descs_inner += "</h3>";

        descs_inner += "<div class='poi_actions_all'>";

        var disable_value = "";
        if (cur_marker && cur_marker.attributes.m_disabled) {disable_value = " disabled='disabled'";}

        var lon_id = "list_change_poi_longitude_" + pind;
        var lat_id = "list_change_poi_latitude_" + pind;
        var dis_id = "list_change_poi_disable_" + pind;
        var ena_id = "list_change_poi_enable_" + pind;
        var voi_id = "list_change_poi_void_" + pind;
        var rem_id = "list_change_poi_remove_" + pind;

        var dis_class = "list_change_poi_disable";
        var ena_class = "list_change_poi_enable";
        var voi_class = "list_change_poi_void";
        var rem_class = "list_change_poi_remove";

        if (cur_marker && cur_marker.attributes.m_disabled)
        {
            dis_class += " map_hidden";
            voi_class += " map_hidden";
        }
        else
        {
            ena_class += " map_hidden";
            rem_class += " map_hidden";
        }

        var prop_ids = '["' + lon_id + '", "' + lat_id + '", "' + dis_id + '", "' + ena_id + '"]';

        descs_inner += "<div class='poi_actions clearfix'>";
        descs_inner += "<a href='#' class='link left-floated' onclick='" + obj_name + ".center_poi(" + pind + ");return false;'>" + this.display_strings.center + "</a>";

        descs_inner += "<span id='" + ena_id + "' class='" + ena_class + "'><a href='#' class='link left-floated' onclick='" + obj_name + ".set_usage_poi(" + pind + ", true, " + prop_ids + ");return false;'>" + this.display_strings.enable + "</a></span>";
        descs_inner += "<span id='" + dis_id + "' class='" + dis_class + "'><a href='#' class='link left-floated' onclick='" + obj_name + ".set_usage_poi(" + pind + ", false, " + prop_ids + ");return false;'>" + this.display_strings.disable + "</a></span>";
        descs_inner += "<a href='#' class='link icon-link right-floated' onclick='" + obj_name + ".edit_poi(" + pind + ");return false;'><span class='icon ui-icon-pencil'></span><strong>" + this.display_strings.edit + "</strong></a>";
        descs_inner += "</div>";

        var coor_class_show = " map_hidden";
        var fset_class_show = " closed";
        if (this.coordinates_to_show) {
            coor_class_show = "";
            fset_class_show = "";
        }

        descs_inner += "<fieldset class='poi_coors_all_set toggle " + fset_class_show + "'>";
        descs_inner += "<legend class='poi_coors_all_legend' style='cursor: pointer;' onClick=\"$('.poi_coors_all').toggleClass('map_hidden'); $('.poi_coors_all_set').toggleClass('closed'); " + obj_name + ".coordinates_to_show = !" + obj_name + ".coordinates_to_show; return false;\"><span class='show_hide_coordinates ui-icon ui-icon-triangle-2-n-s'></span>" + this.display_strings.coordinates + "</legend>";
        descs_inner += "<div class='poi_coors_all " + coor_class_show + "'>";
        descs_inner += "<div class='poi_coors'>";
        descs_inner += "<label>" + this.display_strings.latitude + "</label><input id='" + lat_id + "' class='poi_coors_input' size='9' onChange='" + obj_name + ".update_poi_position(" + pind + ", \"latitude\", this.value, this); return false;' name='poi_latitude_" + pind + "' value='" + cur_poi.lat.toFixed(6) + "'" + disable_value + ">";
        descs_inner += "</div>";
        descs_inner += "<div class='poi_coors'>";
        descs_inner += "<label>" + this.display_strings.longitude + "</label><input id='" + lon_id + "' class='poi_coors_input' size='9' onChange='" + obj_name + ".update_poi_position(" + pind + ", \"longitude\", this.value, this); return false;' name='poi_longitude_" + pind + "'  value='" + cur_poi.lon.toFixed(6) + "'" + disable_value + ">";
        descs_inner += "</div>";
        descs_inner += "</div>";
        descs_inner += "</fieldset>";

        descs_inner += "</div>";
        descs_inner += "</div>";

        disp_index += 1;
    }
    this.descs_elm.innerHTML = "<div id='map_poi_side_list' class='map_poi_side_list'>" + descs_inner + "</div>";

    // putting the list into UI accordion
    $(function() {
        var stop = false;
        $( "#map_poi_side_list h3" ).click(function( event ) {
            if ( stop ) {
                event.stopImmediatePropagation();
                event.preventDefault();
                stop = false;
            }
        });

        $("#map_poi_side_list").accordion({animated: false, autoHeight: false, active: view_index, header: "> div > h3", change: function(event, ui) {geo_obj.on_accordion_change(event, ui);} }).sortable({axis: "y", handle: "h3", stop: function() {stop = true;} });

        $("#map_poi_side_list").bind( "sortupdate", function(event, ui) {
            var poi_order = $(this).sortable('toArray');
            geo_obj.poi_order_update(poi_order);
        });

        $('#map_poi_side_list .icon-button').hover(
            function() { $(this).addClass('ui-state-hover'); }, 
            function() { $(this).removeClass('ui-state-hover'); }
        );
        $('#map_poi_side_list .text-button').hover(
            function() { $(this).addClass('ui-state-hover'); }, 
            function() { $(this).removeClass('ui-state-hover'); }
        );

    });
    this.map_update_side_desc_height();

};

// for keeping track of the displayed accordion part
this.on_accordion_change = function(event, ui)
{
    this.list_shown_header = -1;

    try {
        var index_html = this.obj_name + ".select_poi_on_list";
        var header_html = ui.newHeader[0].innerHTML;
        var index_start = header_html.search(index_html);
        if (0 <= index_start) {
            var rank_start = index_start + index_html.length + 1;
            var rank = parseFloat(header_html.substring(rank_start));
            this.list_shown_header = rank.toFixed();
        }
    }
    catch (exc) {}
};

// sets the height of the side-bar part with POIs, so that it fits into the rest of the side-bar
this.map_update_side_desc_height = function()
{
    var searchres_obj = document.getElementById ? document.getElementById("search_results") : null;
    var height_taken = searchres_obj.offsetHeight;

    var sidedesc_obj = document.getElementById ? document.getElementById("map_sidedescs") : null;

    var new_height = 480 - height_taken;
    if ((!new_height) || (250 > new_height)) {new_height = 250;}

    {
        sidedesc_obj.style.height = new_height + "px";
    }
};

// taking POI-mouse offset on the start of a POI dragging
this.poi_dragg_start = function(feature, pixel)
{
    this.poi_drag_offset = null;

    if ((undefined === feature.attributes) || (undefined === feature.attributes.m_rank))
    {
      return;
    }

    var index = feature.attributes.m_rank;
    var cur_poi_info = this.poi_markers[index];

    var lonlat = this.map.getLonLatFromViewPortPx(pixel);

    cur_poi_info['map_lon_offset'] = lonlat.lon - cur_poi_info['map_lon'];
    cur_poi_info['map_lat_offset'] = lonlat.lat - cur_poi_info['map_lat'];

};

// updating info on POI after it was dragged
this.poi_dragged = function(feature, pixel)
{
    if ((undefined === feature.attributes) || (undefined === feature.attributes.m_rank))
    {
      return;
    }

    this.set_save_state(true);

    var index = feature.attributes.m_rank;
    var cur_poi_info = this.poi_markers[index];
    var map = this.map;

    var lonlat = map.getLonLatFromViewPortPx(pixel);

    lonlat.lon -= cur_poi_info['map_lon_offset'];
    lonlat.lat -= cur_poi_info['map_lat_offset'];

    cur_poi_info['map_lon'] = lonlat.lon;
    cur_poi_info['map_lat'] = lonlat.lat;

    lonlat_map = lonlat.clone();
    lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));

    if (cur_poi_info.in_db)
    {
        if ((lonlat.lon != cur_poi_info['lon']) || (lonlat.lat != cur_poi_info['lat']))
        {
            cur_poi_info.location_changed = true;
        }
    }
    cur_poi_info['lon'] = lonlat.lon;
    cur_poi_info['lat'] = lonlat.lat;

    this.update_poi_descs(index);

    // to move the POI's pop-up too, if it is displayed
    if (this.popup && (feature == this.popup.feature)) {
        pixel = this.map.getLayerPxFromLonLat(lonlat_map);
        this.popup.moveTo(pixel);
    }
};

// actual insertion of a new POI
this.insert_poi = function(coor_type, lonlat_ini, longitude, latitude, label)
{
    if ((undefined === coor_type) || (undefined === lonlat_ini))
    {
        return false;
    }

    if (null === lonlat_ini)
    {
        if ((undefined === longitude) || (undefined === latitude))
        {
            return false;
        }
        lonlat_ini = new OpenLayers.LonLat(longitude, latitude);
    }

    this.set_save_state(true);
    this.poi_order_changed = true;

    var lonlat = null;
    if ('map' == coor_type)
    {
        lonlat = lonlat_ini.clone();
    }
    else
    {
        lonlat = new OpenLayers.LonLat(lonlat_ini.lon, lonlat_ini.lat).transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            this.map.getProjectionObject() // to Spherical Mercator Projection
        );
    }

    var poi_title = "";

    if (undefined !== label)
    {
        poi_title = label;
    }

    // making poi for features
    var features = [];
    var point = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
    var vector = new OpenLayers.Feature.Vector(point, {type: (2 * this.marker_src_default_ind)});

    var poi_index = this.descs_count;

    this.poi_rank_out = this.descs_count;
    vector.attributes.m_rank = this.descs_count;
    vector.attributes.m_title = poi_title;
    vector.attributes.m_perex = "";
    vector.attributes.m_direct = false;
    vector.attributes.m_content = "";
    vector.attributes.m_link = "";
    vector.attributes.m_text = "";
    vector.attributes.m_image_mm = 0;
    vector.attributes.m_image_source = "";
    vector.attributes.m_image_width = "";
    vector.attributes.m_image_height = "";
    vector.attributes.m_image_share = true;
    vector.attributes.m_video_mm = 0;
    vector.attributes.m_video_type = "";
    vector.attributes.m_video_id = "";
    vector.attributes.m_video_width = "";
    vector.attributes.m_video_height = "";
    vector.attributes.m_video_share = true;
    vector.attributes.m_image = "";
    vector.attributes.m_embed = "";
    vector.attributes.m_disabled = false;
    vector.attributes.m_type = this.marker_src_default_ind;

    features.push(vector);

    // setting feature-based classical-shaped marker
    this.layer.addFeatures(features);

    var map_lon = lonlat.lon;
    var map_lat = lonlat.lat;

    if ('map' == coor_type)
    {
        lonlat.transform(this.map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
    }
    else
    {
        lonlat = lonlat_ini.clone();
    }

    this.poi_markers.push({
        'lon':lonlat.lon, 'lat':lonlat.lat, 'map_lon': map_lon, 'map_lat': map_lat,
        'usage':true, "location_changed": false, "content_changed": false, "text_changed": false,
        "icon_changed": false, "state_changed": false, "image_changed": false, "video_changed": false,
        "in_db": false, "con_index": 0, "loc_index": 0, "tmp_index": this.descs_count_inc
    });

    this.poi_order_user.push(this.descs_count);
    this.update_poi_descs(this.descs_count);

    this.descs_count += 1;
    this.descs_count_inc += 1;

    this.view_newly_edited(poi_index);

    OpenLayers.HooksLocal.map_check_pois(this);

    return true;
};

this.view_newly_edited = function(index)
{
    OpenLayers.HooksPopups.on_map_feature_select(this, index);
};


this.main_openlayers_init = function(map_div_name, descs_name)
{
    this.coordinates_to_show = false;

    this.descs_elm = document.getElementById ? document.getElementById(descs_name) : null;
    this.descs_elm_name = descs_name;

    this.map_edit_prepare_markers();

    this.map_pois_load();

    this.set_save_state(false);
    this.map_spec_changed = false;

    if ("0" == "" + this.map_id)
    {
        this.set_save_state(true);
        this.map_spec_changed = true;
        this.main_page_upload = true;

        this.map_update_name_state();
    }

    this.pzb_ctrl = new OpenLayers.Control.PanZoomBarMod();

    this.map = new OpenLayers.Map(map_div_name, {
        controls: [
            new OpenLayers.Control.Navigation(),
            this.pzb_ctrl,
            new OpenLayers.Control.ScaleLine()
        ],
        numZoomLevels: 20
    });

    this.map.geo_obj = this;

    var map_provs = [];
    var map_gsm = null;
    var map_osm = null;
    var map_mqm = null;

    this.map_view_layer_names_all = {};

    var google_label = this.map_view_layer_google;
    var osm_label = this.map_view_layer_osm;
    var mqm_label = this.map_view_layer_mapquest;

    if (this.map_view_layer_providers[google_label])
    {
        // google map v3
        map_gsm = new OpenLayers.Layer.GoogleMod(
            //"Google Map",
            this.display_strings.google_map,
            {}
        );

        this.map_view_layer_names_all[google_label] = map_gsm.name;
        if (google_label == this.map_view_layer_default)
        {
            map_provs.push(map_gsm);
        }
    }

    if (this.map_view_layer_providers[mqm_label])
    {
        // openstreetmap by mapquest
        map_mqm = new OpenLayers.Layer.MapQuest(
            //"MapQuest Map"
            this.display_strings.mapquest_map
        );
        map_mqm.wrapDateLine = true;
        map_mqm.displayOutsideMaxExtent = true;
        map_mqm.transitionEffect = 'resize';

        this.map_view_layer_names_all[mqm_label] = map_mqm.name;
        if (mqm_label == this.map_view_layer_default)
        {
            map_provs.push(map_mqm);
        }
    }

    if (this.map_view_layer_providers[osm_label])
    {
        // openstreetmap
        map_osm = new OpenLayers.Layer.OSMMod(
            //"OpenStreet Map"
            this.display_strings.openstreet_map
        );
        map_osm.wrapDateLine = true;
        map_osm.displayOutsideMaxExtent = true;
        map_osm.transitionEffect = 'resize';

        this.map_view_layer_names_all[osm_label] = map_osm.name;
        if (osm_label == this.map_view_layer_default)
        {
            map_provs.push(map_osm);
        }
    }

    if (map_gsm && (google_label != this.map_view_layer_default))
    {
        map_provs.push(map_gsm);
    }
    if (map_mqm && (mqm_label != this.map_view_layer_default))
    {
        map_provs.push(map_mqm);
    }
    if (map_osm && (osm_label != this.map_view_layer_default))
    {
        map_provs.push(map_osm);
    }

    this.map.addLayers(map_provs);
    this.map.addControl(new OpenLayers.Control.Attribution());

    // for switching between maps
    var lswitch = new OpenLayers.Control.LayerSwitcher();
    lswitch.roundedCornerColor = "#464646";

    this.map.addControl(lswitch);
    lswitch.maximizeControl();

    // an initial center point, set via parameters
    var cen_ini_longitude = this.map_view_layer_center_ini["longitude"];
    var cen_ini_latitude = this.map_view_layer_center_ini["latitude"];
    var lonLat_cen = new OpenLayers.LonLat(cen_ini_longitude, cen_ini_latitude)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            this.map.getProjectionObject() // to Spherical Mercator Projection
          );
    var zoom = this.map_view_layer_zoom;

    var style_map = new OpenLayers.StyleMap({
                cursor: "pointer",
                graphicZIndex: 10
    });

    var lookup = {};
    var labels_len = this.marker_src_labels.length;
    for (var lind = 0; lind < labels_len; lind++)
    {
        var cur_label = this.marker_src_labels[lind];
        var cur_icon = this.marker_src_icons[cur_label];
        lookup[2*lind] = {
            fillOpacity: 1.0,
            externalGraphic: cur_icon["path"],
            graphicWidth: cur_icon["width"],
            graphicHeight: cur_icon["height"],
            graphicXOffset: cur_icon["width_off"],
            graphicYOffset: cur_icon["height_off"]
        };
        lookup[(2*lind)+1] = {
            fillOpacity: 0.4,
            externalGraphic: cur_icon["path"],
            graphicWidth: cur_icon["width"],
            graphicHeight: cur_icon["height"],
            graphicXOffset: cur_icon["width_off"],
            graphicYOffset: cur_icon["height_off"]
        };
    };

    // create a lookup table for the provided icon types
    style_map.addUniqueValueRules("default", "type", lookup);

    // layer for features
    this.layer = new OpenLayers.Layer.Vector(
        //"POI markers",
        this.display_strings.point_markers,
        {
            styleMap: style_map,
            isBaseLayer: false,
            rendererOptions: {yOrdering: true}
        }
    );
    this.map.addLayer(this.layer);

    // setting map center
    this.map.setCenter (lonLat_cen, zoom);

    this.map_view_layer_name = this.map.layers[0].name;
    this.map_view_layer_name_saved = this.map.layers[0].name;
    this.map_view_layer_center = this.map.getCenter();
    this.map_view_layer_center_saved = this.map.getCenter();
    this.map_view_layer_zoom = this.map.getZoom();
    this.map_view_layer_zoom_saved = this.map_view_layer_zoom;

    // registering for click events
    var click = new OpenLayers.Control.Click();
    click.setTriggerAction(function(evt, map) {
        OpenLayers.HooksLocal.new_poi_on_map_click(evt, map);
    });
    this.map.addControl(click);
    click.activate();

    var hover = new OpenLayers.Control.Hover();
    hover.setTriggerAction(function(evt, map) {
        var poi_hover = map.geo_obj.layer.getFeatureFromEvent(evt);
        if (poi_hover) {
            if (null !== poi_hover.attributes.m_rank) {
                map.geo_obj.poi_rank_out = poi_hover.attributes.m_rank;
                map.geo_obj.update_poi_descs(map.geo_obj.poi_rank_out);
            }
        }
    });
    this.map.addControl(hover);
    hover.activate();

    var geo_obj = this; 

    var cur_date = new Date();
    OpenLayers.HooksLocal.redraw_times.map_dragging_last = cur_date.getTime();

    var drag_feature = new OpenLayers.Control.DragFeature(this.layer);
    drag_feature.onStart = function(feature, pixel) {geo_obj.poi_dragg_start(feature, pixel);};
    drag_feature.onComplete = function(feature, pixel) {geo_obj.poi_dragged(feature, pixel);};
    this.map.addControl(drag_feature);
    drag_feature.activate();

    var drag_map = new OpenLayers.Control.DragPanMod([map_gsm, map_mqm, map_osm]);
    this.map.addControl(drag_map);
    drag_map.activate();

    this.select_control = new OpenLayers.Control.SelectFeature(this.layer);
    this.map.addControl(this.select_control);
    this.select_control.activate();

    geo_obj.ignore_select_event = true;
    geo_obj.show_edit_on_select = true;
    this.layer.events.on({
        'featureselected': function(evt) {OpenLayers.HooksPopups.on_feature_select_edit(evt, geo_obj); OpenLayers.HooksPopups.on_feature_select(evt, geo_obj);},
        'featureunselected': function(evt) {OpenLayers.HooksPopups.on_feature_unselect(evt, geo_obj);}
    });

    var view_top_pos = new OpenLayers.Pixel(100, 50);
    var view_top = OpenLayers.Util.createDiv("view_top", view_top_pos, null, null, "absolute", "1px solid " + this.map_border_background);
    view_top.style.fontSize = "1px";
    view_top.style.width = "600px";
    view_top.style.height = "1px";
    view_top.style.background = this.map_border_background;
    view_top.style.backgroundColor = this.map_border_background;
    view_top.style.zIndex = this.map_border_zindex_on;
    view_top.style.opacity = "0.50";
    view_top.style.filter = "alpha(opacity=50)"; // IE
    this.map.viewPortDiv.appendChild(view_top);

    var view_bot_pos = new OpenLayers.Pixel(100, 450);
    var view_bot = OpenLayers.Util.createDiv("view_bot", view_bot_pos, null, null, "absolute", "1px solid " + this.map_border_background);
    view_bot.style.fontSize = "1px";
    view_bot.style.width = "600px";
    view_bot.style.height = "1px";
    view_bot.style.background = this.map_border_background;
    view_bot.style.backgroundColor = this.map_border_background;
    view_bot.style.zIndex = this.map_border_zindex_on;
    view_bot.style.opacity = "0.50";
    view_bot.style.filter = "alpha(opacity=50)"; // IE
    this.map.viewPortDiv.appendChild(view_bot);

    var view_left_pos = new OpenLayers.Pixel(100, 50);
    var view_left = OpenLayers.Util.createDiv("view_left", view_left_pos, null, null, "absolute", "1px solid " + this.map_border_background);
    view_left.style.fontSize = "1px";
    view_left.style.width = "1px";
    view_left.style.height = "400px";
    view_left.style.background = this.map_border_background;
    view_left.style.backgroundColor = this.map_border_background;
    view_left.style.zIndex = this.map_border_zindex_on;
    view_left.style.opacity = "0.50";
    view_left.style.filter = "alpha(opacity=50)"; // IE
    this.map.viewPortDiv.appendChild(view_left);

    var view_right_pos = new OpenLayers.Pixel(700, 50);
    var view_right = OpenLayers.Util.createDiv("view_right", view_right_pos, null, null, "absolute", "1px solid " + this.map_border_background);
    view_right.style.fontSize = "1px";
    view_right.style.width = "1px";
    view_right.style.height = "400px";
    view_right.style.background = this.map_border_background;
    view_right.style.backgroundColor = this.map_border_background;
    view_right.style.zIndex = this.map_border_zindex_on;
    view_right.style.opacity = "0.50";
    view_right.style.filter = "alpha(opacity=50)"; // IE
    this.map.viewPortDiv.appendChild(view_right);

    this.border_left = view_left;
    this.border_right = view_right;
    this.border_top = view_top;
    this.border_bottom = view_bot;

    this.set_map_width(this.map_art_view_width_default);
    this.set_map_height(this.map_art_view_height_default);

    this.map.events.register("moveend", null, function() {
        geo_obj.map_position_changed();
    });
    this.map.events.register("zoomend", null, function() {
        geo_obj.map_zoom_changed();
    });
    this.map.events.register("changelayer", null, function(evt) {
        if ("visibility" == evt.property) {
            OpenLayers.HooksLocal.on_layer_switch(geo_obj.map);
            geo_obj.set_map_provider();
        }
    });

};

// for closing the edit window
this.close_edit_window = function ()
{
    $("#map_mapedit").addClass('map_hidden');
};

// for displaying the edit window
this.open_edit_window = function ()
{
    $("#map_mapedit").removeClass('map_hidden');
};

// showing the last saved reader view
this.map_showview = function()
{
    var map_names = this.map.getLayersByName(this.map_view_layer_name_saved);
    if (0 < map_names.length)
    {
        this.map.setBaseLayer(map_names[0]);
    }
    this.map.setCenter(this.map_view_layer_center_saved, this.map_view_layer_zoom_saved);
};

// setting the current view as the reader initial view
this.map_setview = function()
{
    this.set_save_state(true);
    this.map_spec_changed = true;

    this.map_view_layer_name = this.map.baseLayer.name;
    this.map_view_layer_name_saved = this.map.baseLayer.name;
    this.map_view_layer_center = this.map.getCenter();
    this.map_view_layer_center_saved = this.map.getCenter();
    this.map_view_layer_zoom = this.map.getZoom();
    this.map_view_layer_zoom_saved = this.map_view_layer_zoom;
};

// map provider is set automatically on map layer change
this.set_map_provider = function ()
{
    if (("" != this.map_view_layer_name) && (this.map_view_layer_name != this.map.baseLayer.name))
    {
        this.map_view_layer_name = this.map.baseLayer.name;

        this.set_save_state(true);
        this.map_spec_changed = true;
    }
};

// map position is set automatically on map layer change
this.map_position_changed = function ()
{
    if (this.map_view_layer_center)
    {
        var current_map_center = this.map.getCenter();
        if ((current_map_center.lon != this.map_view_layer_center.lon) || (current_map_center.lat != this.map_view_layer_center.lat))
        {
            var old_pos = new OpenLayers.LonLat(this.map_view_layer_center.lon, this.map_view_layer_center.lat).transform(
                this.map.getProjectionObject(),
                new OpenLayers.Projection("EPSG:4326")
            );
            var new_pos = new OpenLayers.LonLat(current_map_center.lon, current_map_center.lat).transform(
                this.map.getProjectionObject(),
                new OpenLayers.Projection("EPSG:4326")
            );

            var min_dif = 0.0000001;
            var lon_dif = Math.abs(new_pos.lon - old_pos.lon);
            var lat_dif = Math.abs(new_pos.lat - old_pos.lat);

            if ((min_dif < lon_dif) || (min_dif < lat_dif)) {
                this.map_view_layer_center = current_map_center;
                this.set_save_state(true);
                this.map_spec_changed = true;
            }
        }
    }

    OpenLayers.HooksLocal.map_check_pois(this);

    this.restart_select_control();

};

// map zoom is set automatically on map layer change
this.map_zoom_changed = function ()
{
    {
        var current_map_zoom = this.map.getZoom();
        if (current_map_zoom != this.map_view_layer_zoom)
        {
            this.map_view_layer_zoom = current_map_zoom;
            this.set_save_state(true);
            this.map_spec_changed = true;
        }
    }

    OpenLayers.HooksLocal.map_check_pois(this);

    this.restart_select_control();

};

// ol loose the select control for many times
this.restart_select_control = function()
{
    var geo_obj = this;
    var map = geo_obj.map;
    if (geo_obj.select_control) {
        geo_obj.select_control.destroy();
    }
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    map.addControl(map.geo_obj.select_control);
    geo_obj.select_control.activate();
};

// changing the size for the map div for the reader view
this.map_width_change = function(size, unsaved)
{
    if ((0 > size) && (10 >= this.map_art_view_width)) {return;}
    if ((0 < size) && (this.map_limit_width_view <= this.map_art_view_width)) {return;}

    if (undefined === unsaved) {unsaved = true;}
    if (unsaved)
    {
        this.set_save_state(true);
        this.map_spec_changed = true;
    }

    var map_left_border = this.border_left;
    var map_right_border = this.border_right;
    var map_top_border = this.border_top;
    var map_bottom_border = this.border_bottom;

    var map_view_size = document.getElementById ? document.getElementById("map_view_size") : null;

    this.map_art_view_width += size;
    this.map_art_view_right -= size / 2;

    map_view_size.innerHTML = this.map_art_view_width + " x " + this.map_art_view_height;

    var border_zindex = this.map_border_zindex_on;
    if (this.map_limit_width_display < this.map_art_view_width) {border_zindex = this.map_border_zindex_off;}
    map_left_border.style.zIndex = border_zindex;
    map_right_border.style.zIndex = border_zindex;

    if ((0 > size) && (this.map_limit_width_display == this.map_art_view_width)) {return;}
    if (this.map_limit_width_display < this.map_art_view_width) {return;}

    this.map_art_view_width_display += size;
    this.map_art_view_right_display -= size / 2;

    map_left_border.style.left = (this.map_art_view_right_display - 6) + "px";
    map_right_border.style.left = (this.map_art_view_right_display + this.map_art_view_width_display - 5) + "px";
    map_top_border.style.width = (this.map_art_view_width_display + 2) + "px";
    map_top_border.style.left = (this.map_art_view_right_display - 6) + "px";
    map_bottom_border.style.width = (this.map_art_view_width_display + 2) + "px";
    map_bottom_border.style.left = (this.map_art_view_right_display - 6) + "px";

};

// changing the size for the map div for the reader view
this.map_height_change = function(size, unsaved)
{
    if ((0 > size) && (10 >= this.map_art_view_height)) {return;}
    if ((0 < size) && (this.map_limit_height_view <= this.map_art_view_height)) {return;}

    if (undefined === unsaved) {unsaved = true;}
    if (unsaved)
    {
        this.set_save_state(true);
        this.map_spec_changed = true;
    }

    var map_left_border = this.border_left;
    var map_right_border = this.border_right;
    var map_top_border = this.border_top;
    var map_bottom_border = this.border_bottom;

    var map_view_size = document.getElementById ? document.getElementById("map_view_size") : null;

    this.map_art_view_height += size;
    this.map_art_view_top -= size / 2;

    map_view_size.innerHTML = this.map_art_view_width + " x " + this.map_art_view_height;

    var border_zindex = this.map_border_zindex_on;
    if (this.map_limit_height_display < this.map_art_view_height) {border_zindex = this.map_border_zindex_off;}
    map_top_border.style.zIndex = border_zindex;
    map_bottom_border.style.zIndex = border_zindex;

    if ((0 > size) && (this.map_limit_height_display == this.map_art_view_height)) {return;}
    if (this.map_limit_height_display < this.map_art_view_height) {return;}

    this.map_art_view_height_display += size;
    this.map_art_view_top_display -= size / 2;

    map_bottom_border.style.top = (this.map_art_view_top_display + this.map_art_view_height_display - 22) + "px";
    map_top_border.style.top = (this.map_art_view_top_display - 21) + "px";
    map_right_border.style.height = (this.map_art_view_height_display - 1) + "px";
    map_right_border.style.top = (this.map_art_view_top_display - 21) + "px";
    map_left_border.style.height = (this.map_art_view_height_display - 1) + "px";
    map_left_border.style.top = (this.map_art_view_top_display - 21) + "px";

};

// the data that should be loaded on POI edit start
this.load_point_data = function()
{
    this.load_point_label();
    this.load_point_icon();

    this.load_point_properties();

    this.load_point_direct();
};

// storing POI's visible name
this.store_point_label = function(value, index)
{
    var label_value = "";
    var label_obj = document.getElementById ? document.getElementById("point_label") : null;
    if (label_obj) {
        label_value = label_obj.value;
    }
    if (undefined !== value) {
        label_value = value;
    }

    var use_index = this.display_index(this.edited_point);
    if (undefined !== index) {
        use_index = index;
    }

    var cur_marker = this.layer.features[use_index];
    var cur_poi_info = this.poi_markers[use_index];

    var update_preview = false;
    if (cur_poi_info.in_db)
    {
        if (label_value != cur_marker.attributes.m_title)
        {
            this.set_save_state(true); // if a new poi, the state is already set to true

            cur_poi_info.content_changed = true;
            cur_poi_info.text_changed = true;
            update_preview = true;
            this.main_page_upload = true;
        }
    }
    cur_marker.attributes.m_title = label_value;
    if (update_preview) {this.update_edit_preview();}

    this.update_poi_descs(use_index);
};

// loading POI's visible name
this.load_point_label = function()
{
    var label_obj = document.getElementById ? document.getElementById("point_label") : null;

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];
    label_obj.value = cur_marker.attributes.m_title;

};

// storing POI's specified property
this.store_point_property = function(property, value, index)
{
    if ("text" == property) {
        if (value == this.display_strings.fill_in_the_point_description) {
            value = "";
        }
    }

    var video_source = null;

    if ("video_id" == property) {
        var vid_patterns = [/(.*)youtu\.be\/([^\/\?\&\s]+)/i, /(.*)youtube\.com\/watch\?v\=([^\/\?\&\s]+)/i, /(.*)vimeo\.com\/([^\/\?\&\s]+)/i];
        var vid_type_sources = ["youtube", "youtube", "vimeo"];
        var vid_patterns_count = vid_patterns.length;
        var vid_match = "";
        for (var vid = 0; vid < vid_patterns_count; vid++) {
            var vid_pat = vid_patterns[vid];
            vid_match = "";
            if (vid_match = vid_pat.exec(value)) {
                try {
                    var value_test = vid_match[2];
                    if (value_test) {
                        value = value_test;
                        video_source = vid_type_sources[vid];
                    }
                }
                catch(exc) {continue;};
                break;
            }
        }

        if (!video_source) {
            var value_test = value;
            value_test = value_test.toLowerCase();
            if (-1 < value_test.indexOf(".flv")) {
                video_source = "flv";
            }
            if (-1 < value_test.indexOf(".swf")) {
                video_source = "flash";
            }
        }
    }

    var use_index = this.display_index(this.edited_point);
    if (undefined !== index) {
        use_index = index;
    }

    var cur_marker = this.layer.features[use_index];

    var poi_property = "m_" + property;

    var attrs = cur_marker.attributes;

    var cur_poi_info = this.poi_markers[use_index];
    var update_preview = false;
    if (cur_poi_info.in_db)
    {
        if (value != attrs[poi_property])
        {
            this.set_save_state(true); // if a new poi, the state is already set to true

            cur_poi_info.content_changed = true;
            update_preview = true;
            if ("image" == property.substr(0, 5))
            {
                cur_poi_info.image_changed = true;
            }
            else if ("video" == property.substr(0, 5))
            {
                cur_poi_info.video_changed = true;
            }
            else
            {
                cur_poi_info.text_changed = true;
            }
        }
    }
    attrs[poi_property] = value;

    if ("image" == property.substr(0, 5))
    {
        GeoPopups.set_image_tag(attrs, this);
    }

    if ("video" == property.substr(0, 5))
    {
        GeoPopups.set_embed_tag(attrs, this);
        this.update_video_label();
    }
    if (update_preview) {this.update_edit_preview();}

    if (null !== video_source) {
        var video_source_types = {"youtube":1, "vimeo":2, "flash":3, "flv":4};
        if (video_source in video_source_types) {
            var video_source_rank = video_source_types[video_source];
            var video_type_obj = document.getElementById ? document.getElementById("point_video_type") : null;
            if (video_type_obj) {
                video_type_obj.selectedIndex = video_source_rank;
                this.store_point_property("video_type", video_source);
            }
        }
    }

};

// loading POI's properties
this.load_point_properties = function()
{
    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];

    var poi_prop_names = {};
    poi_prop_names['text'] = "point_descr";
    poi_prop_names['link'] = "point_link";
    poi_prop_names['content'] = "point_content";
    poi_prop_names['image_source'] = "point_image";
    poi_prop_names['image_width'] = "point_image_width";
    poi_prop_names['image_height'] = "point_image_height";
    poi_prop_names['video_id'] = "point_video";
    poi_prop_names['video_width'] = "point_video_width";
    poi_prop_names['video_height'] = "point_video_height";

    var video_type = cur_marker.attributes['m_video_type'];

    for (var one_name in poi_prop_names)
    {
        var div_name = poi_prop_names[one_name];
        var div_obj = document.getElementById ? document.getElementById(div_name) : null;

        var poi_property = "m_" + one_name;
        var one_value = cur_marker.attributes[poi_property];
        if (!one_value) {one_value = "";}

        if (("point_descr" == div_name) && ("" == one_value)) {
            one_value = this.display_strings.fill_in_the_point_description;
        }

        if (("video_id" == one_name) && one_value && (0 < one_value.length)) {
            if ("youtube" == video_type) {
                one_value = "http://youtu.be/" + one_value;
            }
            if ("vimeo" == video_type) {
                one_value = "http://vimeo.com/" + one_value;
            }
        }

        div_obj.value = one_value;
    }

    var video_type_names = {'none': 0, 'youtube': 1, 'vimeo': 2, 'flash': 3, 'flv': 4};

    if (!video_type) {video_type = "none";}
    var video_index = video_type_names[video_type];
    if (!video_index) {video_index = 0;}

    var video_type_obj = document.getElementById ? document.getElementById("point_video_type") : null;
    video_type_obj.selectedIndex = video_index;

    this.update_video_label();
};

// setting the proper video label at point editing
this.update_video_label = function()
{
    var video_type_obj = document.getElementById ? document.getElementById("point_video_type") : null;
    var video_index = video_type_obj.selectedIndex;
    if (3 <= video_index)
    {
        $("#video_file_label_id").addClass("map_hidden");
        $("#video_file_label_file").removeClass("map_hidden");
    }
    else
    {
        $("#video_file_label_id").removeClass("map_hidden");
        $("#video_file_label_file").addClass("map_hidden");
    }

    var desc_ids = ["geo_video_desc_other", "geo_video_desc_youtube", "geo_video_desc_vimeo", "geo_video_desc_local_swf", "geo_video_desc_local_flv"];
    var desc_ids_count = desc_ids.length;
    if ((0 > video_index) || (desc_ids_count <= video_index)) {
        video_index = 0;
    }

    for (var vind = 0; vind < desc_ids_count; vind++) {
        var one_desc_id = desc_ids[vind];
        if (vind == video_index) {
            $("#" + one_desc_id + "").removeClass("map_hidden");
        } else {
            $("#" + one_desc_id + "").addClass("map_hidden");
        }
    }

};

// loading POI's marker icon
this.load_point_icon = function()
{
    var img_selected = document.getElementById ? document.getElementById("edit_marker_selected_src") : null;

    var img_index = 0;
    if (this.layer && this.layer.features && this.layer.features[this.edited_point])
    {
        img_index = this.layer.features[this.edited_point].attributes.m_type;
    }

    var img_label = this.marker_src_labels[img_index];
    var img_icon = this.marker_src_icons[img_label];
    var img_path = img_icon["path"];

    img_selected.src = img_path;
};

// storing POI's info on prepared view vs. any html pop-up view
this.store_point_direct = function(direct_usage)
{
    var direct_num = 0;
    var direct_bool = false;
    this.edit_text_mode = 'plain';
    if (direct_usage && (0 != direct_usage))
    {
        direct_num = 1;
        direct_bool = true;
        this.edit_text_mode = 'html';
    }

    this.set_save_state(true);

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];

    var cur_poi_info = this.poi_markers[use_index];
    var update_preview = false;
    if (cur_poi_info.in_db)
    {
        if (direct_bool != cur_marker.attributes.m_direct)
        {
            cur_poi_info.content_changed = true;
            cur_poi_info.text_changed = true;
            update_preview = true;
        }
    }

    cur_marker.attributes.m_direct = direct_bool;
    if (update_preview) {this.update_edit_preview();}

    this.set_edit_direct();
};

// getting info for point editing
this.load_point_direct = function()
{
    var predef_obj = document.getElementById ? document.getElementById("point_predefined") : null;

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];
    var cur_direct = cur_marker.attributes.m_direct;

    if (undefined === cur_direct) {cur_direct = false;}

    var selIndex = 0;
    if (cur_direct) {selIndex = 1;}
    predef_obj.selectedIndex = selIndex;

    this.edit_text_mode = 'plain';
    if (cur_direct)
    {
        this.edit_text_mode = 'html';
    }
    this.edit_view_mode = 'edit';

    this.update_edit_preview();
    this.set_edit_direct();
};

// updating the preview view at point editing
this.update_edit_preview = function()
{
    return;

    var use_index = this.display_index(this.edited_point);
    if (this.layer && this.layer.features && this.layer.features[use_index])
    {
        var cur_marker = this.layer.features[use_index];
        var popup_info = GeoPopups.create_popup_content(cur_marker, this);
        var popup_content = popup_info['inner_html'];

        var min_width = popup_info['min_width'];
        var min_height = popup_info['min_height'];

        var content_obj = document.getElementById ? document.getElementById("edit_part_preview") : null;

        content_obj.innerHTML = "<div id=\"edit_popup_preview\" class=\"edit_popup_preview\">" + popup_content + "</div>";

        var preview_obj = document.getElementById ? document.getElementById("edit_popup_preview") : null;

        var height_taken = preview_obj.offsetHeight;
        var width_taken = preview_obj.offsetWidth;
        if (height_taken < min_height) {preview_obj.style.height = min_height + "px";}
        if (width_taken < min_width) {preview_obj.style.width = min_width + "px";}

    }
};

// displaying appropriate part for text input for the POI content
this.set_edit_direct = function()
{
    direct_usage = false;
    if ('html' == this.edit_text_mode) {direct_usage = true;}

    var direct_num = 0;
    var direct_bool = false;
    if (direct_usage && (0 != direct_usage))
    {
        direct_num = 1;
        direct_bool = true;
    }

    if (direct_usage)
    {
        $("#edit_part_text").addClass("map_hidden");
        {
            $("#edit_part_content").removeClass("map_hidden");
        }
    }
    else
    {
        $("#edit_part_content").addClass("map_hidden");
        {
            $("#edit_part_text").removeClass("map_hidden");
        }
    }

};

// setting POI's icon on edit action
this.map_edit_set_marker = function(index)
{
    this.set_save_state(true);

    var img_label = this.marker_src_labels[index];
    var img_icon = this.marker_src_icons[img_label];
    var img_path = img_icon["path"];

    var img_selected = document.getElementById ? document.getElementById("edit_marker_selected_src") : null;
    img_selected.src = img_path;

    var use_index = this.edited_point;
    var feature = this.layer.features[use_index];
    var attrs = feature.attributes;

    var marker_type = 2 * index;
    if (attrs.m_disabled) {marker_type += 1;}

    var cur_poi_info = this.poi_markers[use_index];
    if (cur_poi_info.in_db)
    {
        if (index != attrs.m_type)
        {
            cur_poi_info.content_changed = true;
            cur_poi_info.icon_changed = true;
        }
    }
    attrs.m_type = index;

    attrs.type = marker_type;
    this.layer.redraw();

};

// preparing icon part of POI editing, initial phase
this.map_edit_prepare_markers = function()
{
    var img_selected = document.getElementById ? document.getElementById("edit_marker_selected_src") : null;
    var img_index = 0; //.m_type

    var img_label = this.marker_src_labels[img_index];
    var img_icon = this.marker_src_icons[img_label];
    var img_path = img_icon["path"];

    img_selected.src = img_path;

    var img_choices = document.getElementById ? document.getElementById("edit_marker_choices") : null;

    var choices_html = "";

    var choice_one = "";
    var choices_count = this.marker_src_labels.length;

    var obj_name = this.get_obj_name();
    for (var cind = 1; cind < choices_count; cind++)
    {
        var cur_img_label = this.marker_src_labels[cind];
        var cur_img_icon = this.marker_src_icons[cur_img_label];
        var cur_img_path = cur_img_icon["path"];

        choice_one = "<a class=\"edit_marker_one_choice_link\" href='#' onClick=\"" + obj_name + ".map_edit_set_marker(" + cind + "); return false;\"><img class='edit_marker_one_choice' src='" + cur_img_path + "'></a>";
        choices_html += choice_one;
    }
    img_choices.innerHTML = choices_html;

};

// setting the saved state flag
this.set_save_state = function(state)
{
    var save_obj = document.getElementById ? document.getElementById("map_button_save") : null;

    if (state)
    {
        this.something_to_save = true;

        save_obj.disabled = false;
    }
    else
    {
        this.something_to_save = false;

        save_obj.disabled = true;
    }

};

// loading the POI data, for the initial phase
this.map_pois_load = function(script_dir)
{
    this.set_save_state(false);

    var geo_obj = this;
    callServer(['Geo_Map', 'LoadMapData'], [
        this.map_id,
        this.language_id,
        this.article_number
        ], function(json) {
            geo_obj.got_load_data(json);
        });
};

this.map_update_name_state = function(value, input_name)
{
    var map_name_value = this.map_label_name;

    if ((undefined !== value) && (null !== value)) {
        map_name_value = value;
    }
    if ((undefined !== input_name) && (null !== input_name)) {
        var input_obj = document.getElementById ? document.getElementById(input_name) : null;
        if (input_obj) {
            map_name_value = input_obj.value;
        }
    }

    if ("" == map_name_value) {
        $("#map_name_display").addClass("map_text_lack");
        $("#map_name_input").addClass("map_text_lack");
    } else {
        $("#map_name_display").removeClass("map_text_lack");
        $("#map_name_input").removeClass("map_text_lack");
    }

};

// setting the map for editing its name
this.map_edit_name = function()
{
    $("#map_name_display").addClass("map_hidden");
    $("#map_name_input").removeClass("map_hidden");

    var input_obj = document.getElementById ? document.getElementById("map_name_input") : null;
    input_obj.focus();

    this.map_update_name_state();
};

// setting the map for displaying its name
this.map_display_name = function()
{
    this.map_save_name();

    $("#map_name_display").removeClass("map_hidden");
    $("#map_name_input").addClass("map_hidden");

    this.map_update_name_state();
};

// saving the name of the map
this.map_save_name = function()
{
    var input_obj = document.getElementById ? document.getElementById("map_name_input") : null;
    var display_obj = document.getElementById ? document.getElementById("map_name_display") : null;

    var name_value = input_obj.value;
    {
        var map_name_disp_str = name_value;
        map_name_disp_str = map_name_disp_str.replace(/&/gi, "&amp;");
        map_name_disp_str = map_name_disp_str.replace(/</gi, "&lt;");
        map_name_disp_str = map_name_disp_str.replace(/>/gi, "&gt;");

        var max_len = this.map_name_max_length;
        if (max_len < map_name_disp_str.length)
        {
            map_name_disp_str = map_name_disp_str.substr(0, max_len) + "...";
        }

        if ("" == map_name_disp_str) {
            map_name_disp_str = this.display_strings.fill_in_map_name;
        }

        display_obj.innerHTML = map_name_disp_str;

        if (name_value != this.map_label_name)
        {
            this.map_label_name = name_value;
            this.map_spec_changed = true;
            this.set_save_state(true);
        }
    }

};

// putting map name into the editing page
this.map_load_name = function()
{
    var input_obj = document.getElementById ? document.getElementById("map_name_input") : null;
    var display_obj = document.getElementById ? document.getElementById("map_name_display") : null;

    var name_value = this.map_label_name;

    input_obj.value = name_value;

    if ("" != name_value)
    {
        var map_name_disp_str = name_value;
        map_name_disp_str = map_name_disp_str.replace(/&/gi, "&amp;");
        map_name_disp_str = map_name_disp_str.replace(/</gi, "&lt;");
        map_name_disp_str = map_name_disp_str.replace(/>/gi, "&gt;");

        var max_len = this.map_name_max_length;
        if (max_len < map_name_disp_str.length)
        {
            map_name_disp_str = map_name_disp_str.substr(0, max_len) + "...";
        }
        display_obj.innerHTML = map_name_disp_str;

        $(display_obj).removeClass("map_text_lack");
    }
    else
    {
        display_obj.innerHTML = this.display_strings.fill_in_map_name;
        $(display_obj).addClass("map_text_lack");
    }

};

// the main action on ajax data retrieval
// it throws away all the current POI info
this.got_load_data = function (received_obj)
{
    this.select_control.destroy();

    this.edited_point = 0;
    this.poi_rank_out = 0;

    this.poi_order_user = [];
    this.poi_markers = [];
    this.descs_count = 0;
    this.descs_count_inc = 0;
    this.poi_order_changed = false;
    this.map_spec_changed = false;

    this.layer.removeFeatures(this.layer.features);
    if (this.popup)
    {
        // this pop-up removal seems to be sometimes strange
        try {
            this.map.removePopup(this.popup);
            this.popup.destroy();
        }
        catch (e) {}
        this.popup = null;
    }

    this.close_edit_window();

    var features_to_add = [];

    var lonlat = null;

    this.set_map_usage(received_obj.map, true);

    var poi_count = received_obj.pois.length;
    for (var pind = 0; pind < poi_count; pind++)
    {
        var one_marker = {};
        var one_poi = received_obj.pois[pind];

        lonlat = new OpenLayers.LonLat(one_poi.longitude, one_poi.latitude).transform(
            new OpenLayers.Projection("EPSG:4326"),
            this.map.getProjectionObject()
        );

        one_marker['lon'] = parseFloat(one_poi.longitude);
        one_marker['lat'] = parseFloat(one_poi.latitude);
        one_marker['map_lon'] = lonlat.lon;
        one_marker['map_lat'] = lonlat.lat;

        one_marker['usage'] = true;
        one_marker['location_changed'] = false;
        one_marker['content_changed'] = false;

        one_marker['icon_changed'] = false;
        one_marker['state_changed'] = false;
        one_marker['image_changed'] = false;
        one_marker['video_changed'] = false;
        one_marker['text_changed'] = false;

        one_marker['in_db'] = true;
        one_marker['loc_index'] = one_poi.loc_id;
        one_marker['con_index'] = one_poi.con_id;
        one_marker['tmp_index'] = 0;

        this.poi_order_user.push(pind);
        this.poi_markers.push(one_marker);

        var disabled = false;
        if (0 == one_poi.display) {disabled = true;}

        var img_name = one_poi.style;
        var icon_type = 0;
        var image_count = this.marker_src_labels.length;
        for (var lind = 0; lind < image_count; lind++)
        {
            var cur_label = this.marker_src_labels[lind];
            var cur_image = this.marker_src_icons[cur_label]['name'];

            if (cur_image == img_name)
            {
                icon_type = lind;
                break;
            }
        }
        var icon_view = 2 * icon_type;
        if (disabled) {icon_view += 1;}

        var point = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
        var vector = new OpenLayers.Feature.Vector(point, {type: icon_view});

        vector.attributes.m_rank = pind;
        vector.attributes.m_disabled = disabled;
        vector.attributes.m_type = icon_type;

        vector.attributes.m_title = one_poi['title'];
        vector.attributes.m_perex = one_poi['perex'];

        var direct = false;
        if (0 == one_poi['content_type']) {direct = true;}
        vector.attributes.m_direct = direct;
        vector.attributes.m_content = one_poi['content'];

        vector.attributes.m_text = one_poi['text'];
        vector.attributes.m_link = one_poi['link'];
        vector.attributes.m_image_mm = one_poi['image_mm'];
        vector.attributes.m_image_source = one_poi['image_src'];
        var one_image_width = one_poi['image_width'];
        if ("0" == "" + one_image_width) {one_image_width = "";}
        var one_image_height = one_poi['image_height'];
        if ("0" == "" + one_image_height) {one_image_height = "";}
        vector.attributes.m_image_width = one_image_width;
        vector.attributes.m_image_height = one_image_height;

        vector.attributes.m_image_share = false;
        vector.attributes.m_video_mm = one_poi['video_mm'];

        vector.attributes.m_video_id = one_poi['video_id'];
        vector.attributes.m_video_type = one_poi['video_type'];
        var one_video_width = one_poi['video_width'];
        if ("0" == "" + one_video_width) {one_video_width = "";}
        var one_video_height = one_poi['video_height'];
        if ("0" == "" + one_video_height) {one_video_height = "";}
        vector.attributes.m_video_width = one_video_width;
        vector.attributes.m_video_height = one_video_height;

        vector.attributes.m_video_share = false;

        vector.attributes.m_image = "";
        vector.attributes.m_embed = "";
        GeoPopups.set_image_tag(vector.attributes, this);
        GeoPopups.set_embed_tag(vector.attributes, this);

        features_to_add.push(vector);

    }

    this.layer.addFeatures(features_to_add);

    this.descs_count = poi_count;
    this.descs_count_inc = poi_count;

    this.select_control = new OpenLayers.Control.SelectFeature(this.layer);
    this.map.addControl(this.select_control);
    this.select_control.activate();

    this.update_poi_descs();

    // setting to tha saved state at the button pressing now
    //this.set_save_state(false);

    if (!this.popup_prev_button)
    {
        this.popup_prev_button = document.getElementById ? document.getElementById("map_button_preview") : null;
    }

    if ("0" == "" + this.map_id)
    {
        this.map_spec_changed = true;
        this.set_save_state(true);
    }
    else
    {
        if (this.popup_prev_button) {this.popup_prev_button.disabled = false;}
    }

    OpenLayers.HooksLocal.map_check_pois(this);

    if (this.go_to_preview_page) {
        this.go_to_preview_page = false;
        window.map_show_preview(true);
    }

};

// setting point information for ajax based saving of it
this.set_save_content_on_poi = function(save_obj, poi_attrs, marker_info)
{
    var poi_prop_names = {};

    poi_prop_names['title'] = "name";
    poi_prop_names['perex'] = "perex";
    poi_prop_names['content'] = "content";
    poi_prop_names['text'] = "text";
    poi_prop_names['link'] = "link";

    poi_prop_names['image_mm'] = "image_mm";
    poi_prop_names['image_source'] = "image_src";
    poi_prop_names['image_width'] = "image_width";
    poi_prop_names['image_height'] = "image_height";

    poi_prop_names['video_mm'] = "video_mm";
    poi_prop_names['video_id'] = "video_id";
    poi_prop_names['video_type'] = "video_type";
    poi_prop_names['video_width'] = "video_width";
    poi_prop_names['video_height'] = "video_height";

    for (var one_name in poi_prop_names)
    {
        var poi_property = "m_" + one_name;
        var obj_property = poi_prop_names[one_name];

        var one_value = poi_attrs[poi_property];
        if (!one_value) {one_value = "";}
        save_obj[obj_property] = one_value;
    }

    var img_label = this.marker_src_labels[poi_attrs.m_type];
    var img_icon = this.marker_src_icons[img_label];
    var img_name = img_icon["name"];
    save_obj["style"] = img_name;

    var content_type = 1;
    if (poi_attrs.m_direct) {content_type = 0;}
    save_obj["content_type"] = content_type;

    var display_poi = 1;
    if (poi_attrs.m_disabled) {display_poi = 0;}
    save_obj["display"] = display_poi;

    save_obj['text_changed'] = marker_info["text_changed"];
    save_obj['icon_changed'] = marker_info["icon_changed"];
    save_obj['state_changed'] = marker_info["state_changed"];
    save_obj['image_changed'] = marker_info["image_changed"];
    save_obj['video_changed'] = marker_info["video_changed"];
};

// prepare info of new points for saving
this.put_poi_into_insertions = function(storage, index)
{
    var cur_marker = this.poi_markers[index];
    var cur_attrs = this.layer.features[index].attributes;

    var cur_obj = {
        'index': cur_marker['tmp_index'],
        'longitude': cur_marker['lon'],
        'latitude': cur_marker['lat']
    };

    this.set_save_content_on_poi(cur_obj, cur_attrs, cur_marker);
    storage.push(cur_obj);
};

// prepare info on points with changed positions for saving of it
this.put_into_poi_locations = function(storage, index)
{
    var cur_marker = this.poi_markers[index];

    var cur_obj = {
        'id': cur_marker['loc_index'],
        'longitude': cur_marker['lon'],
        'latitude': cur_marker['lat']
    };

    storage.push(cur_obj);
};

// prepare info on points with changed contents for saving of it
this.put_into_poi_contents = function(storage, index)
{
    var cur_marker = this.poi_markers[index];
    var cur_attrs = this.layer.features[index].attributes;

    var cur_obj = {
        'id': cur_marker['con_index'],
        'location_id': cur_marker['loc_index']
    };

    this.set_save_content_on_poi(cur_obj, cur_attrs, cur_marker);

    storage.push(cur_obj);

};

this.check_points_filled = function()
{
    var filled = true;

    var features = this.layer.features;
    var poi_count = features.length;

    for (var pind = 0; pind < poi_count; pind++) {
        var one_filled = false;

        var cur_attrs = features[pind].attributes;

        if (!cur_attrs.m_direct) {
            if ("" != cur_attrs.m_text) {one_filled = true;}
        } else {
            if ("" != cur_attrs.m_content) {one_filled = true;}
        }

        if (!one_filled) {
            if (cur_attrs.m_image_source) {one_filled = true;}
            if (cur_attrs.m_video_id && cur_attrs.m_video_type) {one_filled = true;}
        }

        if (!one_filled) {
            filled = false;
            break;
        }
    }

    return filled;
}

// saving data, on the main 'save' user action; do ajax here
this.map_save_all = function(script_dir, force_save)
{
    if (!this.something_to_save) {return;}

    var geo_obj = this;

    this.set_save_state(false);

    var cur_marker = null;

    // init args
    var args = {
        'f_map': '',
        'f_remove': '',
        'f_order': '',
        'f_insert_new': '',
        'f_update_loc': '',
        'f_update_con': ''
    };

    if ((0 == this.map_id) || (this.map_spec_changed))
    {
        var prov_label = "";
        for (var one_prov_name in this.map_view_layer_names_all)
        {
            if (this.map_view_layer_names_all[one_prov_name] == this.map_view_layer_name) {prov_label = one_prov_name;}
        }

        var center_lonlat = new OpenLayers.LonLat(this.map_view_layer_center.lon, this.map_view_layer_center.lat);
        center_lonlat.transform(
            this.map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326")
        )

        var store_map_obj = {
            'provider': prov_label,
            'cen_lon': center_lonlat.lon,
            'cen_lat': center_lonlat.lat,
            'zoom': this.map_view_layer_zoom,
            'width': this.map_art_view_width,
            'height': this.map_art_view_height,
            'name': this.map_label_name
        };
        var store_map_str = Base64.encode(JSON.stringify(store_map_obj));
        store_map_str = store_map_str.replace(/\+/gi, "%2B");
        store_map_str = store_map_str.replace(/\//gi, "%2F");
        args['f_map'] = store_map_str;
    }

    if (0 < this.poi_deletion.length)
    {
        var remove_poi_str = Base64.encode(JSON.stringify(this.poi_deletion));
        remove_poi_str = remove_poi_str.replace(/\+/gi, "%2B");
        remove_poi_str = remove_poi_str.replace(/\//gi, "%2F");
        args['f_remove'] = remove_poi_str;
    }

    var order_length = this.poi_order_user.length;
    if (this.poi_order_changed && (0 < order_length))
    {
        var order_ids = [];
        for (var oind = 0; oind < order_length; oind++)
        {
            var cur_poi_index = this.poi_order_user[oind];
            cur_marker = this.poi_markers[cur_poi_index];
            if (cur_marker.in_db) {order_ids.push({'state': 'old', 'content': cur_marker.con_index, 'location': cur_marker.loc_index});}
            else {order_ids.push({'state': 'new', 'index': cur_marker.tmp_index});}
        }
        var order_poi_str = Base64.encode(JSON.stringify(order_ids));
        order_poi_str = order_poi_str.replace(/\+/gi, "%2B");
        order_poi_str = order_poi_str.replace(/\//gi, "%2F");
        args['f_order'] = order_poi_str;
    }

    var insert_poi_new_array = [];
    var update_poi_loc_array = [];
    var update_poi_con_array = [];

    var marker_count = this.poi_markers.length;
    for (var mind = 0; mind < marker_count; mind++)
    {
        cur_marker = this.poi_markers[mind];
        if (!cur_marker.in_db)
        {
            this.put_poi_into_insertions(insert_poi_new_array, mind);
            continue;
        }
        if (cur_marker.location_changed)
        {
            this.put_into_poi_locations(update_poi_loc_array, mind);
        }
        if (cur_marker.content_changed)
        {
            this.put_into_poi_contents(update_poi_con_array, mind);
        }
    }
    if (0 < insert_poi_new_array.length)
    {
        var insert_poi_new_str = Base64.encode(JSON.stringify(insert_poi_new_array));
        insert_poi_new_str = insert_poi_new_str.replace(/\+/gi, "%2B");
        insert_poi_new_str = insert_poi_new_str.replace(/\//gi, "%2F");
        args['f_insert_new'] = insert_poi_new_str;
    }
    if (0 < update_poi_loc_array.length)
    {
        var update_poi_loc_str = Base64.encode(JSON.stringify(update_poi_loc_array));
        update_poi_loc_str = update_poi_loc_str.replace(/\+/gi, "%2B");
        update_poi_loc_str = update_poi_loc_str.replace(/\//gi, "%2F");
        args['f_update_loc']= update_poi_loc_str;
    }
    if (0 < update_poi_con_array.length)
    {
        var update_poi_con_str = Base64.encode(JSON.stringify(update_poi_con_array));
        update_poi_con_str = update_poi_con_str.replace(/\+/gi, "%2B");
        update_poi_con_str = update_poi_con_str.replace(/\//gi, "%2F");
        args['f_update_con'] = update_poi_con_str;
    }

    if (this.poi_order_changed)
    {
        this.main_page_upload = true;
    }
    if (0 < this.poi_deletion.length)
    {
        this.main_page_upload = true;
    }
    if (0 < insert_poi_new_array.length)
    {
        this.main_page_upload = true;
    }

    var geo_obj = this;
    callServer(['Geo_Map', 'StoreMapData'], [
        this.map_id,
        this.language_id,
        this.article_number,
        args['f_map'],
        args['f_remove'],
        args['f_insert_new'],
        args['f_update_loc'],
        args['f_update_con'],
        args['f_order']
        ], function(json) {
            geo_obj.got_load_data(json);
        });

    this.map_view_layer_name_saved = this.map_view_layer_name;
    this.map_view_layer_center_saved = this.map_view_layer_center;
    this.map_view_layer_zoom_saved = this.map_view_layer_zoom;

    if (this.main_page_upload)
    {
        this.main_page_upload = false;
    }
};

};

