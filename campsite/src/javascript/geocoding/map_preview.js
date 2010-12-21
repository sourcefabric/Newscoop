// times for icon redrawing at some situations
var redraw_times = {
    time_drag_delay: 500,
    map_dragging_last: 0
};

// the POI markers are not re-drawn after some actions happen; this is a part of the fix;
var geo_hook_map_feature_redraw = function(geo_obj, xy, delay)
{
    var cur_date = new Date();
    var cur_time = cur_date.getTime();

    var time_delay = redraw_times.time_drag_delay;
    if (undefined !== delay)
    {
        time_delay = delay;
    }

    if (time_delay <= (cur_time - redraw_times.map_dragging_last))
    {
        geo_hook_map_dragged(geo_obj, xy);
        redraw_times.map_dragging_last = cur_time;
    }

};

// adding redrawing of the POI icons on map panning
//var geo_hook_map_bar_panning = function(geo_obj, ctrl, evt)
var geo_hook_map_bar_panning = function(evt, geo_param)
{
    ctrl = this;
    var geo_obj = this.map.geo_obj;

    if (undefined !== geo_param) {geo_obj = geo_param;}

    if (geo_obj)
    {
        geo_hook_map_feature_redraw(geo_obj, 0);
    }

    if (!OpenLayers.Event.isLeftClick(evt)) {
        return;
    }
    switch (ctrl.action) {
      case "panup":
        ctrl.map.pan(0, - ctrl.getSlideFactor("h"));
        break;
      case "pandown":
        ctrl.map.pan(0, ctrl.getSlideFactor("h"));
        break;
      case "panleft":
        ctrl.map.pan(- ctrl.getSlideFactor("w"), 0);
        break;
      case "panright":
        ctrl.map.pan(ctrl.getSlideFactor("w"), 0);
        break;
      case "zoomin":
        ctrl.map.zoomIn();
        break;
      case "zoomout":
        ctrl.map.zoomOut();
        break;
      case "zoomworld":
        ctrl.map.zoomToMaxExtent();
        break;
      default:;
    }

    OpenLayers.Event.stop(evt);

};

// adding redrawing of the POI icons on map panning
var geo_hook_map_dragging = function(drag_map, geo_obj, xy)
{
    drag_map.panned = true;

    drag_map.map.pan(drag_map.handler.last.x - xy.x, drag_map.handler.last.y - xy.y, {dragging: drag_map.handler.dragging, animate: false});

    geo_hook_map_feature_redraw(geo_obj, xy);

};

// adding redrawing of the POI icons on bar panning
var geo_hook_map_dragged = function(geo_obj, pixel)
{
    var new_center = geo_obj.map.center.clone();
    geo_obj.map.setCenter(new_center);

/**/
    geo_obj.select_control.destroy();
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    geo_obj.map.addControl(geo_obj.select_control);
    geo_obj.select_control.activate();
/**/
};

// to insert new POI on map click, but not on a click that closes a pop-up
var geo_hook_trigger_on_map_click = function(geo_obj, e)
{
    if (geo_obj.ignore_click) {
        geo_obj.ignore_click = false;
    }

    var lonlat = geo_obj.map.getLonLatFromViewPortPx(e.xy);

/**/
    geo_obj.select_control.destroy();
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    geo_obj.map.addControl(geo_obj.select_control);
    geo_obj.select_control.activate();
/**/

};




// needed just for click on pop-up close button
var geo_hook_on_popup_close = function(evt, geo_obj)
{

    try {
        geo_obj.select_control.unselect(geo_obj.feature);
    }
    catch (e) {}

    if (geo_obj.popup) {
        try {
            geo_obj.select_control.unselect(geo_obj.popup.feature);
        }
        catch (e) {}
    }
};

// when a feature pop-up should be removed on map event
var geo_hook_on_feature_unselect = function(evt)
{
    var feature = evt.feature;
    var geo_obj = feature.attributes.m_obj;

    if (feature.popup) {
        geo_obj.popup.feature = null;
        geo_obj.map.removePopup(feature.popup);
        feature.popup.destroy();
        feature.popup = null;
        geo_obj.popup = null;
    }

    try {
        geo_obj.select_control.unselect(geo_obj.feature);
    }
    catch (e) {}

    if (geo_obj.popup) {
        try {
            geo_obj.select_control.unselect(geo_obj.popup.feature);
        }
        catch (e) {}
    }
};

