var redraw_times = {
    time_drag_delay: 500,
    map_dragging_last: 0,
};

// the POI markers are not re-drawn after some actions happen; this is a part of the fix;
var geo_hook_map_feature_redraw = function(geo_obj, xy, delay)
{
    //alert("" + geo_obj + " " + xy + " " + delay);
    var cur_date = new Date();
    var cur_time = cur_date.getTime();

    var time_delay = redraw_times.time_drag_delay;
    if (undefined !== delay)
    {
        time_delay = delay;
    }
    //alert(1);

    if (time_delay <= (cur_time - redraw_times.map_dragging_last))
    {
        geo_hook_map_dragged(geo_obj, xy);
        redraw_times.map_dragging_last = cur_time;
        //alert(12);
    }
    //alert(2);

};

// adding redrawing of the POI icons on map panning
//var geo_hook_map_bar_panning = function(geo_obj, ctrl, evt)
var geo_hook_map_bar_panning = function(evt, geo_param)
{
    //for (pzb_part in this) {
    //    alert("pzb_part " + pzb_part + " is " + this[pzb_part]);
    //}

    ctrl = this;
    var geo_obj = this.map.geo_obj;
    //alert(" t: " + this.action + " c: " + ctrl.action + " g: " + geo_obj);

    if (undefined !== geo_param) {geo_obj = geo_param;}

    //alert(1);
    if (geo_obj)
    {
        geo_hook_map_feature_redraw(geo_obj, 0);
    }
    //alert(" t: " + this.action + " g: " + geo_obj.action + " c: " + ctrl.action + " e: " + evt.action);

    if (!OpenLayers.Event.isLeftClick(evt)) {
        //alert(3);
        return;
    }
    //alert(4);
    switch (ctrl.action) {
      case "panup":
        ctrl.map.pan(0, - ctrl.getSlideFactor("h"));
        //alert("5a");
        break;
      case "pandown":
        ctrl.map.pan(0, ctrl.getSlideFactor("h"));
        //alert("5b");
        break;
      case "panleft":
        ctrl.map.pan(- ctrl.getSlideFactor("w"), 0);
        //alert("5c");
        break;
      case "panright":
        ctrl.map.pan(ctrl.getSlideFactor("w"), 0);
        //alert("5d");
        break;
      case "zoomin":
        ctrl.map.zoomIn();
        //alert("5e");
        break;
      case "zoomout":
        ctrl.map.zoomOut();
        //alert("5f");
        break;
      case "zoomworld":
        ctrl.map.zoomToMaxExtent();
        //alert("5g");
        break;
      default:;
    }

    //alert("6");
    OpenLayers.Event.stop(evt);
    //alert("7");

};

// adding redrawing of the POI icons on map panning
var geo_hook_map_dragging = function(drag_map, geo_obj, xy)
{
    //alert("123: " + geo_obj + " " + xy);
    //this.panned = true;
    drag_map.panned = true;

    //alert("123444: " + this.map + " " + this.map.pan);
    //alert("123444: " + this.map);
    //this.map.pan(this.handler.last.x - xy.x, this.handler.last.y - xy.y, {dragging: this.handler.dragging, animate: false});
    drag_map.map.pan(drag_map.handler.last.x - xy.x, drag_map.handler.last.y - xy.y, {dragging: drag_map.handler.dragging, animate: false});

    //alert(123555);
    geo_hook_map_feature_redraw(geo_obj, xy);
    //alert(123666);

};

// adding redrawing of the POI icons on bar panning
var geo_hook_map_dragged = function(geo_obj, pixel)
{
    //alert(456);
    //return;
    var new_center = geo_obj.map.center.clone();
    geo_obj.map.setCenter(new_center);

    geo_obj.select_control.destroy();
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    geo_obj.map.addControl(geo_obj.select_control);
    geo_obj.select_control.activate();
};

// to insert new POI on map click, but not on a click that closes a pop-up
var geo_hook_trigger_on_map_click = function(geo_obj, e)
{
    //return;
    if (geo_obj.ignore_click) {
        geo_obj.ignore_click = false;
        //return;
    }

    var lonlat = geo_obj.map.getLonLatFromViewPortPx(e.xy);
    //alert("" + e.xy + "\n" + lonlat);


    //var pixel = this.map.getViewPortPxFromLonLat(lonlat);
    var pixel = e.xy;

    var feature = geo_obj.layer.features[0];
    feature.move(pixel);
    //if (this.popup && (feature == this.popup.feature)) {
    //    this.popup.moveTo(pixel);
    //}

/*
    geo_obj.select_control.destroy();
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    geo_obj.map.addControl(geo_obj.select_control);
    geo_obj.select_control.activate();
*/

    //alert(123);

    var attrs = feature.attributes;
    attrs['map_lon'] = lonlat.lon;
    attrs['map_lat'] = lonlat.lat;


    geo_hook_set_search_corners(feature);
};







