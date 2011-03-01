var map, vectors, controls;
var geo_handler;
var del_click;
var last_info_string = "";

var auto_report = setInterval("report(null);", 500);

var into_method_pan = function() {
    var geo_obj = geo_handler;
    if (!geo_handler) {return;}

    controls.polygon.deactivate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    $(pan_obj).removeClass("geo_filter_unselected");
    $(new_obj).addClass("geo_filter_unselected");
    $(new_obj).removeClass("geo_filter_selected");
    $(pan_obj).addClass("geo_filter_selected");
};

var into_method_new = function() {
    var geo_obj = geo_handler;
    if (!geo_handler) {return;}

    controls.polygon.activate();

    controls.modify.deactivate();
    controls.modify.activate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    $(new_obj).removeClass("geo_filter_unselected");
    $(pan_obj).addClass("geo_filter_unselected");
    $(pan_obj).removeClass("geo_filter_selected");
    $(new_obj).addClass("geo_filter_selected");
};

var remove_polygon = function(rank) {
    var geo_obj = geo_handler;
    if (!geo_handler) {return;}

    var feature = vectors.features[rank];

    if (feature == controls.modify.feature) {
        controls.modify.unselectFeature(feature);
    }

    vectors.removeFeatures(feature);
    report(null);
};

var report = function(event) {
    var geo_obj = geo_handler;
    if (!geo_obj) {return;}

    var info_obj = document.getElementById ? document.getElementById("geo_polygons_info") : null;
    //var info_text = "<br>&nbsp;<br>\nPolygon coordinates:<br>&nbsp;<br>\n";
    var info_text = "";

    var features = vectors.features;

    var features_count = features.length;

    for (var find = 0; find < features_count; find++) {
        var cur_feature = features[find];
        var geometry = cur_feature["geometry"];
        var verts = geometry.getVertices();

        if (2 > verts.length) {continue;}

        info_text += "<div style='float:left'>" + verts.length + " " + geo_obj.loc_strings.corners + " <a href='#' onclick='remove_polygon(" + find + "); return false;'><span class=\"ui-icon ui-icon-closethick\"></span></a></div><br>\n";

        var sel_mod = false;
        if (cur_feature == controls.modify.feature) {
            sel_mod = true;
        }

        var cons_pol = "";
        if (sel_mod) {
            cons_pol += "<span style='color:#663300'>";
        }

        cons_pol += "polygon";
        var vert_count = verts.length;
        for (var vind = 0; vind < vert_count; vind++) {
            var cur_vx = verts[vind];

            var point = new OpenLayers.LonLat(cur_vx.x, cur_vx.y).transform(
                geo_obj.map.getProjectionObject(), // to Spherical Mercator Projection
                new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
            );
            cons_pol += " " + point.lat + " " + point.lon + ";";

        }
        if (sel_mod) {
            cons_pol += "</span>";
        }
        info_text += cons_pol + "<br>\n";
        info_text += "<br>\n";
    }

    if (last_info_string == info_text) {return;}
    last_info_string = info_text;

    info_obj.innerHTML = info_text;

};

// better for some providers to try more than once
if (5 > OpenLayers.IMAGE_RELOAD_ATTEMPTS)
{
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
}


// the main object to hold geo-things
function geo_locations () {

// specifying the article that the map is for

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

this.loc_strings = {
    "corners": "corners"
}

this.set_strings = function(params) {
    //alert(params);
    var corners = params["corners"];
    if (corners) {this.loc_strings["corners"] = corners;}
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

    return;

    var feature = this.layer.features[0];

    var lonlat = this.map.getCenter();
    var pixel = this.map.getViewPortPxFromLonLat(lonlat);
    feature.move(pixel);

    var attrs = feature.attributes;
    attrs['map_lon'] = lonlat.lon;
    attrs['map_lat'] = lonlat.lat;

    geo_hook_set_search_corners(feature);

};

};

// map related initialization
var geo_main_openlayers_init = function(geo_obj, map_div_name)
{
    geo_handler = geo_obj;

    var pzb_ctrl = new OpenLayers.Control.PanZoomBarMod();
    pzb_ctrl.geo_obj = geo_obj;

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
            "Google Maps",
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
            "MapQuest Open"
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
        //map_osm.displayOutsideMaxExtent = true;
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

    //var lswitch = new OpenLayers.Control.LayerSwitcherMod();
    var lswitch = new OpenLayers.Control.LayerSwitcher();
    geo_obj.map.addControl(lswitch);



    OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '2';
    vectors = new OpenLayers.Layer.Vector("Vector Layer");

    geo_obj.map.addLayers([vectors]);

    controls = {
        polygon: new OpenLayers.Control.DrawFeature(vectors,
                    OpenLayers.Handler.Polygon),
        modify: new OpenLayers.Control.ModifyFeature(vectors)
    };

    for(var key in controls) {
        geo_obj.map.addControl(controls[key]);
    }

    controls.polygon.activate();
    controls.modify.activate();

    controls.modify.mode = OpenLayers.Control.ModifyFeature.RESHAPE;
    controls.modify.mode |= OpenLayers.Control.ModifyFeature.DRAG;

    vectors.events.on({
        "beforefeaturemodified": report,
        "featuremodified": report,
        "afterfeaturemodified": report,
        "vertexmodified": report,
        "sketchmodified": report,
        "sketchstarted": report,
        "sketchcomplete": report
    });

    // an initial center point, set via parameters
    var cen_ini_longitude = geo_obj.map_view_layer_center_ini["longitude"];
    var cen_ini_latitude = geo_obj.map_view_layer_center_ini["latitude"];
    var lonLat_cen = new OpenLayers.LonLat(cen_ini_longitude, cen_ini_latitude)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            geo_obj.map.getProjectionObject() // to Spherical Mercator Projection
          );
    var zoom = geo_obj.map_view_layer_zoom;

    // setting map center
    geo_obj.map.setCenter (lonLat_cen, zoom);

    geo_obj.map_view_layer_name = geo_obj.map.layers[0].name;
    geo_obj.map_view_layer_center = geo_obj.map.getCenter();
    geo_obj.map_view_layer_zoom = geo_obj.map.getZoom();

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