// selecting a point for popup display, directly at map
var geo_hook_on_feature_select = function(evt, feature_param)
{
    var feature = null;

    if (evt)
    {
        feature = evt.feature;
    }
    else
    {
        if ((undefined !== feature_param) && (null !== feature_param))
        {
            feature = feature_param;
        }
    }

    if (!feature) {return;}

    var attrs = feature.attributes;
    if (!attrs) {return;}

    var geo_obj = attrs.m_obj;

    if (geo_obj.popup) {
        geo_obj.select_control.unselect(geo_obj.popup.feature);
    }

    var pop_info = geo_obj.create_popup_content(feature);
    var pop_text = pop_info['inner_html'];

    geo_obj.cur_pop_rank += 1;
    geo_obj.popup = new OpenLayers.Popup.FramedCloud("featurePopup_" + geo_obj.cur_pop_rank,
        feature.geometry.getBounds().getCenterLonLat(),
        new OpenLayers.Size(geo_obj.popup_width,geo_obj.popup_height),
        pop_text,
        null, true, function(evt) {geo_hook_on_popup_close(evt, geo_obj);});

    var min_width = pop_info['min_width'];
    var min_height = pop_info['min_height'];

    geo_obj.popup.minSize = new OpenLayers.Size(min_width, min_height);

    feature.popup = geo_obj.popup;
    geo_obj.popup.feature = feature;
    geo_obj.map.addPopup(geo_obj.popup);

};

// selecting a point for popup display, from outer js calls
var geo_hook_on_map_feature_select = function(geo_object, poi_index)
{
    var feature = null;

    if (geo_object)
    {
        if ((undefined !== poi_index) && (null !== poi_index))
        {
            feature = geo_object.layer.features[poi_index];
        }
    }
    if (!feature) {return;}

    geo_hook_on_feature_select(null, feature);

};

// when a feature pop-up should be diplayed on map event

