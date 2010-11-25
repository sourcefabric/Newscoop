// the main object to hold geo-things
var geo_locations = {};

// specifying the article that the map is for
geo_locations.article_number = 0;
geo_locations.language_id = 0;

// flags for what has been changed
geo_locations.map_spec_changed = false;
geo_locations.poi_order_changed = false;

// marker icons paths and names, set during initialization
geo_locations.marker_src_base = "";
geo_locations.marker_src_default = "";
geo_locations.marker_src_default_ind = 0;
//geo_locations.marker_src_names = [];
geo_locations.marker_src_labels = [];
geo_locations.marker_src_icons = {};

// what map provider should be used, and map position
geo_locations.map_view_layer_google = "googlev3";
geo_locations.map_view_layer_osm = "osm";
geo_locations.map_view_layer_providers = {};
geo_locations.map_view_layer_providers[geo_locations.map_view_layer_google] = false;
geo_locations.map_view_layer_providers[geo_locations.map_view_layer_osm] = false;

geo_locations.map_view_layer_names_all = {};
geo_locations.map_view_layer_default = "";
geo_locations.map_view_layer_name = "";
geo_locations.map_view_layer_center_ini = {};
geo_locations.map_view_layer_center = null;
geo_locations.map_view_layer_zoom = 0;
geo_locations.map_art_view_width_default = 0;
geo_locations.map_art_view_height_default = 0;

// values for popup style properties
geo_locations.popup_width = 0;
geo_locations.popup_height = 0;
geo_locations.popup_video_default = "";
geo_locations.popup_video_labels = [];
geo_locations.popup_video_props = {};
geo_locations.popup_audio_default = "";
geo_locations.popup_audio_types = [];
geo_locations.popup_audio_props = {};
geo_locations.popup_audio_auto = "";
geo_locations.popup_audio_site = "";
geo_locations.popup_audio_object = "";

// values for the lines to display the proposed map size
geo_locations.map_art_view_width = 600;
geo_locations.map_art_view_height = 400;
geo_locations.map_art_view_top = 70;
geo_locations.map_art_view_right = 105;
geo_locations.map_art_view_width_display = 600;
geo_locations.map_art_view_height_display = 400;
geo_locations.map_art_view_top_display = 70;
geo_locations.map_art_view_right_display = 105;

// currently edited (via the edit link) point
geo_locations.edited_point = 0;

// the order of the pois done by drag-n-drop; we do not reorder pois in the layer
geo_locations.poi_order_user = [];

// map controls
geo_locations.select_control = null

// the pan zoom-and-bar control
geo_locations.pzb_ctrl = null;
geo_locations.not_to_pan_update = true;
// need to update drawing, but not to do it too frequently
geo_locations.map_dragging_last = null;
geo_locations.time_drag_delay = 500;

// for ids of pop-ups
geo_locations.cur_pop_rank = 0;

// tha map layer
geo_locations.map = null;
// the markers layer
geo_locations.layer = null;

// saving info on markers that should be deleted from db
geo_locations.poi_deletion = [];

// info on markers, with the original ids, so that we can push changes into db
geo_locations.poi_markers = [];

// whether map is shown, used at the initial version
geo_locations.map_shown = false;
geo_locations.map_obj = null

// auxiliary index for accordion selection
geo_locations.poi_rank_out = -1;

// auxiliary for POI side-bar updates
geo_locations.descs_elm = null;
geo_locations.descs_elm_name = "";
geo_locations.descs_inner = "";

// count of POIs, with/without counting removals
geo_locations.descs_count = 0;
geo_locations.descs_count_inc = 0;

// not to make new POI on closing a pop-up
geo_locations.ignore_click = false;
// the used pop-up window
geo_locations.popup = null;


// setting the article info
geo_locations.set_article_spec = function(params)
{
    this.article_number = parseInt(params.article_number);
    this.language_id = parseInt(params.language_id);
};

// setting the db based default info
geo_locations.set_map_info = function(params)
{
    //alert("geo_locations.set_map_info");
    this.map_view_layer_default = params.default;
    var prov_len = params.providers.length;
    for (var pind = 0; pind < prov_len; pind++)
    {
        this.map_view_layer_providers[params.providers[pind]] = true;
    }

    this.map_view_layer_center_ini = {"longitude": params.longitude, "latitude": params.latitude};
    this.map_view_layer_zoom = parseInt(params.resolution);

    this.map_art_view_width_default = parseInt(params.width);
    this.map_art_view_height_default = parseInt(params.height);

    if (800 < this.map_art_view_width_default)
    {
        this.map_width_change(800 - this.map_art_view_width);
    }
    if (500 < this.map_art_view_height_default)
    {
        this.map_height_change(500 - this.map_art_view_height);
    }

    var width_diff = this.map_art_view_width_default - this.map_art_view_width;
    var height_diff = this.map_art_view_height_default - this.map_art_view_height;

    this.map_width_change(width_diff);
    this.map_height_change(height_diff);

};
geo_locations.set_icons_info = function(params)
{
    //alert("geo_locations.set_icons_info");

    this.marker_src_base = params.webdir;
    this.marker_src_default = params.default;
    //this.marker_src_names = [];
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
            "height_off": parseFloat(cur_icon["height_off"]),
        };
    }

};
geo_locations.set_popups_info = function(params)
{
    //alert("geo_locations.set_popups_info");

    this.popup_width = params.width;
    this.popup_height = params.height;

    var video = params.video;

    this.popup_video_default = video.default;
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
        };
    }

    var audio = params.audio;

    this.popup_audio_auto = audio.auto;
    this.popup_audio_site = audio.site;
    this.popup_audio_object = audio.object;

    this.popup_audio_default = audio.default;

    var audio_len = audio.types.length;
    for (var aind = 0; aind < audio_len; aind++)
    {
        var cur_audio = audio.types[aind];
        var cur_type = cur_audio["type"];
        this.popup_audio_types.push(cur_type);
        this.popup_audio_props[cur_type] = {
            "mime": cur_audio["mime"],
        };
    }

};

geo_locations.update_poi_position = function(index, coordinate, value, input)
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
        this.popup.moveTo(pixel);
    }
};

// setting the edit window for the requested POI (bound on the 'edit' link)
geo_locations.edit_poi = function(index)
{
    this.edited_point = index;
    this.load_point_data();
    this.open_edit_window();

    return;
};

// to center the map view on the requested position
geo_locations.center_lonlat = function(longitude, latitude)
{
    var lonLat = new OpenLayers.LonLat(longitude, latitude).transform(
        new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
        this.map.getProjectionObject() // to Spherical Mercator Projection
    );

    this.map.setCenter (lonLat);
};

// sets map center onto the requested POI (bound on the 'center' link)
geo_locations.center_poi = function(index)
{
    var mlon = this.poi_markers[index].map_lon;
    var mlat = this.poi_markers[index].map_lat;
    var lonLat = new OpenLayers.LonLat(mlon, mlat);

    this.map.setCenter (lonLat);

};

// this function was used during development; probably will be removed
geo_locations.display_index = function(index)
{
    var real_index = -1;

    for (var rind = 0; rind <= index; rind++)
    {
        if (this.poi_markers[rind]['usage']) {real_index += 1;}
        else
        {
            alert("this should not happen now 0");
            alert(rind + " / " + index);
            alert(this.poi_markers[rind]);
        }
    }
    return real_index;
};

