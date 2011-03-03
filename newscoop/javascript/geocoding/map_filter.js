// better for some providers to try more than once
if (5 > OpenLayers.IMAGE_RELOAD_ATTEMPTS)
{
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
}

// the main object to hold geo-things
function geo_locations_filter () {

this.inited = false;
this.last_info_string = "";

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

// tha map layer
this.map = null;
this.vectors = null;
this.controls = null;
this.obj_name = "";
this.auto_report = null;
this.img_url = "";

this.loc_strings = {
    "corners": "corners"
};

this.display_strings = {
    google_map: "Google&nbsp;Maps",
    mapquest_map: "MapQuest&nbsp;Open",
    openstreet_map: "OpenStreetMap"
};

this.set_obj_name = function(name) {
    this.obj_name = name;
};

this.set_img_dir = function(dir) {
    this.img_url = dir;
};

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

this.set_strings = function(params) {
    var corners = params["corners"];
    if (corners) {this.loc_strings["corners"] = corners;}
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

this.into_method_pan = function() {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    geo_obj.controls.polygon.deactivate();
    geo_obj.controls.modify.deactivate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var mod_obj = document.getElementById ? document.getElementById("geo_filter_edit_polygon") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    $(pan_obj).removeClass("geo_filter_unselected");
    $(mod_obj).addClass("geo_filter_unselected");
    $(new_obj).addClass("geo_filter_unselected");
    $(pan_obj).addClass("geo_filter_selected");
    $(mod_obj).removeClass("geo_filter_selected");
    $(new_obj).removeClass("geo_filter_selected");
};

this.into_method_mod = function() {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    geo_obj.controls.polygon.deactivate();
    geo_obj.controls.modify.activate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var mod_obj = document.getElementById ? document.getElementById("geo_filter_edit_polygon") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    $(pan_obj).addClass("geo_filter_unselected");
    $(mod_obj).removeClass("geo_filter_unselected");
    $(new_obj).addClass("geo_filter_unselected");
    $(pan_obj).removeClass("geo_filter_selected");
    $(mod_obj).addClass("geo_filter_selected");
    $(new_obj).removeClass("geo_filter_selected");
};

this.into_method_new = function() {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    geo_obj.controls.modify.deactivate();
    geo_obj.controls.polygon.activate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var mod_obj = document.getElementById ? document.getElementById("geo_filter_edit_polygon") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    $(pan_obj).addClass("geo_filter_unselected");
    $(mod_obj).addClass("geo_filter_unselected");
    $(new_obj).removeClass("geo_filter_unselected");
    $(pan_obj).removeClass("geo_filter_selected");
    $(mod_obj).removeClass("geo_filter_selected");
    $(new_obj).addClass("geo_filter_selected");
};

this.remove_polygon = function(rank) {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    var feature = this.vectors.features[rank];

    if (feature == geo_obj.controls.modify.feature) {
        geo_obj.controls.modify.unselectFeature(feature);
    }

    this.vectors.removeFeatures(feature);
    geo_obj.report(null);
};

this.report = function(event) {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    var info_obj = document.getElementById ? document.getElementById("geo_polygons_info") : null;
    var info_text = "";

    var features = this.vectors.features;

    var features_count = features.length;

    for (var find = 0; find < features_count; find++) {
        var cur_feature = features[find];
        var geometry = cur_feature["geometry"];
        var verts = geometry.getVertices();

        if (2 > verts.length) {continue;}
        var size_kmsq = geometry.getGeodesicArea(geo_obj.map.getProjectionObject()) / 1000000;
        size_kmsq = size_kmsq.toFixed(3);

        var sel_mod = false;
        if (cur_feature == geo_obj.controls.modify.feature) {
            sel_mod = true;
        }

        var cons_pol = "";
        var polygon_class_name = "geo_polygon_info_std";
        if (sel_mod) {
            polygon_class_name = "geo_polygon_info_edited";
        }
        cons_pol += "<div class='" + polygon_class_name + "'>polygon";

        var vert_arr = [];

        var is_convex = true;
        var dp_positive = 0;
        var dp_negative = 0;

        var vert_count = verts.length;
        for (var vind = 0; vind < vert_count; vind++) {
            var cur_vx = verts[vind];

            var vx_next = verts[((vind+1)%vert_count)];
            var vx_angle = verts[((vind+2)%vert_count)];

            var norm_x = vx_next.y - cur_vx.y;
            var norm_y = cur_vx.x - vx_next.x;
            var test_x = vx_angle.x - cur_vx.x;
            var test_y = vx_angle.y - cur_vx.y;
            var dot_prod = (norm_x * test_x) + (norm_y * test_y);
            if (0 < dot_prod) {dp_positive += 1;}
            if (0 > dot_prod) {dp_negative += 1;}

            var point = new OpenLayers.LonLat(cur_vx.x, cur_vx.y).transform(
                geo_obj.map.getProjectionObject(), // to Spherical Mercator Projection
                new OpenLayers.Projection("EPSG:4326") // transform from WGS 1984
            );
            cons_pol += " " + point.lat.toFixed(6) + " " + point.lon.toFixed(6) + ";";

        }
        cons_pol += "</div>";

        if ((0 < dp_positive) && (0 < dp_negative)) {is_convex = false;}

        var polygon_geometry_class = "geo_polygon_type_convex";
        if (!is_convex) {
            polygon_geometry_class = "geo_polygon_type_concave";
        }

        info_text += "<div class='geo_polygon_info'><div class='geo_polygon_labels'>";
        info_text += "<div class='geo_polygon_remove'><a href='#' onclick='" + this.obj_name + ".remove_polygon(" + find + "); return false;'><span class=\"ui-icon ui-icon-closethick\"></span></a></div>\n";
        info_text += "<div class='geo_polygon_type_info " + polygon_geometry_class + "'>" + verts.length + " " + geo_obj.loc_strings.corners + ", " + size_kmsq + " km<sup>2</sup></div>";
        info_text += "</div><div>";

        info_text += cons_pol + "</div></div>\n";
        info_text += "<br>\n";
    }

    if (this.last_info_string == info_text) {return;}
    this.last_info_string = info_text;

    info_obj.innerHTML = info_text;

};

// map related initialization
this.main_init = function(map_div_name)
{
    geo_obj = this;

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

    this.map.addLayers(map_provs);
    this.map.addControl(new OpenLayers.Control.Attribution());
    // for switching between maps

    var lswitch = new OpenLayers.Control.LayerSwitcherMod();

    this.map.addControl(lswitch);

    OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '2';
    this.vectors = new OpenLayers.Layer.Vector("Polygon Layer");

    geo_obj.map.addLayers([geo_obj.vectors]);

    geo_obj.controls = {
        polygon: new OpenLayers.Control.DrawFeature(geo_obj.vectors,
                    OpenLayers.Handler.Polygon),
        modify: new OpenLayers.Control.ModifyFeature(geo_obj.vectors)
    };

    for(var key in geo_obj.controls) {
        geo_obj.map.addControl(geo_obj.controls[key]);
    }

    geo_obj.controls.modify.mode = OpenLayers.Control.ModifyFeature.RESHAPE;
    geo_obj.controls.modify.mode |= OpenLayers.Control.ModifyFeature.DRAG;

    geo_obj.vectors.events.on({
        "beforefeaturemodified": geo_obj.report,
        "featuremodified": geo_obj.report,
        "afterfeaturemodified": geo_obj.report,
        "vertexmodified": geo_obj.report,
        "sketchmodified": geo_obj.report,
        "sketchstarted": geo_obj.report,
        "sketchcomplete": geo_obj.report
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

    this.inited = true;
    geo_obj.into_method_new();

    if (0 < this.obj_name.length) {
        this.auto_report = setInterval(this.obj_name + ".report(null);", 500);
    }
};

};