// the main object to hold geo-things
function geo_locations () {

this.something_to_save = false;

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
this.map_view_layer_providers = {};
this.map_view_layer_providers[this.map_view_layer_google] = false;
this.map_view_layer_providers[this.map_view_layer_osm] = false;

this.map_view_layer_names_all = {};
this.map_view_layer_default = "";
this.map_view_layer_name = "";
this.map_view_layer_center_ini = {};
this.map_view_layer_center = null;
this.map_view_layer_zoom = 0;
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

this.map_label_name = "";
this.map_id = 0;

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



// setting the article info
this.set_article_spec = function(params)
{
    this.article_number = parseInt(params.article_number);
    this.language_id = parseInt(params.language_id);
};

this.map_limit_width_display = 800;
this.map_limit_height_display = 500;
this.map_limit_width_view = 1200;
this.map_limit_height_view = 1200;

// setting the db based default info
this.set_map_info = function(params)
{
    this.map_view_layer_default = params["default"];
    var prov_len = params.providers.length;
    for (var pind = 0; pind < prov_len; pind++)
    {
        this.map_view_layer_providers[params.providers[pind]] = true;
    }

    this.map_view_layer_center_ini = {"longitude": params.longitude, "latitude": params.latitude};
    this.map_view_layer_zoom = parseInt(params.resolution);

    this.map_art_view_width_default = parseInt(params.width);
    this.map_art_view_height_default = parseInt(params.height);

};

// setting info on basic map parameters
this.set_map_usage = function(params)
{
    this.map_id = params["id"];
    if (0 == this.map_id) {return;}

    var longitude = params.lon;
    var latitude = params.lat;

    this.map_view_layer_center_ini = {"longitude": longitude, "latitude": latitude};
    this.map_view_layer_zoom = parseInt(params.res);

    this.map_label_name = params.name;

    this.map_art_view_width_default = parseInt(params.width);
    this.map_art_view_height_default = parseInt(params.height);

    this.map_view_layer_default = params.prov;

    if (this.map)
    {
        var layer_name = this.map_view_layer_names_all[this.map_view_layer_default];
        if (layer_name && ("" != layer_name))
        {
            this.map_view_layer_name = layer_name;
        }
        this.map_view_layer_center = new OpenLayers.LonLat(longitude, latitude).transform(
            new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject()
        );
    }

};

// setting info on available icons
this.set_icons_info = function(params)
{
    this.marker_src_base = params.webdir;
    this.marker_src_default = params["default"];
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

// setting info on popups
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

// preparing html content for a popup
this.create_popup_content = function(feature)
{
    var none_info = {'inner_html': "", 'min_width': 0, 'min_height': 0};

    if (!feature) {return none_info;}

    var attrs = feature.attributes;
    if (!attrs) {return none_info;}

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

    var with_embed = false;
    {
        if (feature.attributes.m_image)
        {
            pop_text += "<div class='popup_image'>" + feature.attributes.m_image + "</div>";
        }
        if (feature.attributes.m_embed)
        {
            pop_text += "<div class='popup_video'>" + feature.attributes.m_embed + "</div>";
            with_embed = true;
        }
    }

    if (attrs.m_direct)
    {
        var content = attrs.m_content;
        if (!content) {content = "";}
        pop_text += "<div class='popup_content'>" + content + "</div>";
    }
    else
    {
        var plain_text = feature.attributes.m_text;
        plain_text.replace(/&/gi, "&amp;");
        plain_text.replace(/>/gi, "&gt;");
        plain_text.replace(/</gi, "&lt;");
        plain_text.replace(/\r\n/gi, "</p><p>");
        plain_text.replace(/\n/gi, "</p><p>");
        plain_text.replace(/\r/gi, "</p><p>");

        pop_text += "<div class='popup_text'><p>" + plain_text + "</p></div>";
    }

    var min_width = this.popup_width;
    var min_height = this.popup_height;
    if (with_embed) {
        var min_width_embed = feature.attributes.m_embed_width + 100;
        var min_height_embed = feature.attributes.m_embed_height + 100;
        if (min_width_embed > min_width) {min_width = min_width_embed;}
        if (min_height_embed > min_height) {min_height = min_height_embed;}
    }

    return {'inner_html': pop_text, 'min_width': min_width, 'min_height': min_height};
};

// showing the current initial reader view
this.map_showview = function()
{
    var map_names = this.map.getLayersByName(this.map_view_layer_name);
    if (0 < map_names.length)
    {
        this.map.setBaseLayer(map_names[0]);
    }
    this.map.setCenter(this.map_view_layer_center, this.map_view_layer_zoom);
};

// setting image html tag of a poi
this.set_image_tag = function(attrs)
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

// setting video html tag of a poi
this.set_embed_tag = function(attrs)
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

        var vid_poster = "";
        if ("flv" == vid_type)
        {
            if (vid_id.match(/\.flv$/))
            {
                vid_poster = vid_id.replace(/\.flv$/, ".png");
            }
            else
            {
                vid_poster = vid_id + ".png";
                vid_id = vid_id + ".flv";
            }
        }

        var vid_value = vid_src.replace(/%%id%%/g, vid_id);
        var vid_value = vid_value.replace(/%%ps%%/g, vid_poster);

        var vid_height = attrs.m_video_height;
        if ((!vid_height) || ("" == vid_height)) {vid_height = vid_define["height"];}
        var vid_width = attrs.m_video_width;
        if ((!vid_width) || ("" == vid_width)) {vid_width = vid_define["width"];}

        var vid_path = vid_define["path"];
        if (!vid_path) {vid_path = "";}

        vid_value = vid_value.replace(/%%h%%/g, vid_height);
        vid_value = vid_value.replace(/%%w%%/g, vid_width);

        var emptify_server_part = false;
        var full_url_starts = ["http://", "https://", "ftp://", "ftps://"];
        var full_url_starts_count = full_url_starts.length;
        for (var uind = 0; uind < full_url_starts_count; uind++)
        {
            var one_url_start = full_url_starts[uind];
            if (one_url_start == vid_id.substring(0, one_url_start.length)) {emptify_server_part = true; break;}
        }
        if (emptify_server_part)
        {
            vid_path = "";
        }

        vid_value = vid_value.replace(/%%path%%/g, vid_path);

        attrs.m_embed = vid_value;
        attrs.m_embed_height = parseInt(vid_height);
        attrs.m_embed_width = parseInt(vid_width);

    }

};