geo_locations.set_usage_poi = function(index, usage, div_ids)
{
    this.set_save_state(true);

    var to_disable = !usage;

    var layer_index = this.display_index(index);

    var feature = this.layer.features[layer_index];
    var attrs = feature.attributes;

    var cur_poi_info = this.poi_markers[layer_index];
    if (cur_poi_info.in_db)
    {
        //alert("to_disable: " + to_disable);
        //alert("attrs.m_disabled: " + attrs.m_disabled);
        if (to_disable != attrs.m_disabled)
        {
            cur_poi_info.content_changed = true;
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

        $(dis_obj).removeClass('hidden');
        $(voi_obj).removeClass('hidden');

        $(ena_obj).addClass('hidden');
        $(rem_obj).addClass('hidden');
    }
    else
    {
        lon_obj.disabled = true;
        lat_obj.disabled = true;

        $(ena_obj).removeClass('hidden');
        $(rem_obj).removeClass('hidden');

        $(dis_obj).addClass('hidden');
        $(voi_obj).addClass('hidden');
    }

    //this.update_poi_descs(index);
};

// removal of the requested POI (bound on the 'remove' link)
geo_locations.remove_poi = function(index)
{
    var really = confirm("Really to delete the point?\n\nThe removal is from all language versions of the article.");
    if (!really) {return;}

    this.set_save_state(true);
    this.poi_order_changed = true;

    var layer_index = this.display_index(index);

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

// updates the permuation of POIs (via UI 'sortable', or after a POI removal)
geo_locations.poi_order_update = function(poi_order_new)
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
geo_locations.poi_order_revert = function(index)
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

    if (!found) {alert("can not revert!");}

    return rev_index;
};

// for updating the side-bar with POI links
geo_locations.update_poi_descs = function(active, index_type)
{
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

    var max_ind = this.poi_order_user.length - 1;

    var descs_inner = "";
    var disp_index = 1;
    var pind = 0; // real initial poi index

    for(var sind = 0; sind <= max_ind; sind++)
    {
        pind = this.poi_order_user[sind];
        disp_index = pind + 1;

        var cur_poi = this.poi_markers[pind];
        if (!cur_poi.usage) {alert("this should not happen now 1"); continue;}

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

        descs_inner += "<div id=\"poi_seq_" + pind + "\">";
        descs_inner += "<h3 class=\"" + use_class + class_show + " map_poi_side_one\">";
        descs_inner += "<a class='poi_name' href=\"#\">" + disp_index + cur_label_sep + cur_label + "</a></h3>";
        descs_inner += "<div class='poi_actions'>";
        descs_inner += "<div class='poi_actions'>";
        descs_inner += "(<a href='#' onclick='geo_locations.edit_poi(" + pind + ");return false;'>edit</a>)&nbsp;";
        descs_inner += "(<a href='#' onclick='geo_locations.center_poi(" + pind + ");return false;'>center</a>)";
        //descs_inner += "(<a href='#' onclick='geo_locations.remove_poi(" + pind + ");return false;'>remove</a>)&nbsp;";

        //descs_inner += "</div>";

        var disable_value = "";
        if (cur_marker && cur_marker.attributes.m_disabled) {disable_value = " disabled=disabled";}

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
            dis_class += " hidden";
            voi_class += " hidden";
        }
        else
        {
            ena_class += " hidden";
            rem_class += " hidden";
        }

        var prop_ids = '["' + lon_id + '", "' + lat_id + '", "' + dis_id + '", "' + ena_id + '", "' + voi_id + '", "' + rem_id + '"]';

        //var disable_value = "";
        //if (cur_marker && cur_marker.attributes.m_disabled) {disable_value = " disabled=disabled";}

        descs_inner += "&nbsp;";
        descs_inner += "<span id='" + ena_id + "' class='" + ena_class + "'>(<a href='#' onclick='geo_locations.set_usage_poi(" + pind + ", true, " + prop_ids + ");return false;'>enable</a>)</span>";
        descs_inner += "<span id='" + dis_id + "' class='" + dis_class + "'>(<a href='#' onclick='geo_locations.set_usage_poi(" + pind + ", false, " + prop_ids + ");return false;'>disable</a>)</span>";
        descs_inner += "</div>";

        descs_inner += "<div class='poi_coors'>";
        descs_inner += "lat: <input id='" + lat_id + "' class='poi_coors_input' size='8' onChange='geo_locations.update_poi_position(" + pind + ", \"latitude\", this.value, this); return false;' name='poi_latitude_" + pind + "' value='" + cur_poi.lat.toFixed(6) + "'" + disable_value + ">";
        descs_inner += "</div>";
        descs_inner += "<div class='poi_coors'>";
        descs_inner += "lon: <input id='" + lon_id + "' class='poi_coors_input' size='8' onChange='geo_locations.update_poi_position(" + pind + ", \"longitude\", this.value, this); return false;' name='poi_longitude_" + pind + "'  value='" + cur_poi.lon.toFixed(6) + "'" + disable_value + ">";
        descs_inner += "</div>";

        descs_inner += "<div class='poi_actions poi_removal'>";
/*
        if (cur_marker && cur_marker.attributes.m_disabled)
        {
            dis_class += " hidden";
            voi_class += " hidden";
        }
        else
        {
            ena_class += " hidden";
            rem_class += " hidden";
        }

        descs_inner += "<span id='" + ena_id + "' class='" + ena_class + "'>(<a href='#' onclick='geo_locations.set_usage_poi(" + pind + ", true, " + prop_ids + ");return false;'>enable</a>)</span>";
        descs_inner += "<span id='" + dis_id + "' class='" + dis_class + "'>(<a href='#' onclick='geo_locations.set_usage_poi(" + pind + ", false, " + prop_ids + ");return false;'>disable</a>)</span>";
        descs_inner += "&nbsp;";
*/
        descs_inner += "<div id='" + rem_id + "' class='" + rem_class + "'>(<a href='#' onclick='geo_locations.remove_poi(" + pind + ");return false;'>remove</a>)</div>";
        descs_inner += "<div id='" + voi_id + "' class='" + voi_class + "'>(<a href='#' onclick='geo_locations.remove_poi(" + pind + ");return false;'>remove</a>)</div>";
        //descs_inner += "<span id='" + voi_id + "' class='" + voi_class + "'>(<span class='action_disabled'>removal&nbsp;after&nbsp;disabling</span>)</span>";

        descs_inner += "</div>";
        descs_inner += "</div>";
        descs_inner += "</div>";

        disp_index += 1;
    }
    this.descs_elm.innerHTML = "<div class='map_poi_side_list' id='map_poi_side_list'>" + descs_inner + "</div>";

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

        $("#map_poi_side_list").accordion({active: view_index, header: "> div > h3"}).sortable({axis: "y", handle: "h3", stop: function() {stop = true;} });

        $("#map_poi_side_list").bind( "sortupdate", function(event, ui) {
            var poi_order = $(this).sortable('toArray');
            geo_locations.poi_order_update(poi_order);
        });

    });

    this.map_update_side_desc_height();
};

// sets the height of the side-bar part with POIs, so that it fits into the rest of the side-bar
geo_locations.map_update_side_desc_height = function()
{
    var searchres_obj = document.getElementById ? document.getElementById("search_results") : null;
    var height_taken = searchres_obj.offsetHeight;

    var sidedesc_obj = document.getElementById ? document.getElementById("map_sidedescs") : null;

    //var old_height = sidedesc_obj.offsetHeight;
    var new_height = 450 - height_taken;

    //if (old_height > new_height)
    {
        sidedesc_obj.style.height = new_height + "px";
    }
};

// the POI markers are not re-drawn after some actions happen; this is a part of the fix;
geo_locations.map_feature_redraw = function(xy, delay)
{

    var cur_date = new Date();
    var cur_time = cur_date.getTime();

    var time_delay = this.time_drag_delay;
    if (undefined !== delay)
    {
        time_delay = delay;
    }

    if (time_delay <= (cur_time - this.map_dragging_last))
    {
        geo_hook_map_dragged(xy);
        this.map_dragging_last = cur_time;
    }

};

