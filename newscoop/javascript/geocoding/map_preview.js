
// the main object to hold geo-things
function geo_locations () {

this.something_to_save = false;
this.auto_focus = true;
this.auto_focus_max_zoom = true;
this.auto_focus_border = 100;

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
this.map_view_layer_mapquest = "mapquest";
this.map_view_layer_osm = "osm";
this.map_view_layer_providers = {};
this.map_view_layer_providers[this.map_view_layer_google] = false;
this.map_view_layer_providers[this.map_view_layer_mapquest] = false;
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

this.set_auto_focus = function(params)
{
    var param_names = {"auto_focus":"auto_focus", "max_zoom":"auto_focus_max_zoom", "border":"auto_focus_border"};

    for (var param_key in param_names)
    {
        if (undefined !== params[param_key])
        {
            var one_name = param_names[param_key];
            this[one_name] = params[param_key];
        }
    }
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
        var pop_title = "" + feature.attributes.m_title;
        pop_title = pop_title.replace(/&/gi, "&amp;");
        pop_title = pop_title.replace(/>/gi, "&gt;");
        pop_title = pop_title.replace(/</gi, "&lt;");

        pop_text += "<div class='popup_title'>";
        if (0 < pop_link.length) {
            pop_text += "<a href=\"" + pop_link + "\" target=\"_blank\">";
        }
        pop_text += pop_title;
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
        plain_text = plain_text.replace(/&/gi, "&amp;");
        plain_text = plain_text.replace(/>/gi, "&gt;");
        plain_text = plain_text.replace(/</gi, "&lt;");
        plain_text = plain_text.replace(/\r\n/gi, "</p><p>");
        plain_text = plain_text.replace(/\n/gi, "</p><p>");
        plain_text = plain_text.replace(/\r/gi, "</p><p>");

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

    OpenLayers.HooksLocal.map_check_popup(this);

};

// the main action on data retrieval
this.got_load_data = function (load_data, is_obj) {
    load_response = load_data;

    var received_obj = null;
    if (is_obj)
    {
        received_obj = load_data;
    }
    else
    {
        try {
            received_obj = JSON.parse(load_response);
        }
        catch (e) {
            return;
        }
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

    var poi_lon_min = 10e100;
    var poi_lon_max = -10e100;
    var poi_lat_min = 10e100;
    var poi_lat_max = -10e100;
    var pos_lon_min = 10e100;
    var pos_lon_max = -10e100;
    var pos_lat_min = 10e100;
    var pos_lat_max = -10e100;

    var toi_lon_min = 10e100;
    var toi_lon_max = -10e100;
    var toi_lat_min = 10e100;
    var toi_lat_max = -10e100;
    var tos_lon_min = 10e100;
    var tos_lon_max = -10e100;
    var tos_lat_min = 10e100;
    var tos_lat_max = -10e100;
    var use_zone_shift = false;

    var shifts = [0, -360, 360];
    //var shifts = [0];
    var shift_count = shifts.length;
    for (var sind = 0; sind < shift_count; sind++) {

    var poi_count = received_obj.pois.length;
    for (var pind = 0; pind < poi_count; pind++)
    {
        var one_marker = {};
        var one_poi = received_obj.pois[pind];

        var longitude_use = parseFloat(one_poi.longitude) + shifts[sind];
        var latitude_use = parseFloat(one_poi.latitude);

        var longitude_shift = (0 <= longitude_use) ? longitude_use : (longitude_use + 360);


        lonlat = new OpenLayers.LonLat(longitude_use, latitude_use).transform(
            new OpenLayers.Projection("EPSG:4326"),
            this.map.getProjectionObject()
        );

        lonlat_shift = new OpenLayers.LonLat(longitude_shift, latitude_use).transform(
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

        one_marker['duplicate'] = true;
        one_marker['original'] = pind;
        one_marker["lon_shift"] = shifts[sind];
        if ((0 == sind) && (!disabled))
        {
            one_marker['duplicate'] = false;

            if (lonlat.lon < poi_lon_min) {poi_lon_min = lonlat.lon; pos_lon_min = longitude_use;}
            if (lonlat.lon > poi_lon_max) {poi_lon_max = lonlat.lon; pos_lon_max = longitude_use;}
            if (lonlat.lat < poi_lat_min) {poi_lat_min = lonlat.lat; pos_lat_min = latitude_use;}
            if (lonlat.lat > poi_lat_max) {poi_lat_max = lonlat.lat; pos_lat_max = latitude_use;}

            if (lonlat_shift.lon < toi_lon_min) {toi_lon_min = lonlat_shift.lon; tos_lon_min = longitude_shift;}
            if (lonlat_shift.lon > toi_lon_max) {toi_lon_max = lonlat_shift.lon; tos_lon_max = longitude_shift;}
            if (lonlat_shift.lat < toi_lat_min) {toi_lat_min = lonlat_shift.lat; tos_lat_min = latitude_use;}
            if (lonlat_shift.lat > toi_lat_max) {toi_lat_max = lonlat_shift.lat; tos_lat_max = latitude_use;}
        }

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
        GeoPopups.set_image_tag(vector.attributes);
        GeoPopups.set_embed_tag(vector.attributes);

        vector.attributes.m_obj = this;
        vector.attributes.m_count = poi_count;
        vector.attributes.m_duplicate = one_marker["duplicate"];
        vector.attributes.m_original = one_marker["original"];
        vector.attributes.m_shift = one_marker["lon_shift"];
        vector.attributes.m_maplon = one_marker['map_lon'];
        vector.attributes.m_maplat = one_marker['map_lat'];

        features_to_add.push(vector);

    }
    }

    this.layer.addFeatures(features_to_add);

    this.descs_count = poi_count;
    this.descs_count_inc = poi_count;

    if (this.auto_focus && (0 < poi_count))
    {
        if ((poi_lon_max - poi_lon_min) > (toi_lon_max - toi_lon_min))
        {
            poi_lon_min = toi_lon_min;
            poi_lon_max = toi_lon_max;
            poi_lat_min = toi_lat_min;
            poi_lat_max = toi_lat_max;
            pos_lon_min = tos_lon_min;
            pos_lon_max = tos_lon_max;
            pos_lat_min = tos_lat_min;
            pos_lat_max = pos_lat_max;
        }

        var poi_cen_lon = (poi_lon_min + poi_lon_max) / 2.0;
        var poi_cen_lat = (poi_lat_min + poi_lat_max) / 2.0;
        var poi_cen_lonlat = new OpenLayers.LonLat(poi_cen_lon, poi_cen_lat);
        var poi_corner_tl = new OpenLayers.LonLat(poi_lon_min, poi_lat_max);
        var poi_corner_bl = new OpenLayers.LonLat(poi_lon_min, poi_lat_min);
        var poi_corner_tr = new OpenLayers.LonLat(poi_lon_max, poi_lat_max);
        var poi_corner_br = new OpenLayers.LonLat(poi_lon_max, poi_lat_min);
        var poi_corners = [poi_corner_tl, poi_corner_bl, poi_corner_tr, poi_corner_br];
        var poi_corner_count = poi_corners.length;

        var poi_lon_span = poi_lon_max - poi_lon_min;
        var poi_lat_span = poi_lat_max - poi_lat_min;

        var zoom_min = 0;
        var zoom_ini = this.auto_focus_max_zoom;
        var zoom_use = 0;

        var view_box = null;
        var pois_included = true;
        var cind = 0;

        var zoom_out = false;

        for (var zind = zoom_ini; zind > zoom_min; zind--)
        {
            view_box = this.map.calculateBounds(poi_cen_lonlat, this.map.getResolutionForZoom(zind));
            pois_included = true;
            for (cind = 0; cind < poi_corner_count; cind++)
            {
                if (!view_box.containsLonLat(poi_corners[cind], false)) {pois_included = false; break;}
            }
            if (pois_included)
            {
                zoom_use = zind;

                var box_width = view_box.right - view_box.left;
                var box_height = view_box.top - view_box.bottom;
                var outside_lon = (1.0 - (poi_lon_span / box_width)) / 2.0;
                var outside_lat = (1.0 - (poi_lat_span / box_height)) / 2.0;
                var div_size = this.map.getCurrentSize();
                outside_lon *= div_size.w;
                outside_lat *= div_size.h;

                if (outside_lon < this.auto_focus_border) {zoom_out = true;}
                if (outside_lat < this.auto_focus_border) {zoom_out = true;}

                break;
            }
        }

        if (zoom_out && (1 < poi_count))
        {
            if (0 < zoom_use) {zoom_use -= 1;}
        }

        received_obj.map.lon = (pos_lon_min + pos_lon_max) / 2.0;
        if (180 < received_obj.map.lon) {received_obj.map.lon -= 360;}
        if (-180 > received_obj.map.lon) {received_obj.map.lon += 360;}
        received_obj.map.lat = (pos_lat_min + pos_lat_max) / 2.0;
        received_obj.map.res = "" + zoom_use;

    }

    this.set_map_usage(received_obj.map);
    this.map_showview();

    this.select_control = new OpenLayers.Control.SelectFeature(this.layer);
    this.map.addControl(this.select_control);
    this.select_control.activate();

};

this.main_openlayers_init = function(map_div_name) {

    var pzb_ctrl = null;
    var pzb_with_bar = false;

    if (360 <= this.map_art_view_height_default)
    {
        pzb_ctrl = new OpenLayers.Control.PanZoomBarMod();
        pzb_with_bar = true;
    }
    else
    {
        pzb_ctrl = new OpenLayers.Control.PanZoomMod();
    }
    pzb_ctrl.geo_obj = this;

    this.map = new OpenLayers.Map(map_div_name, {
        controls: [
            new OpenLayers.Control.Navigation(),
            pzb_ctrl,
            //new OpenLayers.Control.Permalink('permalink'),
            //new OpenLayers.Control.MousePosition(),
            //new OpenLayers.Control.OverviewMap(),
            new OpenLayers.Control.ScaleLine()
        ],
        numZoomLevels: 20
    });

    this.map.geo_obj = this;
    this.map.geo_obj.obj_name = null;

    var map_provs = [];
    var map_gsm = null;
    var map_mqm = null;
    var map_osm = null;

    this.map_view_layer_names_all = {};

    var google_label = this.map_view_layer_google;
    var mqm_label = this.map_view_layer_mapquest;
    var osm_label = this.map_view_layer_osm;

    if (this.map_view_layer_providers[google_label])
    {
        // google map v3
        map_gsm = new OpenLayers.Layer.GoogleMod(
            "Google Streets",
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
            "MapQuest Map"
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
        map_osm = new OpenLayers.Layer.OSMMod();
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
                graphicZIndex: 10,
                cursor: "pointer"
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
        "POI markers",
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
    this.map_view_layer_center = this.map.getCenter();
    this.map_view_layer_zoom = this.map.getZoom();

    // registering for click events
    var click = new OpenLayers.Control.Click();
    this.map.addControl(click);
    click.setTriggerAction(function(evt, map) {
        OpenLayers.HooksLocal.nothing_on_map_click(evt, map);
    });
    click.activate();

    var hover = new OpenLayers.Control.Hover();
    hover.setTriggerAction(function(evt, map) {
        var poi_hover = map.geo_obj.layer.getFeatureFromEvent(evt);
        if (poi_hover) {
            if (null !== poi_hover.attributes.m_rank) {
                map.geo_obj.poi_rank_out = poi_hover.attributes.m_rank;
            }
        }
    });
    this.map.addControl(hover);
    hover.activate();

    var cur_date = new Date();
    OpenLayers.HooksLocal.redraw_times.map_dragging_last = cur_date.getTime();

    var drag_map = new OpenLayers.Control.DragPanMod([map_gsm, map_mqm, map_osm]);
    this.map.addControl(drag_map);
    drag_map.activate();

    var geo_obj = this;
    this.layer.events.on({
        'featureselected': function(evt) {OpenLayers.HooksPopups.on_feature_select(evt, geo_obj);},
        'featureunselected': function(evt) {OpenLayers.HooksPopups.on_feature_unselect(evt, geo_obj);}
    });

};

};