// taking POI-mouse offset on the start of a POI dragging
var geo_hook_poi_dragg_start = function(feature, pixel)
{
    var attrs = feature.attributes;
    var geo_obj = attrs.geo_obj;

    //geo_obj.poi_drag_offset = null;

/*
    if ((undefined === feature.attributes) || (undefined === feature.attributes.m_rank))
    {
      return;
    }

    var index = feature.attributes.m_rank;
    var cur_poi_info = geo_locations.poi_markers[index];
*/

    var lonlat = geo_obj.map.getLonLatFromViewPortPx(pixel);

    //cur_poi_info['map_lon_offset'] = lonlat.lon - cur_poi_info['map_lon'];
    //cur_poi_info['map_lat_offset'] = lonlat.lat - cur_poi_info['map_lat'];
    attrs['map_lon_offset'] = lonlat.lon - attrs['map_lon'];
    attrs['map_lat_offset'] = lonlat.lat - attrs['map_lat'];

};

// updating info on POI after it was dragged
var geo_hook_poi_dragged = function(feature, pixel)
{
    var attrs = feature.attributes;
    var geo_obj = attrs.geo_obj;

/*
    if ((undefined === feature.attributes) || (undefined === feature.attributes.m_rank))
    {
      return;
    }

    geo_locations.set_save_state(true);

    var index = feature.attributes.m_rank;
    var cur_poi_info = geo_locations.poi_markers[index];
*/

    var lonlat = geo_obj.map.getLonLatFromViewPortPx(pixel);

    lonlat.lon -= attrs['map_lon_offset'];
    lonlat.lat -= attrs['map_lat_offset'];

    attrs['map_lon'] = lonlat.lon;
    attrs['map_lat'] = lonlat.lat;

/*
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
*/

    //geo_locations.update_poi_descs(index);

    // to move the POI's pop-up too, if it is displayed
    //if (geo_locations.popup && (feature == geo_locations.popup.feature)) {
    //    geo_locations.popup.moveTo(pixel);
    //}

    geo_hook_set_search_corners(feature);

};


var geo_hook_set_search_corners = function(feature)
{
    //alert("001");
        var attrs = feature.attributes;
        var geo_obj = attrs.geo_obj;

        var lonlat_search = new OpenLayers.LonLat(attrs["map_lon"], attrs["map_lat"]);

        var cen_px = geo_obj.map.getViewPortPxFromLonLat(lonlat_search.clone());
    //alert("010");
        var topleft_px = cen_px.clone();
        topleft_px.x -= 100;
        topleft_px.y -= 75;
        var bottomright_px = cen_px.clone();
        bottomright_px.x += 100;
        bottomright_px.y += 75;
    //alert("020");
        var topleft_loc = geo_obj.map.getLonLatFromViewPortPx(topleft_px).transform(
            geo_obj.map.getProjectionObject(), // to Spherical Mercator Projection
            new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
        );
    //alert("030");

        var bottomright_loc = geo_obj.map.getLonLatFromViewPortPx(bottomright_px).transform(
            geo_obj.map.getProjectionObject(), // to Spherical Mercator Projection
            new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
        );

    //alert("040");
        attrs['top_left_lon'] = topleft_loc.lon;
        attrs['top_left_lat'] = topleft_loc.lat;
        attrs['bottom_right_lon'] = bottomright_loc.lon;
        attrs['bottom_right_lat'] = bottomright_loc.lat;

    //alert("050");
        //alert("" + attrs['top_left_lon'] + " x " + attrs['top_left_lat'] + "\n    ===> \n" + attrs['bottom_right_lon'] + " x " + attrs['bottom_right_lat']);
    //alert("060");

        geo_obj.fill_bbox_divs();
};




