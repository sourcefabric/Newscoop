// better for some providers to try more than once
if (5 > OpenLayers.IMAGE_RELOAD_ATTEMPTS)
{
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
}

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

    geo_obj.select_control.destroy();
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    geo_obj.map.addControl(geo_obj.select_control);
    geo_obj.select_control.activate();
};

// to insert new POI on map click, but not on a click that closes a pop-up
var geo_hook_trigger_on_map_click = function(geo_obj, e)
{
    if (geo_obj.ignore_click) {
        geo_obj.ignore_click = false;
    }

    if (e['cancelBubble']) {return true;}

    if (undefined !== e.originalTarget)
    {
        if ("object" != (typeof e.originalTarget))
        {
            return true;
        }

        if (e.originalTarget instanceof HTMLSpanElement)
        {
            return true;
        }
        if (e.originalTarget instanceof HTMLDivElement)
        {
            return true;
        }
    }
    else
    {
        if (undefined !== e['srcElement'])
        {
            var src_el_rep = e['srcElement'].toString();
            if ("http" == src_el_rep.substr(0, 4)) {return true;}
        }
    }

    var lonlat = geo_obj.map.getLonLatFromViewPortPx(e.xy);

    var pixel = e.xy;

    var feature = geo_obj.layer.features[0];
    feature.move(pixel);

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

    var lonlat = geo_obj.map.getLonLatFromViewPortPx(pixel);

    attrs['map_lon_offset'] = lonlat.lon - attrs['map_lon'];
    attrs['map_lat_offset'] = lonlat.lat - attrs['map_lat'];

};

// updating info on POI after it was dragged
var geo_hook_poi_dragged = function(feature, pixel)
{
    var attrs = feature.attributes;
    var geo_obj = attrs.geo_obj;

    var lonlat = geo_obj.map.getLonLatFromViewPortPx(pixel);

    lonlat.lon -= attrs['map_lon_offset'];
    lonlat.lat -= attrs['map_lat_offset'];

    attrs['map_lon'] = lonlat.lon;
    attrs['map_lat'] = lonlat.lat;

    geo_hook_set_search_corners(feature);

};

// setting info on the currently set search box coordinates
var geo_hook_set_search_corners = function(feature)
{
        var attrs = feature.attributes;
        var geo_obj = attrs.geo_obj;

        var lonlat_search = new OpenLayers.LonLat(attrs["map_lon"], attrs["map_lat"]);

        var cen_px = geo_obj.map.getViewPortPxFromLonLat(lonlat_search.clone());

        var topleft_px = cen_px.clone();
        topleft_px.x -= 100;
        topleft_px.y -= 75;
        var bottomright_px = cen_px.clone();
        bottomright_px.x += 100;
        bottomright_px.y += 75;

        var topleft_loc = geo_obj.map.getLonLatFromViewPortPx(topleft_px).transform(
            geo_obj.map.getProjectionObject(), // to Spherical Mercator Projection
            new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
        );

        var bottomright_loc = geo_obj.map.getLonLatFromViewPortPx(bottomright_px).transform(
            geo_obj.map.getProjectionObject(), // to Spherical Mercator Projection
            new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
        );

        attrs['top_left_lon'] = topleft_loc.lon;
        attrs['top_left_lat'] = topleft_loc.lat;
        attrs['bottom_right_lon'] = bottomright_loc.lon;
        attrs['bottom_right_lat'] = bottomright_loc.lat;

        geo_obj.fill_bbox_divs();
};


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
//this.marker_src_names = [];
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

// optional divs for displaying corners of the search rectangle
this.bbox_divs = null;

// setting the optional output divs
this.set_bbox_divs = function (params)
{
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
        'br_lat': br_lat_obj
    };

};

// puts info into the output divs
this.fill_bbox_divs = function ()
{
    if (!this.bbox_divs) {return;}

    var tl_vals = this.get_top_left();

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

// public function to provide a corner coordinates
this.get_top_left = function ()
{
    var top_left = {'longitude': 0, 'latitude': 0};

    var feature = this.layer.features[0];
    if (!feature) {return top_left;}

    var attrs = feature.attributes;
    if (!attrs) {return top_left;}

    top_left['longitude'] = attrs['top_left_lon'];
    top_left['latitude'] = attrs['top_left_lat'];

    return top_left;
};

// public function to provide a corner coordinates
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

    var pzb_ctrl = new OpenLayers.Control.PanZoomBarMod();
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
    var map_mqm = null;
    var map_osm = null;

    geo_obj.map_view_layer_names_all = {};

    var google_label = geo_obj.map_view_layer_google;
    var mqm_label = geo_obj.map_view_layer_mapquest;
    var osm_label = geo_obj.map_view_layer_osm;

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

    if (geo_obj.map_view_layer_providers[mqm_label])
    {
        // openstreetmap by mapquest
        map_mqm = new OpenLayers.Layer.MapQuest(
            "MapQuest Map"
        );
        map_mqm.wrapDateLine = true;
        map_mqm.displayOutsideMaxExtent = true;
        map_mqm.transitionEffect = 'resize';

        geo_obj.map_view_layer_names_all[mqm_label] = map_mqm.name;
        if (mqm_label == geo_obj.map_view_layer_default)
        {
            map_provs.push(map_mqm);
        }
    }

    if (geo_obj.map_view_layer_providers[osm_label])
    {
        // openstreetmap
        map_osm = new OpenLayers.Layer.OSM();
        map_osm.wrapDateLine = true;
        map_osm.attribution = "Data CC-By-SA by <a href='http://openstreetmap.org/' target='_blank'>OpenStreetMap</a>";
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
    if (map_mqm && (mqm_label != geo_obj.map_view_layer_default))
    {
        map_provs.push(map_mqm);
    }
    if (map_osm && (osm_label != geo_obj.map_view_layer_default))
    {
        map_provs.push(map_osm);
    }

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
                graphicZIndex: 10
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

    {
        var features_to_add = [];

        var lonlat_search = new OpenLayers.LonLat(lonLat_cen.lon, lonLat_cen.lat);

        var point_search = new OpenLayers.Geometry.Point(lonlat_search.lon, lonlat_search.lat);
        var icon_search = 0;

        var vector_search = new OpenLayers.Feature.Vector(point_search, {type: icon_search});

        vector_search.attributes.geo_obj = geo_obj;
        vector_search.attributes['map_lon'] = lonlat_search.lon;
        vector_search.attributes['map_lat'] = lonlat_search.lat;
        vector_search.attributes['map_lon_offset'] = 0;
        vector_search.attributes['map_lat_offset'] = 0;

        features_to_add.push(vector_search);

        geo_obj.layer.addFeatures(features_to_add);

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

    var drag_map = new OpenLayers.Control.DragPan([map_gsm, map_mqm, map_osm]);
    drag_map.panMapDone = function(pixel) {geo_hook_map_dragged(geo_obj, pixel)};
    drag_map.panMap = function(xy) {geo_hook_map_dragging(drag_map, geo_obj, xy)};
    geo_obj.map.addControl(drag_map);
    drag_map.activate();

    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    geo_obj.map.addControl(geo_obj.select_control);
    geo_obj.select_control.activate();

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

    var use_show_class = "map_shown";
    var use_hide_class = "map_hidden";
    if (geo_obj.map_shown)
    {
        use_show_class = "map_hidden";
        use_hide_class = "map_shown";
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

