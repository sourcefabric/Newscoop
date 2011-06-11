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

this.display_strings = {
    corners: "corners",
    google_map: "Google&nbsp;Maps",
    mapquest_map: "MapQuest&nbsp;Open",
    openstreet_map: "OpenStreetMap",
    pan_map: "Pan Map",
    edit_polygon: "Edit Polygon",
    create_polygon: "Create Polygon"
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

this.set_display_strings = function(local_strings)
{
    if (!local_strings) {return;}

    var display_string_names = [
        "corners",
        "google_map",
        "mapquest_map",
        "openstreet_map",
        "pan_map",
        "edit_polygon",
        "create_polygon"
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

};

this.add_polygon = function(data, layer) {
    if (layer === undefined) {
        layer = this.vectors;
    }

    var geo_obj = this;

    var cur_polygon_points = [];
    var one_point = null;

    var geo_ring = null;
    var geo_polygon = null;
    var geo_polygon_feature = null;

    var cur_stage = "lat";
    var cur_lat = null;
    var cur_lon = null;

    try {
        var data_parts = data.split(" ");
        var data_parts_size = data_parts.length;
        for (var dind = 0; dind < data_parts_size; dind++) {
            var one_part = data_parts[dind].replace(/^\s+|\s+$/g, '');
            if ("" == one_part) {continue;}
            one_part = one_part.replace(/;$/g, '');

            one_part = one_part.toLowerCase();
            if ("polygon" == one_part) {
                if (0 < cur_polygon_points.length) {
                    this.insert_polygon(cur_polygon_points, layer);
                }

                cur_polygon_points = [];

                cur_stage = "lat";
                cur_lat = null;
                cur_lon = null;

                continue;
            }

            one_number = parseFloat(one_part);
            if (isNaN(one_number)) {continue;}
            if (!isFinite(one_number)) {continue;}

            if ("lat" == cur_stage) {
                cur_lat = one_number;
                cur_stage = "lon";
            }
            else {
                cur_lon = one_number;
                cur_stage = "lat";
                one_point = new OpenLayers.Geometry.Point(cur_lon, cur_lat).transform(
                    new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                    geo_obj.map.getProjectionObject() // to Spherical Mercator Projection
                );
                cur_polygon_points.push(one_point);
                one_point = null;
            }
        }

        if (0 < cur_polygon_points.length) {
            this.insert_polygon(cur_polygon_points, layer);
        }

    } catch(e) {
        alert("wrong polygon data");
    }

    if (this.vectors == layer) {
        this.report();
    }
};

this.into_method_pan = function(event) {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    geo_obj.controls.polygon.deactivate();
    geo_obj.controls.modify.deactivate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var mod_obj = document.getElementById ? document.getElementById("geo_filter_edit_polygon") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    if (pan_obj) {
        $(pan_obj).removeClass("geo_filter_unselected");
    }
    if (mod_obj) {
        $(mod_obj).addClass("geo_filter_unselected");
    }
    if (new_obj) {
        $(new_obj).addClass("geo_filter_unselected");
    }
    if (pan_obj) {
        $(pan_obj).addClass("geo_filter_selected");
    }
    if (mod_obj) {
        $(mod_obj).removeClass("geo_filter_selected");
    }
    if (new_obj) {
        $(new_obj).removeClass("geo_filter_selected");
    }

    if (this.act_pan_map) {
        this.act_pan_map.innerHTML = this.act_pan_map_button_on;
    }
    if (this.act_edit_polygon) {
        this.act_edit_polygon.innerHTML = this.act_edit_polygon_button_off;
    }
    if (this.act_create_polygon) {
        this.act_create_polygon.innerHTML = this.act_create_polygon_button_off;
    }

    if (event) {
        event.stopImmediatePropagation();
        event.preventDefault();
    }

};

this.into_method_mod = function(event) {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    geo_obj.controls.polygon.deactivate();
    geo_obj.controls.modify.deactivate();
    geo_obj.controls.modify.activate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var mod_obj = document.getElementById ? document.getElementById("geo_filter_edit_polygon") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    if (pan_obj) {
        $(pan_obj).addClass("geo_filter_unselected");
    }
    if (mod_obj) {
        $(mod_obj).removeClass("geo_filter_unselected");
    }
    if (new_obj) {
        $(new_obj).addClass("geo_filter_unselected");
    }
    if (pan_obj) {
        $(pan_obj).removeClass("geo_filter_selected");
    }
    if (mod_obj) {
        $(mod_obj).addClass("geo_filter_selected");
    }
    if (new_obj) {
        $(new_obj).removeClass("geo_filter_selected");
    }

    if (this.act_pan_map) {
        this.act_pan_map.innerHTML = this.act_pan_map_button_off;
    }
    if (this.act_edit_polygon) {
        this.act_edit_polygon.innerHTML = this.act_edit_polygon_button_on;
    }
    if (this.act_create_polygon) {
        this.act_create_polygon.innerHTML = this.act_create_polygon_button_off;
    }

    if (event) {
        event.stopImmediatePropagation();
        event.preventDefault();
    }
};

this.into_method_new = function(event) {
    var geo_obj = this;
    if (!geo_obj.inited) {return;}

    geo_obj.controls.modify.deactivate();
    geo_obj.controls.polygon.deactivate();
    geo_obj.controls.polygon.activate();

    var pan_obj = document.getElementById ? document.getElementById("geo_filter_pan_map") : null;
    var mod_obj = document.getElementById ? document.getElementById("geo_filter_edit_polygon") : null;
    var new_obj = document.getElementById ? document.getElementById("geo_filter_create_polygon") : null;
    if (pan_obj) {
        $(pan_obj).addClass("geo_filter_unselected");
    }
    if (mod_obj) {
        $(mod_obj).addClass("geo_filter_unselected");
    }
    if (new_obj) {
        $(new_obj).removeClass("geo_filter_unselected");
    }
    if (pan_obj) {
        $(pan_obj).removeClass("geo_filter_selected");
    }
    if (mod_obj) {
        $(mod_obj).removeClass("geo_filter_selected");
    }
    if (new_obj) {
        $(new_obj).addClass("geo_filter_selected");
    }

    if (this.act_pan_map) {
        this.act_pan_map.innerHTML = this.act_pan_map_button_off;
    }
    if (this.act_edit_polygon) {
        this.act_edit_polygon.innerHTML = this.act_edit_polygon_button_off;
    }
    if (this.act_create_polygon) {
        this.act_create_polygon.innerHTML = this.act_create_polygon_button_on;
    }

    if (event) {
        event.stopImmediatePropagation();
        event.preventDefault();
    }

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

    var brecs = [];

    var info_obj = document.getElementById ? document.getElementById("geo_polygons_info") : null;
    var info_text = "";

    var features = this.vectors.features;

    var features_count = features.length;

    for (var find = 0; find < features_count; find++) {
        min_lon = 1000;
        min_lat = 1000;
        max_lon = -1000;
        max_lat = -1000;

        var cur_feature = features[find];
        var geometry = cur_feature["geometry"];
        if (!geometry) {continue;}
        if (!geometry.getVertices) {continue;}
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
        var within_datelines = true;
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

            if (point.lon < min_lon) {
                min_lon = point.lon;
            }
            if (point.lon > max_lon) {
                max_lon = point.lon;
            }
            if (point.lat < min_lat) {
                min_lat = point.lat;
            }
            if (point.lat > max_lat) {
                max_lat = point.lat;
            }

            if ((-180 > point.lon) || (180 < point.lon)) {
                within_datelines = false;
            }
        }
        cons_pol += "</div>";

        if ((0 < dp_positive) && (0 < dp_negative)) {is_convex = false;}

        var polygon_geometry_class = "geo_polygon_type_within_dl";
        if (!within_datelines) {
            polygon_geometry_class = "geo_polygon_type_over_dl";
        }

        info_text += "<div class='geo_polygon_info'><div class='geo_polygon_labels'>";
        info_text += "<div class='geo_polygon_remove'><a href='#' onclick='" + this.obj_name + ".remove_polygon(" + find + "); return false;'><span class=\"ui-icon ui-icon-closethick\"></span></a></div>\n";
        info_text += "<div class='geo_polygon_type_info " + polygon_geometry_class + "'>" + verts.length + " " + geo_obj.display_strings.corners + ", " + size_kmsq + " km<sup>2</sup></div>";
        info_text += "</div><div>";

        info_text += cons_pol + "</div></div>\n";
        info_text += "<div class='geo_filter_polygon_spacer'>&nbsp;</div>\n";

        brecs.push("polygon" + " " + min_lat + " " + min_lon + ";" + " " + min_lat + " " + max_lon + ";" + " " + max_lat + " " + max_lon + ";" + " " + max_lat + " " + min_lon + ";");

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
        map_gsm.wrapDateLine = false; // so that over-dateline polygons can be drawn

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
        map_mqm.wrapDateLine = false; // so that over-dateline polygons can be drawn
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
        map_osm.wrapDateLine = false; // so that over-dateline polygons can be drawn
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
    var lswitch = new OpenLayers.Control.LayerSwitcher();

    this.map.addControl(lswitch);

    OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '2';
    OpenLayers.Feature.Vector.style['default']['strokeOpacity'] = 0.8;
    OpenLayers.Feature.Vector.style['default']['strokeColor'] = '#000000';
    OpenLayers.Feature.Vector.style['default']['fillOpacity'] = 0.25;
    OpenLayers.Feature.Vector.style['default']['fillColor'] = '#0080ff';
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

    this.act_pan_map_img_off = this.img_url + "geo_move_off.png";
    this.act_pan_map_img_on = this.img_url + "geo_move_on.png";

    this.act_edit_polygon_img_off = this.img_url + "geo_edit_off.png";
    this.act_edit_polygon_img_on = this.img_url + "geo_edit_on.png";

    this.act_create_polygon_img_off = this.img_url + "geo_polygon_off.png";
    this.act_create_polygon_img_on = this.img_url + "geo_polygon_on.png";

    this.act_pan_map_button_on = "<div><img src=" + this.act_pan_map_img_on + " onClick='" + this.obj_name + ".into_method_pan(); return false;' alt='" + this.display_strings.pan_map + "' title='" + this.display_strings.pan_map + "'></div>";
    this.act_pan_map_button_off = "<div><img src=" + this.act_pan_map_img_off + " onClick='" + this.obj_name + ".into_method_pan(); return false;' alt='" + this.display_strings.pan_map + "' title='" + this.display_strings.pan_map + "'></div>";

    this.act_edit_polygon_button_on = "<div><img src=" + this.act_edit_polygon_img_on + " onClick='" + this.obj_name + ".into_method_mod(); return false;' alt='" + this.display_strings.edit_polygon + "' title='" + this.display_strings.edit_polygon + "'></div>";
    this.act_edit_polygon_button_off = "<div><img src=" + this.act_edit_polygon_img_off + " onClick='" + this.obj_name + ".into_method_mod(); return false;' alt='" + this.display_strings.edit_polygon + "' title='" + this.display_strings.edit_polygon + "'></div>";

    this.act_create_polygon_button_on = "<div><img src=" + this.act_create_polygon_img_on + " onClick='" + this.obj_name + ".into_method_new(); return false;' alt='" + this.display_strings.create_polygon + "' title='" + this.display_strings.create_polygon + "'></div>";
    this.act_create_polygon_button_off = "<div><img src=" + this.act_create_polygon_img_off + " onClick='" + this.obj_name + ".into_method_new(); return false;' alt='" + this.display_strings.create_polygon + "' title='" + this.display_strings.create_polygon + "'></div>";

    var act_pan_map_pos = new OpenLayers.Pixel(700, 3);
    var act_pan_map = OpenLayers.Util.createDiv("act_pan_map", act_pan_map_pos, null, null, "absolute");
    act_pan_map.style.fontSize = "1px";
    act_pan_map.style.width = "24px";
    act_pan_map.style.height = "23px";
    act_pan_map.style.background = "#a0a0a0";
    act_pan_map.style.backgroundColor = "#a0a0a0";
    act_pan_map.style.zIndex = 1500;
    act_pan_map.style.opacity = "0.80";
    act_pan_map.style.filter = "alpha(opacity=80)"; // IE
    act_pan_map.innerHTML = this.act_pan_map_button_off;
    this.act_pan_map = act_pan_map;
    this.map.viewPortDiv.appendChild(this.act_pan_map);

    var act_edit_polygon_pos = new OpenLayers.Pixel(724, 3);
    var act_edit_polygon = OpenLayers.Util.createDiv("act_edit_polygon", act_edit_polygon_pos, null, null, "absolute");
    act_edit_polygon.style.fontSize = "1px";
    act_edit_polygon.style.width = "24px";
    act_edit_polygon.style.height = "23px";
    act_edit_polygon.style.background = "#a0a0a0";
    act_edit_polygon.style.backgroundColor = "#a0a0a0";
    act_edit_polygon.style.zIndex = 1500;
    act_edit_polygon.style.opacity = "0.80";
    act_edit_polygon.style.filter = "alpha(opacity=80)"; // IE
    act_edit_polygon.innerHTML = this.act_edit_polygon_button_off;
    this.act_edit_polygon = act_edit_polygon;
    this.map.viewPortDiv.appendChild(this.act_edit_polygon);

    var act_create_polygon_pos = new OpenLayers.Pixel(748, 3);
    var act_create_polygon = OpenLayers.Util.createDiv("act_create_polygon", act_create_polygon_pos, null, null, "absolute");
    act_create_polygon.style.fontSize = "1px";
    act_create_polygon.style.width = "24px";
    act_create_polygon.style.height = "23px";
    act_create_polygon.style.background = "#a0a0a0";
    act_create_polygon.style.backgroundColor = "#a0a0a0";
    act_create_polygon.style.zIndex = 1500;
    act_create_polygon.style.opacity = "0.80";
    act_create_polygon.style.filter = "alpha(opacity=80)"; // IE
    act_create_polygon.innerHTML = this.act_create_polygon_button_on;
    this.act_create_polygon = act_create_polygon;
    this.map.viewPortDiv.appendChild(this.act_create_polygon);

    this.map.events.register("changelayer", null, function(evt) {
        if ("visibility" == evt.property) {
            OpenLayers.HooksLocal.on_layer_switch(geo_obj.map);
        }
    });

    setInterval("try {" + this.obj_name + ".map.updateSize();} catch(e) {}", 1000); // the map needs to know about div changes, e.g. when a slider comes in/out
};

this.insert_polygon = function(parsed_points, layer) {

    if (!parsed_points) {return;}

    var linear_ring = new OpenLayers.Geometry.LinearRing(parsed_points);
    if (!linear_ring) {return;}
    var polygon_feature = null;
    polygon_feature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), null, null);
    if (!polygon_feature) {return;}

    layer.addFeatures([polygon_feature]);
};

};