/*

// needed just for click on pop-up close button
var geo_hook_on_popup_close = function(evt, geo_obj)
{
    //alert("1opc: " + geo_obj);
    //var feature = evt.feature;
    //alert("2opc: " + feature);
    //var geo_obj = feature.attributes.m_obj;
    //return;
    //geo_obj.ignore_click = true;
    //alert("5opc");

    try {
        //alert("6opc");
        geo_obj.select_control.unselect(geo_obj.feature);
    }
    catch (e) {}
    //alert("7opc");

    if (geo_obj.popup) {
        try {
            //alert("9opc");
            geo_obj.select_control.unselect(geo_obj.popup.feature);
        }
        catch (e) {}
    }
    //alert(1234);
};

// when a feature pop-up should be removed on map event
var geo_hook_on_feature_unselect = function(evt)
{
    //alert("1ofu");
    //return;
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

var geo_hook_on_feature_select = function(evt, feature_param)
{
    //return;
    //alert("evt: " + evt + "\n" + "feature: " + feature_param);
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

    //alert("0005");
    if (geo_obj.popup) {
        geo_obj.select_control.unselect(geo_obj.popup.feature);
    }

    //alert("101a: " + geo_obj.create_popup_content);
    var pop_info = geo_obj.create_popup_content(feature);
    //var pop_info = {"inner_html": "asdf", "min_width": 100, "min_height": 100};
    //alert("101b");
    var pop_text = pop_info['inner_html'];

    //alert("102: " + geo_obj.geo_hook_on_popup_close);
    geo_obj.cur_pop_rank += 1;
    geo_obj.popup = new OpenLayers.Popup.FramedCloud("featurePopup_" + geo_obj.cur_pop_rank,
        feature.geometry.getBounds().getCenterLonLat(),
        new OpenLayers.Size(geo_obj.popup_width,geo_obj.popup_height),
        pop_text,
        null, true, function(evt) {geo_hook_on_popup_close(evt, geo_obj);});

    //alert("106");
    var min_width = pop_info['min_width'];
    var min_height = pop_info['min_height'];

    geo_obj.popup.minSize = new OpenLayers.Size(min_width, min_height);

    feature.popup = geo_obj.popup;
    geo_obj.popup.feature = feature;
    geo_obj.map.addPopup(geo_obj.popup);

};

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
*/

// when a feature pop-up should be diplayed on map event



//alert("a 001");
// the main object to hold geo-things
function geo_locations () {
//alert("a 002");
//return;

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
//this.marker_src_names = [];
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
//this.popup_video_default = "";
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
// need to update drawing, but not to do it too frequently
//this.map_dragging_last = null;
//this.time_drag_delay = 500;

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





/*
// setting the article info
this.set_article_spec = function(params)
{
    this.article_number = parseInt(params.article_number);
    this.language_id = parseInt(params.language_id);
};
*/


this.bbox_divs = null;

this.set_bbox_divs = function (params)
{
//    $bbox_divs = array("tl_lon" => 'top_left_longitude', "tl_lat" => 'top_left_latitude', "br_lon" => 'bottom_right_longitude', "br_lat" => 'bottom_right_latitude')
    if (!params) {return;}

    var tl_lon_name = params["tl_lon"];
    var tl_lat_name = params["tl_lat"];
    var br_lon_name = params["br_lon"];
    var br_lat_name = params["br_lat"];

    if (!tl_lon_name) {return;}
    if (!tl_lat_name) {return;}
    if (!br_lon_name) {return;}
    if (!br_lat_name) {return;}

    var tl_lon_obj = document.getElementById ? document.getElementById(tl_lon_name) : null;
    var tl_lat_obj = document.getElementById ? document.getElementById(tl_lat_name) : null;
    var br_lon_obj = document.getElementById ? document.getElementById(br_lon_name) : null;
    var br_lat_obj = document.getElementById ? document.getElementById(br_lat_name) : null;

    if (!tl_lon_obj) {return;}
    if (!tl_lat_obj) {return;}
    if (!br_lon_obj) {return;}
    if (!br_lat_obj) {return;}

    this.bbox_divs = {
        'tl_lon': tl_lon_obj,
        'tl_lat': tl_lat_obj,
        'br_lon': br_lon_obj,
        'br_lat': br_lat_obj,
    };

};

this.fill_bbox_divs = function ()
{
    //alert(this.bbox_divs)
    if (!this.bbox_divs) {return;}

    var tl_vals = this.get_top_left();
    //for (key in tl_vals) {
    //    alert("" + key + " " + tl_vals[key]);
    //}
    var br_vals = this.get_bottom_right();

    this.bbox_divs['tl_lon'].innerHTML = tl_vals['longitude'];
    this.bbox_divs['tl_lat'].innerHTML = tl_vals['latitude'];
    this.bbox_divs['br_lon'].innerHTML = br_vals['longitude'];
    this.bbox_divs['br_lat'].innerHTML = br_vals['latitude'];

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

/*
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
*/

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
            "height_off": parseFloat(cur_icon["height_off"]),
        };
    }

};