// the main action on data retrieval
this.got_load_data = function (load_data)
{
    load_response = load_data;

    var received_obj = null;
    try {
        received_obj = JSON.parse(load_response);
    }
    catch (e) {
        return;
    }

    if (this.select_control)
    {
        this.select_control.destroy();
    }

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

    var features_to_add = [];

    var lonlat = null;

    this.set_map_usage(received_obj.map);

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
        this.set_image_tag(vector.attributes);
        this.set_embed_tag(vector.attributes);

        vector.attributes.m_obj = this;

        features_to_add.push(vector);

    }

    this.layer.addFeatures(features_to_add);

    this.descs_count = poi_count;
    this.descs_count_inc = poi_count;

    this.select_control = new OpenLayers.Control.SelectFeature(this.layer);
    this.map.addControl(this.select_control);
    this.select_control.activate();

};


};

// map related initialization
var geo_main_openlayers_init = function(geo_obj, map_div_name)
{
    OpenLayers.Control.Hover = OpenLayers.Class(OpenLayers.Control, {
        defaultHandlerOptions: {
            'delay': 200,
            'pixelTolerance': 2
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
            var poi_hover = geo_obj.layer.getFeatureFromEvent(evt);
            if (poi_hover) {
                if (null !== poi_hover.attributes.m_rank) {
                    geo_obj.poi_rank_out = poi_hover.attributes.m_rank;
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
            'displayProjection': new OpenLayers.Projection("EPSG:900913")
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
                    'click': function(e) {geo_hook_trigger_on_map_click(geo_obj, e);}
                }, this.handlerOptions
            );
        }

    });

    geo_obj.pzb_ctrl = new OpenLayers.Control.PanZoomBar();

    var pzb_ctrl = new OpenLayers.Control.PanZoomBar();
    pzb_ctrl.geo_obj = geo_obj;

    pzb_ctrl.buttonDown = geo_hook_map_bar_panning;

    geo_obj.map = new OpenLayers.Map(map_div_name, {
        controls: [
            new OpenLayers.Control.Navigation(),
            pzb_ctrl,
            new OpenLayers.Control.ScaleLine()
        ],
        numZoomLevels: 20
    });

    geo_obj.map.geo_obj = geo_obj;

    var map_provs = [];
    var map_gsm = null;
    var map_osm = null;

    geo_obj.map_view_layer_names_all = {};

    var google_label = geo_obj.map_view_layer_google;
    var osm_label = geo_obj.map_view_layer_osm;
    var mqm_label = "MapQuest";

    if (geo_obj.map_view_layer_providers[google_label])
    {
        // google map v3
        map_gsm = new OpenLayers.Layer.Google(
            "Google Streets",
            {
                numZoomLevels: 20, 'sphericalMercator': true, 'repositionMapElements': function () {
                    google.maps.event.trigger(this.mapObject, "resize");
                    var div = this.mapObject.getDiv().firstChild;
                    if (!div || div.childNodes.length < 3) {
                        this.repositionTimer = window.setTimeout(OpenLayers.Function.bind(this.repositionMapElements, this), 250);
                        return false;
                    }

                    var cache = OpenLayers.Layer.Google.cache[this.map.id];
                    var container = this.map.viewPortDiv;

                    var termsOfUse = div.lastChild;
                    container.appendChild(termsOfUse);
                    termsOfUse.style.zIndex = "1100";
                    termsOfUse.style.bottom = "";
                    termsOfUse.className = "olLayerGoogleCopyright olLayerGoogleV3";
                    //termsOfUse.style.display = "";
                    //cache.termsOfUse = termsOfUse;

                    var poweredBy = div.lastChild;
                    container.appendChild(poweredBy);
                    poweredBy.style.zIndex = "1100";
                    poweredBy.style.bottom = "";
                    poweredBy.className = "olLayerGooglePoweredBy olLayerGoogleV3 gmnoprint";
                    poweredBy.style.display = "";
                    cache.poweredBy = poweredBy;

                    this.setGMapVisibility(this.visibility);
                }
            }
        );
        geo_obj.map_view_layer_names_all[google_label] = map_gsm.name;
        if (google_label == geo_obj.map_view_layer_default)
        {
            map_provs.push(map_gsm);
        }
    }

    if (geo_obj.map_view_layer_providers[osm_label])
    {
        // openstreetmap
        var map_osm = new OpenLayers.Layer.OSM();
        geo_obj.map_view_layer_names_all[osm_label] = map_osm.name;
        if (osm_label == geo_obj.map_view_layer_default)
        {
            map_provs.push(map_osm);
        }
    }

    var map_mqm = null;

try {
    {
        map_mqm = new OpenLayers.Layer.MapQuest();
        //if (mqm_label == geo_obj.map_view_layer_default)
        {
            map_provs.push(map_mqm);
        }
    }
} catch (e) {alert(e);}

    if (map_gsm && (google_label != geo_obj.map_view_layer_default))
    {
        map_provs.push(map_gsm);
    }
    if (map_osm && (osm_label != geo_obj.map_view_layer_default))
    {
        map_provs.push(map_osm);
    }

    map_provs.push(map_mqm);

    geo_obj.map.addLayers(map_provs);
    geo_obj.map.addControl(new OpenLayers.Control.Attribution());

    // an initial center point, set via parameters
    var cen_ini_longitude = geo_obj.map_view_layer_center_ini["longitude"];
    var cen_ini_latitude = geo_obj.map_view_layer_center_ini["latitude"];
    var lonLat_cen = new OpenLayers.LonLat(cen_ini_longitude, cen_ini_latitude)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            geo_obj.map.getProjectionObject() // to Spherical Mercator Projection
          );
    var zoom = geo_obj.map_view_layer_zoom;

    var style_map = new OpenLayers.StyleMap({
                graphicZIndex: 10,
                cursor: "pointer"
    });

    var lookup = {};
    var labels_len = geo_obj.marker_src_labels.length;
    for (var lind = 0; lind < labels_len; lind++)
    {
        var cur_label = geo_obj.marker_src_labels[lind];
        var cur_icon = geo_obj.marker_src_icons[cur_label];
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
    geo_obj.layer = new OpenLayers.Layer.Vector(
        "POI markers",
        {
            styleMap: style_map,
            isBaseLayer: false,
            rendererOptions: {yOrdering: true}
        }
    );
    geo_obj.map.addLayer(geo_obj.layer);

    // setting map center
    geo_obj.map.setCenter (lonLat_cen, zoom);

    geo_obj.map_view_layer_name = geo_obj.map.layers[0].name;
    geo_obj.map_view_layer_center = geo_obj.map.getCenter();
    geo_obj.map_view_layer_zoom = geo_obj.map.getZoom();

    // registering for click events
    var click = new OpenLayers.Control.Click();
    geo_obj.map.addControl(click);
    click.activate();

    var hover = new OpenLayers.Control.Hover();
    geo_obj.map.addControl(hover);
    hover.activate();

    var cur_date = new Date();
    redraw_times.map_dragging_last = cur_date.getTime();

    var drag_map = new OpenLayers.Control.DragPan([map_gsm, map_osm]);
    drag_map.panMapDone = function(pixel) {geo_hook_map_dragged(geo_obj, pixel)};
    drag_map.panMap = function(xy) {geo_hook_map_dragging(drag_map, geo_obj, xy)};
    geo_obj.map.addControl(drag_map);
    drag_map.activate();

    geo_obj.layer.events.on({
        'featureselected': geo_hook_on_feature_select,
        'featureunselected': geo_hook_on_feature_unselect
    });

};

// the entry initialization point
var geo_main_selecting_locations = function (geo_obj, geocodingdir, div_name, descs_name, names_show, names_hide, editing)
{
    var map_canvas = document.getElementById ? document.getElementById(div_name) : null;
    geo_obj.descs_elm = document.getElementById ? document.getElementById(descs_name) : null;
    geo_obj.descs_elm_name = descs_name;

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
    if (geo_obj.map_shown)
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

    if (geo_obj.map_shown) {
        geo_obj.map_shown = false;
        return;
    }

    geo_obj.map_shown = true;

    useSystemParameters();

    geo_main_openlayers_init(geo_obj, div_name);

};