// adding redrawing of the POI icons on map panning
var geo_hook_map_bar_panning = function(evt)
{
    geo_locations.map_feature_redraw(0, 500);

    if (!OpenLayers.Event.isLeftClick(evt)) {
        return;
    }
    switch (this.action) {
      case "panup":
        this.map.pan(0, - this.getSlideFactor("h"));
        break;
      case "pandown":
        this.map.pan(0, this.getSlideFactor("h"));
        break;
      case "panleft":
        this.map.pan(- this.getSlideFactor("w"), 0);
        break;
      case "panright":
        this.map.pan(this.getSlideFactor("w"), 0);
        break;
      case "zoomin":
        this.map.zoomIn();
        break;
      case "zoomout":
        this.map.zoomOut();
        break;
      case "zoomworld":
        this.map.zoomToMaxExtent();
        break;
      default:;
    }

    OpenLayers.Event.stop(evt);

};

// adding redrawing of the POI icons on map panning
var geo_hook_map_dragging = function(xy)
{

    this.panned = true;

    this.map.pan(this.handler.last.x - xy.x, this.handler.last.y - xy.y, {dragging: this.handler.dragging, animate: false});

    geo_locations.map_feature_redraw(xy);

};

// adding redrawing of the POI icons on bar panning
var geo_hook_map_dragged = function(pixel)
{
    var new_center = geo_locations.map.center.clone();
    geo_locations.map.setCenter(new_center);

    geo_locations.select_control.destroy();
    geo_locations.select_control = new OpenLayers.Control.SelectFeature(geo_locations.layer);
    geo_locations.map.addControl(geo_locations.select_control);
    geo_locations.select_control.activate();
};

// taking POI-mouse offset on the start of a POI dragging
var geo_hook_poi_dragg_start = function(feature, pixel)
{
    geo_locations.poi_drag_offset = null;

    if ((undefined === feature.attributes) || (undefined === feature.attributes.m_rank))
    {
      return;
    }

    var index = feature.attributes.m_rank;
    var cur_poi_info = geo_locations.poi_markers[index];

    var lonlat = geo_locations.map.getLonLatFromViewPortPx(pixel);

    cur_poi_info['map_lon_offset'] = lonlat.lon - cur_poi_info['map_lon'];
    cur_poi_info['map_lat_offset'] = lonlat.lat - cur_poi_info['map_lat'];

};

// updating info on POI after it was dragged
var geo_hook_poi_dragged = function(feature, pixel)
{
    if ((undefined === feature.attributes) || (undefined === feature.attributes.m_rank))
    {
      return;
    }

    geo_locations.set_save_state(true);

    var index = feature.attributes.m_rank;
    var cur_poi_info = geo_locations.poi_markers[index];

    var lonlat = geo_locations.map.getLonLatFromViewPortPx(pixel);

    lonlat.lon -= cur_poi_info['map_lon_offset'];
    lonlat.lat -= cur_poi_info['map_lat_offset'];

    cur_poi_info['map_lon'] = lonlat.lon;
    cur_poi_info['map_lat'] = lonlat.lat;

    lonlat.transform(geo_locations.map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));

    if (cur_poi_info.in_db)
    {
        if ((lonlat.lon != cur_poi_info['lon']) || (lonlat.lat != cur_poi_info['lat']))
        {
            cur_poi_info.location_changed = true;
        }
    }
    cur_poi_info['lon'] = lonlat.lon;
    cur_poi_info['lat'] = lonlat.lat;

    geo_locations.update_poi_descs(index);

    // to move the POI's pop-up too, if it is displayed
    if (geo_locations.popup && (feature == geo_locations.popup.feature)) {
        geo_locations.popup.moveTo(pixel);
    }
};

// to insert new POI on map click, but not on a click that closes a pop-up
var geo_hook_trigger_on_map_click = function(e)
{
    if (geo_locations.ignore_click) {
        geo_locations.ignore_click = false;
        return;
    }

    var lonlat = geo_locations.map.getLonLatFromViewPortPx(e.xy);

    geo_locations.insert_poi('map', lonlat);
};

// actual insertion of a new POI
geo_locations.insert_poi = function(coor_type, lonlat_ini, longitude, latitude, label)
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

    var poi_title = "POI no. " + (this.descs_count_inc + 1);
    if (undefined !== label)
    {
        poi_title = label;
    }

    // making poi for features
    var features = [];
    var point = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
    var vector = new OpenLayers.Feature.Vector(point, {type: (2 * this.marker_src_default_ind)});

    this.poi_rank_out = this.descs_count;
    vector.attributes.m_rank = this.descs_count;
    vector.attributes.m_title = poi_title;
    vector.attributes.m_perex = "";
    vector.attributes.m_direct = false;
    vector.attributes.m_content = "";
    vector.attributes.m_link = "";
    vector.attributes.m_text = "fill in the POI description";
    vector.attributes.m_image_source = "";
    vector.attributes.m_image_width = "";
    vector.attributes.m_image_height = "";
    vector.attributes.m_video_type = "";
    vector.attributes.m_video_id = "";
    vector.attributes.m_video_width = "";
    vector.attributes.m_video_height = "";
    vector.attributes.m_image = "";
    vector.attributes.m_embed = "";
    vector.attributes.m_disabled = false;
    vector.attributes.m_type = this.marker_src_default_ind;

    features.push(vector);

    this.select_control.destroy();

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
        'usage':true, "location_changed": false, "content_changed": false,
        "in_db": false, "con_index": 0, "loc_index": 0, "tmp_index": this.descs_count_inc
    });

    this.poi_order_user.push(this.descs_count);
    this.update_poi_descs(this.descs_count);

    this.descs_count += 1;
    this.descs_count_inc += 1;

    this.select_control = new OpenLayers.Control.SelectFeature(this.layer);
    this.map.addControl(this.select_control);
    this.select_control.activate();

    return true;
};