/*
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
            "path": cur_video["path"],
        };
    }

};
*/


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

// showing the current initial reader view
this.map_showview = function()
{
    var map_names = this.map.getLayersByName(this.map_view_layer_name);
    if (0 < map_names.length)
    {
        this.map.setBaseLayer(map_names[0]);
    }
    this.map.setCenter(this.map_view_layer_center, this.map_view_layer_zoom);

    var feature = this.layer.features[0];

    var lonlat = this.map.getCenter();
    var pixel = this.map.getViewPortPxFromLonLat(lonlat);
    feature.move(pixel);

    var attrs = feature.attributes;
    attrs['map_lon'] = lonlat.lon;
    attrs['map_lat'] = lonlat.lat;

    geo_hook_set_search_corners(feature);

};


this.get_top_left = function ()
{
    //alert("001gtl");
    var top_left = {'longitude': 0, 'latitude': 0};

    var feature = this.layer.features[0];
    //alert("002gtl: " + feature);
    if (!feature) {return top_left;}

    var attrs = feature.attributes;
    //alert("003gtl: " + attrs);
    if (!attrs) {return top_left;}

    //alert("004gtl");
    top_left['longitude'] = attrs['top_left_lon'];
    top_left['latitude'] = attrs['top_left_lat'];

    return top_left;
};

this.get_bottom_right = function ()
{
    var bottom_right = {'longitude': 0, 'latitude': 0};

    var feature = this.layer.features[0];
    if (!feature) {return bottom_right;}

    var attrs = feature.attributes;
    if (!attrs) {return bottom_right;}

    bottom_right['longitude'] = attrs['bottom_right_lon'];
    bottom_right['latitude'] = attrs['bottom_right_lat'];

    return bottom_right;
};






};

