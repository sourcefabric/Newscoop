// the main object to hold geo-things
var geo_locations = {};

// marker icons paths and names, set during initialization
geo_locations.marker_src_base = "";
geo_locations.marker_src_default = "";
geo_locations.marker_src_names = [];

// what map provider should be used, and map position
geo_locations.map_view_layer_name = "";
geo_locations.map_view_layer_center = null;
geo_locations.map_view_layer_zoom = 0;

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
geo_locations.descs_count_inc;

// not to make new POI on closing a pop-up
geo_locations.ignore_click = false;
// the used pop-up window
geo_locations.popup = null;


// setting the db based default info
geo_locations.set_map_info = function(params)
{
    //alert("geo_locations.set_map_info");
}
geo_locations.set_icons_info = function(params)
{
    //alert("geo_locations.set_icons_info");
}
geo_locations.set_popups_info = function(params)
{
    //alert("geo_locations.set_popups_info");
}

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

// removal of the requested POI (bound on the 'remove' link)
geo_locations.remove_poi = function(index)
{
    var layer_index = this.display_index(index);

    var to_remove = [];
    to_remove.push(this.layer.features[layer_index])
    this.layer.removeFeatures(to_remove);

    this.poi_markers[index].usage = false;
    var cur_marker = this.poi_markers[index];
    if (cur_marker.in_db)
    {
        this.poi_deletion.push(cur_marker.db_index);
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

    var poi_order_length = poi_order_new.length;
    for (var pind = 0; pind < poi_order_length; pind++)
    {
        var cur_poi_desc = poi_order_new[pind];
        var cur_poi_list = cur_poi_desc.split("_");
        var cur_poi_ind = parseInt(cur_poi_list[cur_poi_list.length - 1]);
        this.poi_order_user.push(cur_poi_ind);
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

    descs_inner = "";
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
        if (disp_index <= this.layer.features.length)
        {
            var cur_marker = this.layer.features[disp_index - 1];
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
        descs_inner += "<div>";
        descs_inner += "<div class='poi_actions'>";
        descs_inner += "(<a href='#' onclick='geo_locations.edit_poi(" + pind + ");return false;'>edit</a>)&nbsp;";
        descs_inner += "(<a href='#' onclick='geo_locations.center_poi(" + pind + ");return false;'>center</a>)&nbsp;";
        descs_inner += "(<a href='#' onclick='geo_locations.remove_poi(" + pind + ");return false;'>remove</a>)";
        descs_inner += "</div>";
        descs_inner += "<div class='poi_coors'>";
        descs_inner += "lat: " + cur_poi.lat.toFixed(6) + "";
        descs_inner += "</div>";
        descs_inner += "<div class='poi_coors'>";
        descs_inner += "lon: " + cur_poi.lon.toFixed(6) + "";
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

    var index = feature.attributes.m_rank;
    var cur_poi_info = geo_locations.poi_markers[index];

    var lonlat = geo_locations.map.getLonLatFromViewPortPx(pixel);

    lonlat.lon -= cur_poi_info['map_lon_offset'];
    lonlat.lat -= cur_poi_info['map_lat_offset'];

    cur_poi_info['map_lon'] = lonlat.lon;
    cur_poi_info['map_lat'] = lonlat.lat;

    lonlat.transform(geo_locations.map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));

    cur_poi_info['lon'] = lonlat.lon;
    cur_poi_info['lat'] = lonlat.lat;

    geo_locations.update_poi_descs(index);

    // to move the POI's pop-up too, if it is displayed
    if (geo_locations.popup) {
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

    var marker_main = this.marker_src_base + this.marker_src_default;

    // making poi for features
    var features = [];
    var point = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
    var vector = new OpenLayers.Feature.Vector(point, {type: 0});

    this.poi_rank_out = this.descs_count;
    vector.attributes.m_rank = this.descs_count;
    vector.attributes.m_title = poi_title;
    vector.attributes.m_link = "";
    vector.attributes.m_description = "fill in the POI description";
    vector.attributes.m_image = "";
    vector.attributes.m_embed = "";
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

    this.poi_markers.push({'lon':lonlat.lon, 'lat':lonlat.lat, 'usage':true, 'map_lon': map_lon, 'map_lat': map_lat, "in_db": false});

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
var geo_main_openlayers_init = function(map_div_name, markers)
{
    var marker_main = markers['main'];

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

    // google map v3
    var map_gsm = new OpenLayers.Layer.Google(
        "Google Streets", // the default
        {numZoomLevels: 20, 'sphericalMercator': true}
    );

    // openstreetmap
    var map_osm = new OpenLayers.Layer.OSM();

    geo_locations.map.addLayers([map_gsm, map_osm]);
    // for switching between maps
    geo_locations.map.addControl(new OpenLayers.Control.LayerSwitcher());

/*
    // an initial center point, will be set via parameters
    var lonLat_cen = new OpenLayers.LonLat(13.92, 51.29)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            geo_locations.map.getProjectionObject() // to Spherical Mercator Projection
          );
*/

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

    var zoom = 6; // the 4 should be (probably) the default

    var style_map = new OpenLayers.StyleMap({
                fillOpacity: 1,
                pointRadius: 10,
                graphicYOffset: -20,
                graphicXOffset: -10,
                //graphicYOffset: -25,
                //graphicXOffset: -10.5,
                graphicZIndex: 10,
    });

    var lookup = {
                0: {externalGraphic: geo_locations.marker_src_base + geo_locations.marker_src_names[0]},
                1: {externalGraphic: geo_locations.marker_src_base + geo_locations.marker_src_names[1]},
                2: {externalGraphic: geo_locations.marker_src_base + geo_locations.marker_src_names[2]},
                3: {externalGraphic: geo_locations.marker_src_base + geo_locations.marker_src_names[3]}
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

    // setting some demo feature POIs
    var features = [];
    var point = null;
    var vector = null;
    point = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);
    vector = new OpenLayers.Feature.Vector(point, {type: 0, title: "bah A"});
    vector.attributes.m_rank = 0;
    vector.attributes.m_title = "Prague";
    vector.attributes.m_link = "http://campsite.sourcefabric.org/";
    vector.attributes.m_description = "Great Campsite";
    vector.attributes.m_embed = "";
    vector.attributes.m_image = "http://www.sourcefabric.org/get_img.php?NrImage=120&NrArticle=6";
    features.push(vector);

    geo_locations.poi_markers.push({'lon': lonLat_ini.lon, 'lat': lonLat_ini.lat, 'usage':true, 'map_lon': lonLat.lon, 'map_lat': lonLat.lat, "in_db": true, "db_index": 0});

    point = new OpenLayers.Geometry.Point(lonLat2.lon, lonLat2.lat);
    vector2 = new OpenLayers.Feature.Vector(point, {type: 0, title: "bah 2"});
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

    // setting map center
    //geo_locations.map.setCenter (lonLat_cen, zoom);
    geo_locations.map.setCenter (lonLat, zoom);

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
    }

};

// when a feature pop-up should be diplayed on map event
var geo_hook_on_feature_select = function(evt)
{
    var feature = evt.feature;

    var pop_link = feature.attributes.m_link;
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

    var pop_text = "";
    if (0 < pop_link.length) {
        pop_text += "<a href=\"" + pop_link + "\" target=\"_blank\">";
    }
    pop_text += "<h2>" + feature.attributes.m_title + "</h2>";
    if (0 < pop_link.length) {
        pop_text += "</a>";
    }

    var with_embed = false;
    if (feature.attributes.m_embed)
    {
        pop_text += "<br />" + feature.attributes.m_embed + "<br />";
        with_embed = true;
    }
    else
    {
        if (feature.attributes.m_image)
        {
            pop_text += "<br /><img src=\"" + feature.attributes.m_image + "\"><br />";
        }
    }

    pop_text += feature.attributes.m_description;
    geo_locations.cur_pop_rank += 1;
    geo_locations.popup = new OpenLayers.Popup.FramedCloud("featurePopup_" + geo_locations.cur_pop_rank,
        feature.geometry.getBounds().getCenterLonLat(),
        new OpenLayers.Size(100,100),
        pop_text,
        null, true, geo_hook_on_popup_close);

    if (with_embed) {
        geo_locations.popup.minSize = new OpenLayers.Size(425 + 100, 350 + 250);
    }

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

    // should be set via parameters
    geo_locations.marker_src_base = geocodingdir + "markers/";
    geo_locations.marker_src_default = "marker-gold.png";
    geo_locations.marker_src_names = ["marker-gold.png", "marker-red.png", "marker-green.png", "marker-blue.png"];
    geo_locations.map_edit_prepare_markers();

    var marker_files = {};
    marker_files['main'] = geo_locations.marker_src_base + geo_locations.marker_src_default;

    // call the map-related initialization
    geo_main_openlayers_init(div_name, marker_files);

};

// the data that should be loaded on POI edit start
geo_locations.load_point_data = function()
{
    this.load_point_label();
    this.load_point_icon();
};

// storing POI's visible name
geo_locations.store_point_label = function()
{
    var label_obj = document.getElementById ? document.getElementById("point_label") : null;

    var use_index = this.display_index(this.edited_point);
    var cur_marker = this.layer.features[use_index];
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

// storing POI's short description, for side panel view
geo_locations.store_point_perex = function()
{

};

// loading POI's marker icon
geo_locations.load_point_icon = function()
{
    var img_selected = document.getElementById ? document.getElementById("eidt_marker_selected_src") : null;

    var img_index = 0;
    if (this.layer && this.layer.features && this.layer.features[this.edited_point])
    {
        img_index = this.layer.features[this.edited_point].attributes.type;
    }

    var img_path = this.marker_src_base + this.marker_src_names[img_index];

    img_selected.src = img_path;
};

// loading info on pop-up image, if any
geo_locations.load_point_image = function()
{
}

// storing info on pop-up image, if any
geo_locations.store_point_image = function()
{
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
    this.map_view_layer_name = this.map.baseLayer.name;
    this.map_view_layer_center = this.map.getCenter();
    this.map_view_layer_zoom = this.map.getZoom();
};

// changing the size for the map div for the reader view
geo_locations.map_width_change = function(size)
{
    if ((0 > size) && (10 >= this.map_art_view_width)) {return;}
    if ((0 < size) && (1200 <= this.map_art_view_width)) {return;}

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

// saving data, on the main 'save' user action; do ajax here
geo_locations.map_save_all = function()
{
    return;
};

// storing info on a single POI (the edited one); ajax action
geo_locations.save_edit_window = function()
{

};

// storing POI's info on prepared view vs. any html pop-up view
geo_locations.store_point_direct = function(checked)
{
    this.set_edit_direct(checked);
};

// displaying appropriate part for text input for the POI content
geo_locations.set_edit_direct = function(checked)
{

    if (checked)
    {
        $("#edit_part_content").removeClass("hidden");
        $("#edit_part_description").addClass("hidden");
    }
    else
    {
        $("#edit_part_content").addClass("hidden");
        $("#edit_part_description").removeClass("hidden");
    }

};

// setting POI's icon on edit action
geo_locations.map_edit_set_marker = function(index)
{
    var img_path = this.marker_src_base + this.marker_src_names[index];

    var img_selected = document.getElementById ? document.getElementById("eidt_marker_selected_src") : null;
    img_selected.src = img_path;

    this.layer.features[this.edited_point].attributes.type = index;
    this.layer.redraw();

};

// preparing icon part of POI editing, initial phase
geo_locations.map_edit_prepare_markers = function()
{
    var img_selected = document.getElementById ? document.getElementById("eidt_marker_selected_src") : null;
    var img_index = 0;

    var img_path = this.marker_src_base + this.marker_src_names[img_index];

    img_selected.src = img_path;

    var img_choices = document.getElementById ? document.getElementById("eidt_marker_choices") : null;

    var choices_html = "";

    var choice_one = "";
    var choices_count = this.marker_src_names.length;

    for (var cind = 0; cind < choices_count; cind++)
    {
        var cur_img_name = this.marker_src_names[cind];
        choice_one = "<div class='edit_marker_one_choice'><a class=\"edit_marker_one_choice_link\" href='#' onClick=\"geo_locations.map_edit_set_marker(" + cind + "); return false;\"><img src='" + this.marker_src_base + cur_img_name + "'></a></div>";
        choices_html += choice_one;
    }
    img_choices.innerHTML = choices_html;

};