// map related initialization
var geo_main_openlayers_init = function(map_div_name)
{

    OpenLayers.Control.Hover = OpenLayers.Class(OpenLayers.Control, {
        defaultHandlerOptions: {
            //'delay': 100,
            //'pixelTolerance': 10,
            'delay': 200,
            'pixelTolerance': 2,
        },
        initialize: function(options) {
            this.handlerOptions = OpenLayers.Util.extend(
                {}, this.defaultHandlerOptions
            );
            OpenLayers.Control.prototype.initialize.apply(
                this, arguments
            );
            this.handler = new OpenLayers.Handler.Hover(
                this, {
                    'pause': this.trigger
                }, this.handlerOptions
            );
        },
        trigger: function(evt) {
            var poi_hover = geo_locations.layer.getFeatureFromEvent(evt);
            if (poi_hover) {
                if (null !== poi_hover.attributes.m_rank) {
                    geo_locations.poi_rank_out = poi_hover.attributes.m_rank;
                    //$("#map_poi_side_list").accordion("activate", geo_locations.display_index(geo_locations.poi_rank_out));
                    geo_locations.update_poi_descs(geo_locations.poi_rank_out);
                }
            }
        }
    });

    OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
        defaultHandlerOptions: {
            'single': true,
            'double': false,
            'pixelTolerance': 0,
            'stopSingle': false,
            'stopDouble': false,
            'projection': new OpenLayers.Projection("EPSG:900913"),
            'displayProjection': new OpenLayers.Projection("EPSG:900913"),
            //'displayProjection': new OpenLayers.Projection("EPSG:4326"),
        },

        initialize: function(options) {
            this.handlerOptions = OpenLayers.Util.extend(
                {}, this.defaultHandlerOptions
            );
            OpenLayers.Control.prototype.initialize.apply(
                this, arguments
            );
            this.handler = new OpenLayers.Handler.Click(
                this, {
                    'click': geo_hook_trigger_on_map_click
                }, this.handlerOptions
            );
        }, 

    });

    geo_locations.pzb_ctrl = new OpenLayers.Control.PanZoomBar();
    geo_locations.pzb_ctrl.buttonDown = geo_hook_map_bar_panning;

    geo_locations.map = new OpenLayers.Map(map_div_name, {
        controls: [
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.PanZoomBar(),
            geo_locations.pzb_ctrl,
            new OpenLayers.Control.ScaleLine(),
            //new OpenLayers.Control.LayerSwitcher({'ascending':false}),
            //new OpenLayers.Control.Permalink('permalink'),
            //new OpenLayers.Control.MousePosition(),
            //new OpenLayers.Control.OverviewMap(),
            //new OpenLayers.Control.KeyboardDefaults()
        ],
        numZoomLevels: 20
    });

    var map_provs = [];
    //var map_default = null;
    var map_gsm = null;
    var map_osm = null;

    geo_locations.map_view_layer_names_all = {};

    var google_label = geo_locations.map_view_layer_google;
    var osm_label = geo_locations.map_view_layer_osm;

    if (geo_locations.map_view_layer_providers[google_label])
    {
        // google map v3
        map_gsm = new OpenLayers.Layer.Google(
            "Google Streets", // the default
            {numZoomLevels: 20, 'sphericalMercator': true}
        );
        geo_locations.map_view_layer_names_all[google_label] = map_gsm.name;
        if (google_label == geo_locations.map_view_layer_default)
        {
            //map_default = map_gsm;
            map_provs.push(map_gsm);
        }
    }

    if (geo_locations.map_view_layer_providers[osm_label])
    {
        // openstreetmap
        var map_osm = new OpenLayers.Layer.OSM();
        geo_locations.map_view_layer_names_all[osm_label] = map_osm.name;
        if (osm_label == geo_locations.map_view_layer_default)
        {
            //map_default = map_osm;
            map_provs.push(map_osm);
        }
    }

    //map_provs.push(map_default);
    if (map_gsm && (google_label != geo_locations.map_view_layer_default))
    {
        map_provs.push(map_gsm);
    }
    if (map_osm && (osm_label != geo_locations.map_view_layer_default))
    {
        map_provs.push(map_osm);
    }

    //geo_locations.map.addLayers([map_gsm, map_osm]);
    geo_locations.map.addLayers(map_provs);
    // for switching between maps
    geo_locations.map.addControl(new OpenLayers.Control.LayerSwitcher());

    // an initial center point, set via parameters
    var cen_ini_longitude = geo_locations.map_view_layer_center_ini["longitude"];
    var cen_ini_latitude = geo_locations.map_view_layer_center_ini["latitude"];
    var lonLat_cen = new OpenLayers.LonLat(cen_ini_longitude, cen_ini_latitude)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            geo_locations.map.getProjectionObject() // to Spherical Mercator Projection
          );
/*
    // two initial demo points for center and features/markers
    var lonLat_ini = {'lon': 14.424133, 'lat': 50.089926}
    var lonLat = new OpenLayers.LonLat(lonLat_ini.lon, lonLat_ini.lat)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            geo_locations.map.getProjectionObject() // to Spherical Mercator Projection
          );
    var lonLat2_ini = {'lon': 13.4105, 'lat': 52.5244}
    var lonLat2 = new OpenLayers.LonLat(lonLat2_ini.lon, lonLat2_ini.lat)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            geo_locations.map.getProjectionObject() // to Spherical Mercator Projection
          );
*/
    //var zoom = 6; // the 4 should be (probably) the default
    var zoom = geo_locations.map_view_layer_zoom;

    var style_map = new OpenLayers.StyleMap({
                //fillOpacity: 1.0,
                //pointRadius: 10,
                //graphicWidth: 21,
                //graphicHeight: 25,
                //graphicYOffset: -20,
                //graphicXOffset: -10,
                //graphicYOffset: -25,
                //graphicXOffset: -10.5,
                graphicZIndex: 10,
    });

    var lookup = {};
    var labels_len = geo_locations.marker_src_labels.length;
    for (var lind = 0; lind < labels_len; lind++)
    {
        var cur_label = geo_locations.marker_src_labels[lind];
        var cur_icon = geo_locations.marker_src_icons[cur_label];
        lookup[2*lind] = {
            fillOpacity: 1.0,
            externalGraphic: cur_icon["path"],
            graphicWidth: cur_icon["width"],
            graphicHeight: cur_icon["height"],
            graphicXOffset: cur_icon["width_off"],
            graphicYOffset: cur_icon["height_off"],
        };
        lookup[(2*lind)+1] = {
            fillOpacity: 0.4,
            externalGraphic: cur_icon["path"],
            graphicWidth: cur_icon["width"],
            graphicHeight: cur_icon["height"],
            graphicXOffset: cur_icon["width_off"],
            graphicYOffset: cur_icon["height_off"],
        };
    };

    // create a lookup table for the provided icon types
    style_map.addUniqueValueRules("default", "type", lookup);

    // layer for features
    geo_locations.layer = new OpenLayers.Layer.Vector(
        "POI markers",
        {
            styleMap: style_map,
            isBaseLayer: false,
            rendererOptions: {yOrdering: true}
        }
    );
    geo_locations.map.addLayer(geo_locations.layer);
/*
    // setting some demo feature POIs
    var features = [];
    var point = null;
    var vector = null;
    point = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);
    vector = new OpenLayers.Feature.Vector(point, {type: geo_locations.marker_src_default_ind, title: "bah A"});
    vector.attributes.m_rank = 0;
    vector.attributes.m_title = "Prague";
    vector.attributes.m_link = "http://campsite.sourcefabric.org/";
    vector.attributes.m_description = "Great Campsite";
    vector.attributes.m_embed = "";
    vector.attributes.m_image = "http://www.sourcefabric.org/get_img.php?NrImage=120&NrArticle=6";
    features.push(vector);

    geo_locations.poi_markers.push({'lon': lonLat_ini.lon, 'lat': lonLat_ini.lat, 'usage':true, 'map_lon': lonLat.lon, 'map_lat': lonLat.lat, "in_db": true, "db_index": 0});

    point = new OpenLayers.Geometry.Point(lonLat2.lon, lonLat2.lat);
    vector2 = new OpenLayers.Feature.Vector(point, {type: geo_locations.marker_src_default_ind, title: "bah 2"});
    vector2.attributes.m_rank = 1;
    vector2.attributes.m_title = "Berlin";
    vector2.attributes.m_link = "http://www.sourcefabric.org/en/home/articles/226/Sourcecamp-2010---minute-by-minute.htm?tpl=32";
    vector2.attributes.m_description = "Great Sourcefabric";
    vector2.attributes.m_embed = '';
    vector2.attributes.m_image = "http://www.sourcefabric.org/get_img?NrArticle=226&NrImage=1";
    features.push(vector2);

    geo_locations.poi_markers.push({'lon': lonLat2_ini.lon, 'lat': lonLat2_ini.lat, 'usage':true, 'map_lon': lonLat2.lon, 'map_lat': lonLat2.lat, "in_db": true, "db_index": 1});
    geo_locations.descs_count = 2;
    geo_locations.descs_count_inc = 2;

    geo_locations.layer.addFeatures(features);

    geo_locations.poi_order_user = [0, 1];
    geo_locations.update_poi_descs();
*/
    // setting map center
    //geo_locations.map.setCenter (lonLat, zoom);
    geo_locations.map.setCenter (lonLat_cen, zoom);

    geo_locations.map_view_layer_name = geo_locations.map.layers[0].name;
    geo_locations.map_view_layer_center = geo_locations.map.getCenter();
    geo_locations.map_view_layer_zoom = geo_locations.map.getZoom();

    // registering for click events
    var click = new OpenLayers.Control.Click();
    geo_locations.map.addControl(click);
    click.activate();

    var hover = new OpenLayers.Control.Hover();
    geo_locations.map.addControl(hover);
    hover.activate();

    var cur_date = new Date();
    geo_locations.map_dragging_last = cur_date.getTime();

    var drag_feature = new OpenLayers.Control.DragFeature(geo_locations.layer);
    drag_feature.onStart = geo_hook_poi_dragg_start;
    drag_feature.onComplete = geo_hook_poi_dragged;
    geo_locations.map.addControl(drag_feature);
    drag_feature.activate();

    var drag_map = new OpenLayers.Control.DragPan([map_gsm, map_osm]);
    drag_map.panMapDone = geo_hook_map_dragged;
    drag_map.panMap = geo_hook_map_dragging;
    geo_locations.map.addControl(drag_map);
    drag_map.activate();

    geo_locations.select_control = new OpenLayers.Control.SelectFeature(geo_locations.layer);
    geo_locations.map.addControl(geo_locations.select_control);
    geo_locations.select_control.activate();

    geo_locations.layer.events.on({
        'featureselected': geo_hook_on_feature_select,
        'featureunselected': geo_hook_on_feature_unselect
    });

};