// map related initialization
var geo_main_openlayers_init = function(geo_obj, map_div_name)
{
    //alert("mi 001");
    OpenLayers.Control.Hover = OpenLayers.Class(OpenLayers.Control, {
        defaultHandlerOptions: {
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
            var poi_hover = geo_obj.layer.getFeatureFromEvent(evt);
            if (poi_hover) {
                if (null !== poi_hover.attributes.m_rank) {
                    geo_obj.poi_rank_out = poi_hover.attributes.m_rank;
                    //geo_locations.update_poi_descs(geo_locations.poi_rank_out);
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
                    //'click': geo_hook_trigger_on_map_click
                }, this.handlerOptions
            );
        }, 

    });

    geo_obj.pzb_ctrl = new OpenLayers.Control.PanZoomBar();
    //geo_obj.pzb_ctrl.time_drag_delay = 500;
    //geo_obj.pzb_ctrl.buttonDown = function(evt) {geo_hook_map_bar_panning(geo_obj, geo_obj.pzb_ctrl, evt);};

    var pzb_ctrl = new OpenLayers.Control.PanZoomBar();
    pzb_ctrl.geo_obj = geo_obj;

    //alert("mi 010");
    //pzb_ctrl.buttonDown = function(evt) {geo_hook_map_bar_panning(geo_obj, pzb_ctrl, evt);};
    pzb_ctrl.buttonDown = geo_hook_map_bar_panning;
    //pzb_ctrl.buttonDown = function(evt) {geo_hook_map_bar_panning(evt, geo_obj);};

    //for (pzb_part in pzb_ctrl) {
    //    alert("pzb_part " + pzb_part + " is " + pzb_ctrl[pzb_part]);
    //}

    geo_obj.map = new OpenLayers.Map(map_div_name, {
        controls: [
            new OpenLayers.Control.Navigation(),
            //geo_obj.pzb_ctrl,
            pzb_ctrl,
            new OpenLayers.Control.ScaleLine(),
            //new OpenLayers.Control.OverviewMap(),
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

    if (geo_obj.map_view_layer_providers[google_label])
    {
        // google map v3
        map_gsm = new OpenLayers.Layer.Google(
            "Google Streets",
            {numZoomLevels: 20, 'sphericalMercator': true}
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

    if (map_gsm && (google_label != geo_obj.map_view_layer_default))
    {
        map_provs.push(map_gsm);
    }
    if (map_osm && (osm_label != geo_obj.map_view_layer_default))
    {
        map_provs.push(map_osm);
    }

    geo_obj.map.addLayers(map_provs);

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
                //cursor: "pointer",
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

    {
        //alert("001");
        var features_to_add = [];

        var lonlat_search = new OpenLayers.LonLat(lonLat_cen.lon, lonLat_cen.lat);
        //alert("002");

        var point_search = new OpenLayers.Geometry.Point(lonlat_search.lon, lonlat_search.lat);
        var icon_search = 0;
        //alert("003");
        var vector_search = new OpenLayers.Feature.Vector(point_search, {type: icon_search});
        //alert("004");
        vector_search.attributes.geo_obj = geo_obj;
        vector_search.attributes['map_lon'] = lonlat_search.lon;
        vector_search.attributes['map_lat'] = lonlat_search.lat;
        vector_search.attributes['map_lon_offset'] = 0;
        vector_search.attributes['map_lat_offset'] = 0;

/*
        var cen_px = geo_obj.map.getViewPortPxFromLonLat(lonlat_search.clone());
        var topleft_px = cen_px.clone();
        topleft_px.x -= 75;
        topleft_px.y -= 100;
        var bottomright_px = cen_px.clone();
        topleft_px.x += 75;
        topleft_px.y += 100;
        var topleft_loc = geo_obj.map.getLonLatFromViewPortPx(topleft_px).transform(
            this.map.getProjectionObject(), // to Spherical Mercator Projection
            new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
        );

        var bottomright_loc = geo_obj.map.getLonLatFromViewPortPx(bottomright_px).transform(
            this.map.getProjectionObject(), // to Spherical Mercator Projection
            new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
        );

        vector_search.attributes['top_left_lon'] = 0;
        vector_search.attributes['top_left_lat'] = 0;
        vector_search.attributes['bottom_right_lon'] = 0;
        vector_search.attributes['bottom_right_lat'] = 0;
*/

        features_to_add.push(vector_search);
        //alert("005");

        geo_obj.layer.addFeatures(features_to_add);
        //alert("006");

        geo_hook_set_search_corners(vector_search);
    }


    geo_obj.map.events.register("zoomend", geo_obj, function (e) {geo_hook_set_search_corners(vector_search);});

    // registering for click events
    var click = new OpenLayers.Control.Click();
    geo_obj.map.addControl(click);
    click.activate();

    var hover = new OpenLayers.Control.Hover();
    geo_obj.map.addControl(hover);
    hover.activate();

    var cur_date = new Date();
    redraw_times.map_dragging_last = cur_date.getTime();


    var drag_feature = new OpenLayers.Control.DragFeature(geo_obj.layer);
    drag_feature.onStart = geo_hook_poi_dragg_start;
    drag_feature.onComplete = geo_hook_poi_dragged;
    geo_obj.map.addControl(drag_feature);
    drag_feature.activate();

    var drag_map = new OpenLayers.Control.DragPan([map_gsm, map_osm]);
    drag_map.panMapDone = function(pixel) {geo_hook_map_dragged(geo_obj, pixel)};
    drag_map.panMap = function(xy) {geo_hook_map_dragging(drag_map, geo_obj, xy)};
    geo_obj.map.addControl(drag_map);
    drag_map.activate();

    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    geo_obj.map.addControl(geo_obj.select_control);
    geo_obj.select_control.activate();

/*
    geo_obj.layer.events.on({
        'featureselected': geo_hook_on_feature_select,
        'featureunselected': geo_hook_on_feature_unselect
    });
*/
    //alert("mi 050");

};

// the entry initialization point
var geo_main_selecting_locations = function (geo_obj, geocodingdir, div_name, descs_name, names_show, names_hide, editing)
{
    //alert("aa: " + geo_obj);
    // doing the divs show/hide task first
    // the show/hide part was used mainly at the initial version
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

    //if (geo_obj.map_obj) {return;}
    //geo_obj.map_obj = true;

    //alert("wtf");
    useSystemParameters();
    //alert("wtf");

    //geo_obj.map_edit_prepare_markers();

    // call the map-related initialization
    geo_main_openlayers_init(geo_obj, div_name);
    //alert("wtf");

    //geo_obj.map_pois_load();

    //geo_obj.set_save_state(false);
    //geo_obj.map_spec_changed = false;
/*
    if ("0" == "" + geo_obj.map_id)
    {
        geo_obj.set_save_state(true);
        geo_obj.map_spec_changed = true;
    }
*/
};