// needed just for click on pop-up close button
var geo_hook_on_popup_close = function(evt)
{
    geo_locations.ignore_click = true;
    geo_locations.select_control.unselect(this.feature);
};

// when a feature pop-up should be removed on map event
var geo_hook_on_feature_unselect = function(evt)
{
    var feature = evt.feature;

    if (feature.popup) {
        geo_locations.popup.feature = null;
        geo_locations.map.removePopup(feature.popup);
        feature.popup.destroy();
        feature.popup = null;
        geo_locations.popup = null;
    }

};

// when a feature pop-up should be diplayed on map event
var geo_hook_on_feature_select = function(evt)
{
    var feature = evt.feature;
    if (!feature) {return;}

    var attrs = feature.attributes;
    if (!attrs) {return;}

/*
    if (0 < pop_link.length) {
        var to_prepend = true;
        if ("http://" == pop_link.substr(0, 7)) {
            to_prepend = false;
        }
        if ("https://" == pop_link.substr(0, 8)) {
            to_prepend = false;
        }
        if ("ftp://" == pop_link.substr(0, 6)) {
            to_prepend = false;
        }
        if ("ftps://" == pop_link.substr(0, 7)) {
            to_prepend = false;
        }
        if (to_prepend) {pop_link = "http://" + pop_link;}
    }
*/
    var pop_text = "";
    {
        var pop_link = attrs.m_link;

        pop_text += "<div class='popup_title'>";
        if (0 < pop_link.length) {
            pop_text += "<a href=\"" + pop_link + "\" target=\"_blank\">";
        }
        pop_text += feature.attributes.m_title;
        if (0 < pop_link.length) {
            pop_text += "</a>";
        }
        pop_text += "</div>";
    }

    if (attrs.m_direct)
    {
        var content = attrs.m_content;
        if (!content) {content = "";}
        pop_text += "<div class='popup_content'>" + content + "</div>";
    }
    else
    {
        var with_embed = false;
        if (feature.attributes.m_embed)
        {
            pop_text += "<div class='popup_video'>" + feature.attributes.m_embed + "</div>";
            with_embed = true;
        }
        else
        {
            if (feature.attributes.m_image)
            {
                pop_text += "<div class='popup_image'>" + feature.attributes.m_image + "</div>";
            }
        }
    
        pop_text += "<div class='popup_text'>" + feature.attributes.m_text + "</div>";
    }

    geo_locations.cur_pop_rank += 1;
    geo_locations.popup = new OpenLayers.Popup.FramedCloud("featurePopup_" + geo_locations.cur_pop_rank,
        feature.geometry.getBounds().getCenterLonLat(),
        new OpenLayers.Size(geo_locations.popup_width,geo_locations.popup_height),
        pop_text,
        null, true, geo_hook_on_popup_close);

    var min_width = geo_locations.popup_width;
    var min_height = geo_locations.popup_height;
    if (with_embed) {
        min_width = feature.attributes.m_embed_width + 100;
        min_height = feature.attributes.m_embed_height + 100;
    }

    geo_locations.popup.minSize = new OpenLayers.Size(min_width, min_height);

    feature.popup = geo_locations.popup;
    geo_locations.popup.feature = feature;
    geo_locations.map.addPopup(geo_locations.popup);

};

// for closing the edit window
geo_locations.close_edit_window = function ()
{
    $("#map_mapedit").addClass('hidden');
};

// for displaying the edit window
geo_locations.open_edit_window = function ()
{
    $("#map_mapedit").removeClass('hidden');
};

// the entry initialization point
var geo_main_selecting_locations = function (geocodingdir, div_name, descs_name, names_show, names_hide, editing)
{
    // doing the divs show/hide task first
    // the show/hide part was used mainly at the initial version
    var map_canvas = document.getElementById ? document.getElementById(div_name) : null;
    geo_locations.descs_elm = document.getElementById ? document.getElementById(descs_name) : null;
    geo_locations.descs_elm_name = descs_name;

    var divs_show = [];
    var divs_hide = [];

    var show_obj = null;
    var hide_obj = null;

    if (names_show) {
        var divs_show_names = names_show.split(",");
        var len_show_names = divs_show_names.length;
        for (var nsind = len_show_names - 1; nsind >= 0; nsind--)
        {
            show_obj = null;
            show_obj = document.getElementById ? document.getElementById(divs_show_names[nsind]) : null;
            if (show_obj) {divs_show.push(show_obj);}
        }
    }

    if (names_hide) {
        var divs_hide_names = names_hide.split(",");
        var len_hide_names = divs_hide_names.length;
        for (var nhind = len_hide_names - 1; nhind >= 0; nhind--)
        {
            hide_obj = null;
            hide_obj = document.getElementById ? document.getElementById(divs_hide_names[nhind]) : null;
            if (hide_obj) {divs_hide.push(hide_obj);}
        }
    }

    var use_show_class = "map-shown";
    var use_hide_class = "map-hidden";
    if (geo_locations.map_shown)
    {
        use_show_class = "map-hidden";
        use_hide_class = "map-shown";
    }

    {
        var len_show = divs_show.length;
        for (var dsind = len_show - 1; dsind >= 0; dsind--)
        {
            show_obj = divs_show[dsind];
            show_obj.className = use_show_class;
        }

        var len_hide = divs_hide.length;
        for (var dhind = len_hide - 1; dhind >= 0; dhind--)
        {
            hide_obj = divs_hide[dhind];
            hide_obj.className = use_hide_class;
        }
    }

    if (geo_locations.map_shown) {
        geo_locations.map_shown = false;
        return;
    }

    geo_locations.map_shown = true;

    if (geo_locations.map_obj) {return;}
    geo_locations.map_obj = true;

    useSystemParameters();

    geo_locations.map_edit_prepare_markers();

    // call the map-related initialization
/* TODO: DISABLED ON DEBUG MODE */
    geo_main_openlayers_init(div_name);
/* */

    geo_locations.map_pois_load();

    geo_locations.set_save_state(false);
};

// showing the current initial reader view
geo_locations.map_showview = function()
{
    var map_names = this.map.getLayersByName(this.map_view_layer_name);
    if (0 < map_names.length)
    {
        this.map.setBaseLayer(map_names[0]);
    }
    this.map.setCenter(this.map_view_layer_center, this.map_view_layer_zoom);
};

// setting the current view as the reader initial view
geo_locations.map_setview = function()
{
    this.set_save_state(true);

    this.map_view_layer_name = this.map.baseLayer.name;
    this.map_view_layer_center = this.map.getCenter();
    this.map_view_layer_zoom = this.map.getZoom();
};

// changing the size for the map div for the reader view
geo_locations.map_width_change = function(size)
{
    if ((0 > size) && (10 >= this.map_art_view_width)) {return;}
    if ((0 < size) && (1200 <= this.map_art_view_width)) {return;}

    this.set_save_state(true);

    var map_left_border = document.getElementById ? document.getElementById("map_part_left") : null;
    var map_right_border = document.getElementById ? document.getElementById("map_part_right") : null;
    var map_top_border = document.getElementById ? document.getElementById("map_part_top") : null;
    var map_bottom_border = document.getElementById ? document.getElementById("map_part_bottom") : null;

    var map_view_size = document.getElementById ? document.getElementById("map_view_size") : null;

    this.map_art_view_width += size;
    this.map_art_view_right -= size / 2;

    map_view_size.innerHTML = this.map_art_view_width + "x" + this.map_art_view_height;

    var border_width = 1;
    if (800 < this.map_art_view_width) {border_width = 0;}
    map_left_border.style.borderWidth = border_width;
    map_right_border.style.borderWidth = border_width;

    if ((0 > size) && (800 == this.map_art_view_width)) {return;}
    if (800 < this.map_art_view_width) {return;}

    this.map_art_view_width_display += size;
    this.map_art_view_right_display -= size / 2;

    map_left_border.style.right = (this.map_art_view_right_display + this.map_art_view_width_display) + "px";
    map_right_border.style.right = this.map_art_view_right_display + "px";
    map_top_border.style.width = this.map_art_view_width_display + "px";
    map_top_border.style.right = this.map_art_view_right_display + "px";
    map_bottom_border.style.width = this.map_art_view_width_display + "px";
    map_bottom_border.style.right = this.map_art_view_right_display + "px";

};

// changing the size for the map div for the reader view
geo_locations.map_height_change = function(size)
{
    if ((0 > size) && (10 >= this.map_art_view_height)) {return;}
    if ((0 < size) && (1200 <= this.map_art_view_height)) {return;}

    this.set_save_state(true);

    var map_left_border = document.getElementById ? document.getElementById("map_part_left") : null;
    var map_right_border = document.getElementById ? document.getElementById("map_part_right") : null;
    var map_top_border = document.getElementById ? document.getElementById("map_part_top") : null;
    var map_bottom_border = document.getElementById ? document.getElementById("map_part_bottom") : null;

    var map_view_size = document.getElementById ? document.getElementById("map_view_size") : null;

    this.map_art_view_height += size;
    this.map_art_view_top -= size / 2;

    map_view_size.innerHTML = this.map_art_view_width + "x" + this.map_art_view_height;

    var border_width = 1;
    if (500 < this.map_art_view_height) {border_width = 0;}
    map_top_border.style.borderWidth = border_width;
    map_bottom_border.style.borderWidth = border_width;

    if ((0 > size) && (500 == this.map_art_view_height)) {return;}
    if (500 < this.map_art_view_height) {return;}

    this.map_art_view_height_display += size;
    this.map_art_view_top_display -= size / 2;

    map_bottom_border.style.top = (this.map_art_view_top_display + this.map_art_view_height_display) + "px";
    map_top_border.style.top = this.map_art_view_top_display + "px";
    map_right_border.style.height = this.map_art_view_height_display + "px";
    map_right_border.style.top = this.map_art_view_top_display + "px";
    map_left_border.style.height = this.map_art_view_height_display + "px";
    map_left_border.style.top = this.map_art_view_top_display + "px";

};

// the data that should be loaded on POI edit start
geo_locations.load_point_data = function()
{
    this.load_point_label();
    this.load_point_icon();

    this.load_point_properties();

    this.load_point_direct();
};

// storing POI's visible name
geo_locations.store_point_label = function()
{
    this.set_save_state(true);

    var label_obj = document.getElementById ? document.getElementById("point_label") : null;

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];
    var cur_poi_info = this.poi_markers[use_index];

    if (cur_poi_info.in_db)
    {
        if (label_obj.value != cur_marker.attributes.m_title)
        {
            cur_poi_info.content_changed = true;
        }
    }
    cur_marker.attributes.m_title = label_obj.value;

    this.update_poi_descs(this.edited_point);
};

// loading POI's visible name
geo_locations.load_point_label = function()
{
    var label_obj = document.getElementById ? document.getElementById("point_label") : null;

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];
    label_obj.value = cur_marker.attributes.m_title;

};

geo_locations.set_image_tag = function(attrs)
{
    attrs.m_image = "";

    var img_src = attrs.m_image_source;
    if (!img_src) {img_src = "";}
    if (0 < img_src.length)
    {
        var img_value = "<img src='" + img_src + "'";
        var img_height = attrs.m_image_height;
        if (undefined !== img_height) {img_value += " height='" + img_height + "'";}
        var img_width = attrs.m_image_width;
        if (undefined !== img_width) {img_value += " width='" + img_width + "'";}
        img_value += " />";

        attrs.m_image = img_value;
    }
};

geo_locations.set_embed_tag = function(attrs)
{
    attrs.m_embed = "";
    attrs.m_embed_width = 0;
    attrs.m_embed_height = 0;

    var vid_id = attrs.m_video_id;
    var vid_type = attrs.m_video_type;
    if (!vid_id) {vid_id = "";}
    if (!vid_type) {vid_type = "none";}

    var vid_define = null;
    if ("none" != vid_type)
    {
        vid_define = this.popup_video_props[vid_type];
    }

    if ((0 < vid_id.length) && vid_define)
    {
        var vid_src = vid_define["source"];
        if (!vid_src) {vid_src = "";}

        var vid_value = vid_src.replace(/%%id%%/g, vid_id);

        var vid_height = attrs.m_video_height;
        if ((!vid_height) || ("" == vid_height)) {vid_height = vid_define["height"];}
        var vid_width = attrs.m_video_width;
        if ((!vid_width) || ("" == vid_width)) {vid_width = vid_define["width"];}

        vid_value = vid_value.replace(/%%h%%/g, vid_height);
        vid_value = vid_value.replace(/%%w%%/g, vid_width);

        attrs.m_embed = vid_value;
        attrs.m_embed_height = parseInt(vid_height);
        attrs.m_embed_width = parseInt(vid_width);

    }

};

// storing POI's specified property
geo_locations.store_point_property = function(property, value)
{
    this.set_save_state(true);

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];

    var poi_property = "m_" + property;

    var attrs = cur_marker.attributes;

    var cur_poi_info = this.poi_markers[use_index];
    if (cur_poi_info.in_db)
    {
        if (value != attrs[poi_property])
        {
            cur_poi_info.content_changed = true;
        }
    }
    attrs[poi_property] = value;

    if ("image" == property.substr(0, 5))
    {
        this.set_image_tag(attrs);
    }

    if ("video" == property.substr(0, 5))
    {
        this.set_embed_tag(attrs);
    }

};

// loading POI's properties
geo_locations.load_point_properties = function()
{
    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];

    var poi_prop_names = {};
    poi_prop_names['perex'] = "point_perex";
    poi_prop_names['text'] = "point_descr";
    poi_prop_names['link'] = "point_link";
    poi_prop_names['content'] = "point_content";
    poi_prop_names['image_source'] = "point_image";
    poi_prop_names['image_width'] = "point_image_width";
    poi_prop_names['image_height'] = "point_image_height";
    poi_prop_names['video_id'] = "point_video";
    poi_prop_names['video_width'] = "point_video_width";
    poi_prop_names['video_height'] = "point_video_height";

    for (one_name in poi_prop_names)
    {
        var div_name = poi_prop_names[one_name];
        var div_obj = document.getElementById ? document.getElementById(div_name) : null;

        var poi_property = "m_" + one_name;
        var one_value = cur_marker.attributes[poi_property];
        if (!one_value) {one_value = "";}
        div_obj.value = one_value;
    }

    video_type_names = ['none', 'youtube', 'vimeo'];
    video_type_count = video_type_names.length;
    for (var vind = 0; vind < video_type_count; vind++)
    {
        var one_video_type = video_type_names[vind];
        var one_video_div_name = 'point_video_type_' + one_video_type;
        var one_video_div_obj = document.getElementById ? document.getElementById(one_video_div_name) : null;
        one_video_div_obj.checked = false;
    }

    var video_type = cur_marker.attributes['m_video_type'];
    if (!video_type) {video_type = "none";}
    var video_div_name = 'point_video_type_' + video_type;
    var video_div_obj = document.getElementById ? document.getElementById(video_div_name) : null;
    video_div_obj.checked = true;

};

// loading POI's marker icon
geo_locations.load_point_icon = function()
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
geo_locations.store_point_direct = function(direct_usage)
{
    //alert("du0: " + direct_usage);
    var direct_num = 0;
    var direct_bool = false;
    if (direct_usage && (0 != direct_usage))
    {
        //alert("is direct");
        direct_num = 1;
        direct_bool = true;
    }

    //alert(direct);

    this.set_save_state(true);

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];

    var cur_poi_info = this.poi_markers[use_index];
    if (cur_poi_info.in_db)
    {
        if (direct_bool != cur_marker.attributes.m_direct)
        {
            cur_poi_info.content_changed = true;
        }
    }

    cur_marker.attributes.m_direct = direct_bool;

    this.set_edit_direct(direct_num);
};

geo_locations.load_point_direct = function()
{
    var predef_obj = document.getElementById ? document.getElementById("point_predefined") : null;

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];
    var cur_direct = cur_marker.attributes.m_direct;

    if (undefined === cur_direct) {cur_direct = false;}
    //predef_obj.checked = !cur_direct;
    var selIndex = 0;
    if (cur_direct) {selIndex = 1;}
    predef_obj.selectedIndex = selIndex;

    this.set_edit_direct(cur_direct);
};

// displaying appropriate part for text input for the POI content
geo_locations.set_edit_direct = function(direct_usage)
{
    //alert("du: " + direct_usage);
    var direct_num = 0;
    var direct_bool = false;
    if (direct_usage && (0 != direct_usage))
    {
        direct_num = 1;
        direct_bool = true;
    }

    var predef_names = ['point_image', 'point_image_width', 'point_image_height', 'point_video_type_none', 'point_video_type_youtube', 'point_video_type_vimeo', 'point_video', 'point_video_width', 'point_video_height', 'image_edit_part', 'video_edit_part'];

    var predef_count = predef_names.length;
    for (var pind = 0; pind < predef_count; pind++)
    {
        var one_predef_name = predef_names[pind];
        var one_predef_obj = document.getElementById ? document.getElementById(one_predef_name) : null;
        one_predef_obj.disabled = direct_bool;
    }

    //alert("bbb");
    if (direct_usage)
    {
        //alert("direct");
        $("#edit_html_text_message").removeClass("hidden");
        $("#edit_part_content").removeClass("hidden");
        $("#edit_plain_text_message").addClass("hidden");
        $("#edit_part_text").addClass("hidden");
        //$("#edit_part_link").addClass("hidden");
        $('#edit_tabs_all').tabs("option", "disabled", [3, 4]);
    }
    else
    {
        //alert("predefined");
        $("#edit_html_text_message").addClass("hidden");
        $("#edit_part_content").addClass("hidden");
        $("#edit_plain_text_message").removeClass("hidden");
        $("#edit_part_text").removeClass("hidden");
        //$("#edit_part_link").removeClass("hidden");
        $('#edit_tabs_all').tabs("option", "disabled", []);
    }
    //alert("ccc");

};

// setting POI's icon on edit action
geo_locations.map_edit_set_marker = function(index)
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
        }
    }
    attrs.m_type = index;

    attrs.type = marker_type;
    this.layer.redraw();

};

// preparing icon part of POI editing, initial phase
geo_locations.map_edit_prepare_markers = function()
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

    for (var cind = 0; cind < choices_count; cind++)
    {
        var cur_img_label = this.marker_src_labels[cind];
        var cur_img_icon = this.marker_src_icons[cur_img_label];
        var cur_img_path = cur_img_icon["path"];

        //choice_one = "<span class='edit_marker_one_choice'><a class=\"edit_marker_one_choice_link\" href='#' onClick=\"geo_locations.map_edit_set_marker(" + cind + "); return false;\"><img src='" + cur_img_path + "'></a></span>";
        choice_one = "<a class=\"edit_marker_one_choice_link\" href='#' onClick=\"geo_locations.map_edit_set_marker(" + cind + "); return false;\"><img class='edit_marker_one_choice' src='" + cur_img_path + "'></a>";
        choices_html += choice_one;
    }
    img_choices.innerHTML = choices_html;

};

geo_locations.set_save_state = function(state)
{
    var save_obj = document.getElementById ? document.getElementById("map_save_label") : null;
    var info_obj = document.getElementById ? document.getElementById("map_save_info") : null;

    if (state)
    {
        $(save_obj).removeClass("map_save_off");
        $(save_obj).addClass("map_save_on");
        info_obj.innerHTML = "&nbsp;to store data";
    }
    else
    {
        $(save_obj).removeClass("map_save_on");
        $(save_obj).addClass("map_save_off");
        info_obj.innerHTML = "&nbsp;no change yet";
    }

};

// loading the POI data, for the initial phase
geo_locations.map_pois_load = function(script_dir)
{
    var load_request = getHTTPObject();

    load_request.onreadystatechange = function()
    {
        if (4 == load_request.readyState)
        {
            geo_locations.got_load_data(load_request);
        }
    };

    try
    {
        if (undefined === script_dir)
        {
            script_dir = "";
        }
        var script_path = script_dir + "manage.php";

        load_request.open("GET", script_path + "?load=1&f_article_number=" + this.article_number + "&f_language_id=" + this.language_id, true);
        load_request.send(null);
    }
    catch (e)
    {
        load_request.onreadystatechange = function() {}
        load_request = null;
        return;
    }

    return false;


};

// the main action on ajax data retrieval for cities search
// it throws away all the current POI info
geo_locations.got_load_data = function (load_request)
{
    //return;
    //alert(load_request.responseText);

    var load_status = load_request.status;
    var http_status_ok = 200;

    var load_response = "";
    if (load_status == http_status_ok)
    {
        load_response = load_request.responseText;
    }

    if (load_status != http_status_ok)
    {
        load_response = "failed: " + load_status;
        alert(load_response);
        return;
    }

    var received_obj = null;
    try {
        received_obj = JSON.parse(load_response);
    }
    catch (e) {
        alert("probably logged out: " + e);
        return;
    }

    if ("200" != ("" + received_obj.status))
    {
        alert("not logged in?");
        return;
    }

    //alert("0003");
    this.select_control.destroy();
    //alert("0004");

    this.edited_point = 0;
    this.poi_rank_out = 0;

    this.poi_order_user = [];
    this.poi_markers = [];
    this.descs_count = 0;
    this.descs_count_inc = 0;
    this.poi_order_changed = false;

    //alert("0006");
    this.layer.removeFeatures(this.layer.features);
    if (this.popup)
    {
        // this pop-up removal is sometimes strange
        try {
            this.map.removePopup(this.popup);
            this.popup.destroy();
        }
        catch (e) {}
        this.popup = null;
    }
    //alert("0009");

    this.close_edit_window();

    var features_to_add = [];

    var lonlat = null;

    //alert("0010");
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
        vector.attributes.m_image_source = one_poi['image_src'];
        vector.attributes.m_image_width = one_poi['image_width'];
        vector.attributes.m_image_height = one_poi['image_height'];
        vector.attributes.m_video_id = one_poi['video_id'];
        vector.attributes.m_video_type = one_poi['video_type'];
        vector.attributes.m_video_width = one_poi['video_width'];
        vector.attributes.m_video_height = one_poi['video_height'];

        vector.attributes.m_image = "";
        vector.attributes.m_embed = "";
        this.set_image_tag(vector.attributes);
        this.set_embed_tag(vector.attributes);

        features_to_add.push(vector);

    }
    //alert("0020");

    this.layer.addFeatures(features_to_add);
    //alert("0021");

    this.descs_count = poi_count;
    this.descs_count_inc = poi_count;

    //alert("0030");
    this.select_control = new OpenLayers.Control.SelectFeature(this.layer);
    this.map.addControl(this.select_control);
    this.select_control.activate();
    //alert("0040");

    this.update_poi_descs();
    //alert("0045");

    this.set_save_state(false);
    //alert("0050");

};

geo_locations.set_save_content_on_poi = function(save_obj, poi_attrs)
{
    var poi_prop_names = {};

    poi_prop_names['title'] = "name";
    poi_prop_names['perex'] = "perex";
    poi_prop_names['content'] = "content";
    poi_prop_names['text'] = "text";
    poi_prop_names['link'] = "link";

    poi_prop_names['image_source'] = "image_src";
    poi_prop_names['image_width'] = "image_width";
    poi_prop_names['image_height'] = "image_height";

    poi_prop_names['video_id'] = "video_id";
    poi_prop_names['video_type'] = "video_type";
    poi_prop_names['video_width'] = "video_width";
    poi_prop_names['video_height'] = "video_height";

    //poi_prop_names['icon_name'] = "icon_name";

    for (one_name in poi_prop_names)
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

};

geo_locations.put_poi_into_insertions = function(storage, index)
{
    var cur_marker = this.poi_markers[index];
    var cur_attrs = this.layer.features[index].attributes;

    var cur_obj = {
        'index': cur_marker['tmp_index'],
        'longitude': cur_marker['lon'],
        'latitude': cur_marker['lat'],
        //'usage': cur_marker['usage'],
    };

    this.set_save_content_on_poi(cur_obj, cur_attrs);
    storage.push(cur_obj);
};

geo_locations.put_into_poi_locations = function(storage, index)
{
    var cur_marker = this.poi_markers[index];
    //var cur_attrs = this.layer.features[index].attributes;

    var cur_obj = {
        'id': cur_marker['loc_index'],
        'longitude': cur_marker['lon'],
        'latitude': cur_marker['lat'],
    };

    storage.push(cur_obj);
};

geo_locations.put_into_poi_contents = function(storage, index)
{
    var cur_marker = this.poi_markers[index];
    var cur_attrs = this.layer.features[index].attributes;

    var cur_obj = {
        'id': cur_marker['con_index'],
    };

    this.set_save_content_on_poi(cur_obj, cur_attrs);
/*
    var poi_prop_names = {};
    poi_prop_names['label'] = "name";
    poi_prop_names['direct'] = "direct";
    poi_prop_names['video_type'] = "video_type";
    poi_prop_names['icon_name'] = "icon_name";

    poi_prop_names['perex'] = "perex";
    poi_prop_names['text'] = "text";
    poi_prop_names['link'] = "link";
    poi_prop_names['content'] = "content";
    poi_prop_names['image_source'] = "image";
    poi_prop_names['image_width'] = "image_width";
    poi_prop_names['image_height'] = "image_height";
    poi_prop_names['video_id'] = "video";
    poi_prop_names['video_width'] = "video_width";
    poi_prop_names['video_height'] = "video_height";

    for (one_name in poi_prop_names)
    {
        var poi_property = "m_" + one_name;
        var obj_property = poi_prop_names[one_name];

        var one_value = cur_attrs[poi_property];
        if (!one_value) {one_value = "";}
        cur_obj[obj_property] = one_value;
    }
*/
    storage.push(cur_obj);

};

// saving data, on the main 'save' user action; do ajax here
geo_locations.map_save_all = function(script_dir)
{
    //alert(this.map_view_layer_name);
    var store_request = getHTTPObject();
    var cur_marker = null;

    var store_data = "";
    if (this.map_spec_changed)
    {
        var prov_label = "";
        for (var one_prov_name in this.map_view_layer_names_all)
        {
            if (this.map_view_layer_names_all[one_prov_name] == this.map_view_layer_name) {prov_label = one_prov_name;}
        }

        var store_map_obj = {
            'prov': prov_label,
            'lon': this.map_view_layer_center.lon,
            'lat': this.map_view_layer_center.lat,
            'zoom': this.map_view_layer_zoom,
            'width': this.map_art_view_width,
            'height': this.map_art_view_height
        };
        var store_map_str = Base64.encode(JSON.stringify(store_map_obj));
        //alert(JSON.stringify(store_map_obj));
        store_data += "&f_map=" + store_map_str;
    }

    if (0 < this.poi_deletion.length)
    {
        var remove_poi_str = Base64.encode(JSON.stringify(this.poi_deletion));
        store_data += "&f_remove=" + remove_poi_str;
    }

    //alert(this.poi_order_user);
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
        //alert(JSON.stringify(order_ids));
        store_data += "&f_order=" + order_poi_str;
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
        //alert(JSON.stringify(insert_poi_new_array));
        store_data += "&f_insert_new=" + insert_poi_new_str;
    }
    if (0 < update_poi_loc_array.length)
    {
        var update_poi_loc_str = Base64.encode(JSON.stringify(update_poi_loc_array));
        //alert(JSON.stringify(update_poi_loc_array));
        store_data += "&f_update_loc=" + update_poi_loc_str;
    }
    if (0 < update_poi_con_array.length)
    {
        var update_poi_con_str = Base64.encode(JSON.stringify(update_poi_con_array));
        //alert(JSON.stringify(update_poi_con_array));
        store_data += "&f_update_con=" + update_poi_con_str;
    }

    //alert("010");
    store_request.onreadystatechange = function()
    {
        if (4 == store_request.readyState)
        {
            geo_locations.got_load_data(store_request);
        }
    };

    //alert("015");
    try
    {
        if (undefined === script_dir)
        {
            script_dir = "";
        }
        var script_path = script_dir + "manage.php";

        store_request.open("POST", script_path, true);
        store_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //alert("" + "store=1&f_article_number=" + this.article_number + "&f_language_id=" + this.language_id + store_data);
        store_request.send("" + "store=1&f_article_number=" + this.article_number + "&f_language_id=" + this.language_id + store_data);
    }
    catch (e)
    {
        store_request.onreadystatechange = function() {}
        store_request = null;
        return;
    }

    //alert("020");
    return false;
/*
*/

};

/*
// storing info on a single POI (the edited one); ajax action
geo_locations.save_edit_window = function()
{

};
*/




