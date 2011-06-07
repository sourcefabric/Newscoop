// the OL are not ready at the 'ready' event by JQuery

OpenLayers.Util.test_ready = function() {
    var aux_layer = null;
    try {
        aux_layer = new OpenLayers.Layer.Vector("auxiliary");
    } catch (e) {
        return false;
    }
    return true;
};

// auxiliary url changing on MapQuest failures

OpenLayers.IMAGE_RELOAD_ATTEMPTS = 20;
OpenLayers.Util.originalOnImageLoadError = OpenLayers.Util.onImageLoadError;
OpenLayers.Util.onImageLoadError = function() {
    if (this.src.match(/^http:\/\/otile[1-4]\.mqcdn\.com\//)) {
        if (!this._attempts) {this._attempts = 0;}
        var mq_start = "http://otile" + Math.floor(1 + (4 * Math.random())) + ".mqcdn.com/tiles/1.0.0/osm/";
        if ((2 + this._attempts) > OpenLayers.IMAGE_RELOAD_ATTEMPTS)
        {
            var osm_start = "http://tile.openstreetmap.org/";
            this.src = osm_start + this.src.substr(mq_start.length);
        }
        else
        {
            this.src = mq_start + this.src.substr(mq_start.length);
        }
    }
    OpenLayers.Util.originalOnImageLoadError();
};

// OSM based map layers with correct tail numbers, attribution texts, urls, ...

OpenLayers.Layer.OSMMod = OpenLayers.Class(OpenLayers.Layer.OSM, {
    name: "OpenStreetMap",
    attribution: "Data CC-By-SA by <a href='http://openstreetmap.org/' target='_blank'>OpenStreetMap</a>",
    sphericalMercator: true,
    url: 'http://tile.openstreetmap.org/${z}/${x}/${y}.png',
    getURL: function (bounds) {
        var res = this.map.getResolution();
        var x = Math.round((bounds.left - this.maxExtent.left) 
            / (res * this.tileSize.w));
        var y = Math.round((this.maxExtent.top - bounds.top) 
            / (res * this.tileSize.h));
        var z = this.map.getZoom() + this.zoomOffset;

        var z_mod = Math.pow(2, z);
        while (x < 0) {
            x += z_mod;
        }
        while (y < 0) {
            y += z_mod;
        }
        while (x >= z_mod) {
            x -= z_mod;
        }
        while (y >= z_mod) {
            y -= z_mod;
        }

        var url = this.url;
        var s = '' + x + y + z;
        if (url instanceof Array)
        {
            url = this.selectUrl(s, url);
        }
        
        var path = OpenLayers.String.format(url, {'x': x, 'y': y, 'z': z});

        return path;
    },
     CLASS_NAME: "OpenLayers.Layer.OSM"
});

OpenLayers.Layer.MapQuest = OpenLayers.Class(OpenLayers.Layer.OSMMod, {
    name: "MapQuest",
    attribution: "Data CC-By-SA by <a href='http://openstreetmap.org/' target='_blank'>OpenStreetMap</a>. Rendered by <a href='http://open.mapquest.co.uk/' target='_blank'>MapQuest</a>.",
    sphericalMercator: true,
    url: [
        'http://otile1.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
        'http://otile2.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
        'http://otile3.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
        'http://otile4.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png'
    ],
    CLASS_NAME: "OpenLayers.Layer.MapQuest"
});

// solving issues on Google v3 things
OpenLayers.Layer.GoogleMod = OpenLayers.Class(OpenLayers.Layer.Google, {
    // just added the modRepositionMapElements call at the end
    initialize: function(name, options) {
        options = options || {};
        if(!options.version) {
            options.version = typeof GMap2 === "function" ? "2" : "3";
        }
        var mixin = OpenLayers.Layer.Google["v" +
            options.version.replace(/\./g, "_")];
        if (mixin) {
            OpenLayers.Util.applyDefaults(options, mixin);
        } else {
            throw "Unsupported Google Maps API version: " + options.version;
        }

        OpenLayers.Util.applyDefaults(options, mixin.DEFAULTS);
        if (options.maxExtent) {
            options.maxExtent = options.maxExtent.clone();
        }

        OpenLayers.Layer.EventPane.prototype.initialize.apply(this,
            [name, options]);
        OpenLayers.Layer.FixedZoomLevels.prototype.initialize.apply(this,
            [name, options]);

        if (this.sphericalMercator) {
            OpenLayers.Util.extend(this, OpenLayers.Layer.SphericalMercator);
            this.initMercatorParameters();
        }
        this.modRepositionMapElements();
    },
    'numZoomLevels': 20,
    'sphericalMercator': true,
    'modRepositionMapElements': function () {
    // we need to change it here, otherwise the old repositionMapElements function would be used
        this.repositionMapElements = this.repositionMapElementsMod;
    },
    'repositionMapElementsMod' : function () {
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
    },
    CLASS_NAME: "OpenLayers.Layer.Google"
});

// controls for actions on hover and click

OpenLayers.Control.Hover = OpenLayers.Class(OpenLayers.Control, {
    defaultHandlerOptions: {
        'delay': 200,
        'pixelTolerance': 2,
        'triggerAction': function(evt, map) {}
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
    setTriggerAction: function(action) {this.triggerAction = action;},
    trigger: function(evt) {
        this.triggerAction(evt, this.map);
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
        'triggerAction': function(evt, map) {}
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
                'click': this.trigger
            }, this.handlerOptions
        );
    },
    setTriggerAction: function(action) {this.triggerAction = action;},
    trigger: function(evt) {
        this.triggerAction(evt, this.map);
    }
});

// empty placeholders for the proposed hook functions

OpenLayers.Hooks = {};
OpenLayers.Hooks.Zooms = {};

OpenLayers.Hooks.LayerSwitcher = {};
OpenLayers.Hooks.LayerSwitcher.layerSwitched = function(ctrl) {return null;};
OpenLayers.Hooks.Zooms.maxZoom = function(ctrl) {return null;}
OpenLayers.Hooks.Zooms.minZoom = function(ctrl) {return 0;}
OpenLayers.Hooks.PanZoom = {};
OpenLayers.Hooks.PanZoom.buttonDown = function(ctrl) {return null};
OpenLayers.Hooks.PanZoomBar = {};
OpenLayers.Hooks.PanZoomBar.divClick = function(ctrl) {return true};
OpenLayers.Hooks.PanZoomBar.buttonDown = function(ctrl) {return null};
OpenLayers.Hooks.PanZoomBar.zoomBarUp = function(ctrl) {return true};
OpenLayers.Hooks.DragPan = {};
OpenLayers.Hooks.DragPan.panMap = function(ctrl) {return null};
OpenLayers.Hooks.DragPan.panMapDone = function(ctrl) {return null};

// changed map controls to contain the proposed hook calls

OpenLayers.Control.PanZoomMod = OpenLayers.Class(OpenLayers.Control.PanZoom, {
    buttonDown: function(evt) {
        if (!OpenLayers.Event.isLeftClick(evt)) {
            return;
        }
        OpenLayers.Hooks.PanZoom.buttonDown(this);

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
    },
    CLASS_NAME: "OpenLayers.Control.PanZoom"
});

OpenLayers.Control.PanZoomBarMod = OpenLayers.Class(OpenLayers.Control.PanZoomBar, {
    // msie does not stops the event, and does not preserves its properties either
    divClick: function (evt) {
        //if (undefined === evt.stopPropagation) {evt.stopPropagation = null;}

        if (!OpenLayers.Event.isLeftClick(evt)) {
            return;
        }
        var levels = evt.xy.y / this.zoomStopHeight;
        if (this.forceFixedZoomLevel || !this.map.fractionalZoom) {
            levels = Math.floor(levels);
        }
        var zoom = this.map.getNumZoomLevels() - 1 - levels;
        zoom = Math.min(Math.max(zoom, 0), this.map.getNumZoomLevels() - 1);
        this.map.zoomTo(zoom);

        var stop_event = OpenLayers.Hooks.PanZoomBar.divClick(this);
        if (stop_event)
        {
            OpenLayers.Event.stop(evt);
        }

    },

    buttonDown: function(evt) {
        if (!OpenLayers.Event.isLeftClick(evt)) {
            return;
        }
        OpenLayers.Hooks.PanZoomBar.buttonDown(this);

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
    },

    zoomBarUp: function(evt) {
        if (!OpenLayers.Event.isLeftClick(evt)) {
            return;
        }
        if (this.mouseDragStart) {
            this.div.style.cursor="";
            this.map.events.un({
                "mouseup": this.passEventToSlider,
                "mousemove": this.passEventToSlider,
                scope: this
            });
            var deltaY = this.zoomStart.y - evt.xy.y;
            var zoomLevel = this.map.zoom;
            if (!this.forceFixedZoomLevel && this.map.fractionalZoom) {
                zoomLevel += deltaY/this.zoomStopHeight;
                zoomLevel = Math.min(Math.max(zoomLevel, 0), 
                                     this.map.getNumZoomLevels() - 1);
            } else {
                zoomLevel += Math.round(deltaY/this.zoomStopHeight);
            }

            var max_layer_zoom = OpenLayers.Hooks.Zooms.maxZoom(this);
            var min_layer_zoom = OpenLayers.Hooks.Zooms.minZoom(this);

            if ((max_layer_zoom !== undefined) && (typeof max_layer_zoom === "number")) {
                if (max_layer_zoom < zoomLevel) {zoomLevel = max_layer_zoom;}
            }
            if ((min_layer_zoom !== undefined) && (typeof min_layer_zoom === "number")) {
                if (min_layer_zoom > zoomLevel) {zoomLevel = min_layer_zoom;}
            }

            this.map.zoomTo(zoomLevel);
            this.mouseDragStart = null;
            this.zoomStart = null;

            var stop_event = OpenLayers.Hooks.PanZoomBar.zoomBarUp(this);
            if (stop_event) {
                OpenLayers.Event.stop(evt);
            }
        }
    },

    CLASS_NAME: "OpenLayers.Control.PanZoomBar"
});

OpenLayers.Control.DragPanMod = OpenLayers.Class(OpenLayers.Control.DragPan, {
    panMapDone: function(pixel) {
        OpenLayers.Hooks.DragPan.panMapDone(this);
    },
    panMap: function(xy) {
        this.panned = true;

        this.map.pan(this.handler.last.x - xy.x, this.handler.last.y - xy.y, {dragging: this.handler.dragging, animate: false});

        OpenLayers.Hooks.DragPan.panMap(this);
    },

    CLASS_NAME: "OpenLayers.Control.DragPan"
});

// our auxiliary functions for popup processing

OpenLayers.HooksPopups = {};

// needed just for click on pop-up close button
OpenLayers.HooksPopups.on_popup_close = function(evt, geo_obj)
{
    if (geo_obj.popup) {
        try {
            geo_obj.select_control.unselect(geo_obj.popup.feature);
        }
        catch (e) {}
    }

    OpenLayers.Event.stop(evt, true);
};

OpenLayers.HooksPopups.on_feature_unselect = function(evt, geo_obj)
{
    var feature = evt.feature;

    if (feature.popup) {
        geo_obj.popup.feature = null;
        geo_obj.map.removePopup(feature.popup);
        feature.popup.destroy();
        feature.popup = null;
        geo_obj.popup = null;
    }

    if (geo_obj.popup) {
        try {
            geo_obj.select_control.unselect(geo_obj.popup.feature);
        }
        catch (e) {}
    }
};

OpenLayers.HooksPopups.on_feature_select_edit = function(evt, geo_obj)
{
    if (geo_obj.show_edit_on_select) {

        var feature = null;
    
        if (evt)
        {
            feature = evt.feature;
        }
    
        if (!feature) {return;}
    
        var attrs = feature.attributes;
        if (!attrs) {return;}

        geo_obj.edit_poi(attrs.m_rank);
    }
};

OpenLayers.HooksPopups.on_feature_edit_disp = function(geo_obj, index)
{
    geo_obj.ignore_edit_click = true;
    geo_obj.edit_poi(index);

    var restor_str = '' + geo_obj.obj_name + '.ignore_edit_click = false;';
    setTimeout(restor_str, 500);
};

OpenLayers.HooksPopups.on_feature_select = function(evt, geo_obj, avoid_rec)
{
    if (geo_obj.ignore_select_event) {
        geo_obj.select_control.unselect(evt.feature);
        return;
    }

    var feature = null;

    if (geo_obj.editing) {
        geo_obj.close_edit_window();
    }

    if (evt)
    {
        feature = evt.feature;
    }

    if (!feature) {return;}

    var attrs = feature.attributes;
    if (!attrs) {return;}

    if (geo_obj.popup) {
        geo_obj.select_control.unselect(geo_obj.popup.feature);
    }

    var pop_info = GeoPopups.create_popup_content(feature, geo_obj);
    var pop_text = pop_info['inner_html'];

    var edit_link = "";
    if (geo_obj.editing) {
        edit_link = '<div class="geo_edit_popup_top">';
        edit_link += "<a href='#' class='link icon-link' onClick=\"" + geo_obj.obj_name + ".remove_poi(" + attrs.m_rank + ");return false;\" title=\"" + geo_obj.display_strings.remove + "\"><span class='icon ui-icon-closethick'></span><strong>" + geo_obj.display_strings.remove_short + "</strong></a>";
        edit_link += '<a href="#" class="link icon-link" onClick="OpenLayers.HooksPopups.on_feature_edit_disp(' + geo_obj.obj_name + ', ' + attrs.m_rank + '); return false;" title="' + geo_obj.display_strings.edit_advanced + '"><span class="icon ui-icon-pencil"></span><strong>' + geo_obj.display_strings.edit + '</strong></a></div> ';
    }

    geo_obj.cur_pop_rank += 1;
    geo_obj.popup = new OpenLayers.Popup.FramedCloud("featurePopup_" + geo_obj.cur_pop_rank,
        feature.geometry.getBounds().getCenterLonLat(),
        new OpenLayers.Size(geo_obj.popup_width,geo_obj.popup_height),
        edit_link + pop_text,
        null, true, function(evt) {OpenLayers.HooksPopups.on_popup_close(evt, geo_obj);});

    var min_width = pop_info['min_width'];
    var min_height = pop_info['min_height'];

    // for a safe setting of input value on inline poi label editing
    if (geo_obj.editing) {
        geo_obj.poi_label_value_inline = pop_info.poi_label;
        setTimeout(geo_obj.obj_name + ".set_inline_label_value(" + attrs.m_rank + ");", 10);

        geo_obj.poi_content_value_inline = pop_info.poi_content;
        if (null !== geo_obj.poi_content_value_inline) {
            setTimeout(geo_obj.obj_name + ".set_inline_desc_value(\"content\", " + attrs.m_rank + ");", 10);
        }
        geo_obj.poi_text_value_inline = pop_info.poi_text;
        if (null !== geo_obj.poi_text_value_inline) {
            setTimeout(geo_obj.obj_name + ".set_inline_desc_value(\"text\", " + attrs.m_rank + ");", 10);
        }

    }

    geo_obj.popup.minSize = new OpenLayers.Size(min_width, min_height);
    geo_obj.popup.updateSize();

    feature.popup = geo_obj.popup;
    geo_obj.popup.feature = feature;
    geo_obj.popup.m_rank = attrs.m_rank;
    geo_obj.map.addPopup(geo_obj.popup);

    if (!avoid_rec)
    {
        OpenLayers.HooksLocal.map_check_popup(geo_obj);
    }
};


// selecting a point for popup display, from outer js calls
OpenLayers.HooksPopups.on_map_feature_select = function(geo_object, poi_index)
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

    OpenLayers.HooksPopups.on_feature_select({'feature':feature}, geo_object);

};

// our auxiliary functions to be used inside the hook functions

OpenLayers.HooksLocal = {};

OpenLayers.HooksLocal.on_layer_switch = function(map) {
    var geo_obj = map.geo_obj;

    if (geo_obj.map.baseLayer.name == geo_obj.display_strings.google_map)
    {
        $('.olLayerGoogleCopyright').removeClass('map_hidden');
        $('.olLayerGooglePoweredBy').removeClass('map_hidden');
    }
    else
    {
        $('.olLayerGoogleCopyright').addClass('map_hidden');
        $('.olLayerGooglePoweredBy').addClass('map_hidden');
    }

};

OpenLayers.HooksLocal.redraw_times = {
    time_drag_delay: 500,
    map_dragging_last: 0
};

OpenLayers.HooksLocal.map_center_update = function (geo_obj)
{
    var new_center = geo_obj.map.center.clone();
    geo_obj.map.setCenter(new_center);

    try {
        geo_obj.select_control.destroy();
        geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
        geo_obj.map.addControl(geo_obj.select_control);
        geo_obj.select_control.activate();
    } catch (e) {}

};

OpenLayers.HooksLocal.map_feature_redraw = function(geo_obj) {
    var cur_date = new Date();
    var cur_time = cur_date.getTime();

    var time_delay = OpenLayers.HooksLocal.redraw_times.time_drag_delay;

    if (time_delay <= (cur_time - OpenLayers.HooksLocal.redraw_times.map_dragging_last))
    {
        OpenLayers.HooksLocal.map_center_update(geo_obj);
        OpenLayers.HooksLocal.redraw_times.map_dragging_last = cur_time;
    }

};

// on map click for the (pre)view mode
OpenLayers.HooksLocal.nothing_on_map_click = function(evt, map) {
    if (!map) {return;}
    var geo_obj = map.geo_obj;
    if (!geo_obj) {return;}

    if (geo_obj.select_control) {
        geo_obj.select_control.destroy();
    }
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    map.addControl(map.geo_obj.select_control);
    geo_obj.select_control.activate();

};

// to insert new POI on map click, but not on a click that closes a pop-up
OpenLayers.HooksLocal.new_poi_on_map_click = function(evt, map) {
    var geo_obj = map.geo_obj;

    geo_obj.select_control.destroy();
    geo_obj.select_control = new OpenLayers.Control.SelectFeature(geo_obj.layer);
    map.addControl(map.geo_obj.select_control);
    geo_obj.select_control.activate();

    if (geo_obj.ignore_click) {
        geo_obj.ignore_click = false;
        return true;
    }

    if (geo_obj.ignore_edit_click) {return true;}

    if (evt.onControlDiv) {return true;}

    if (evt['cancelBubble']) {return true;}

    if (undefined !== evt.originalTarget)
    {
        if ("object" != (typeof evt.originalTarget))
        {
            return true;
        }

        if (evt.originalTarget instanceof HTMLSpanElement)
        {
            return true;
        }
        if (evt.originalTarget instanceof HTMLDivElement)
        {
            return true;
        }
    }
    else
    {
        if (undefined !== evt['srcElement'])
        {
            var src_el_rep = evt['srcElement'].toString();
            if ('http' == src_el_rep.substr(0, 4)) {return true;}
        }
    }

    var lonlat = map.getLonLatFromViewPortPx(evt.xy);

    geo_obj.insert_poi('map', lonlat);
};

OpenLayers.HooksLocal.map_check_popup = function(geo_obj) {
    if (!geo_obj) {return;}

    if (geo_obj.popup)
    {
        var feature = geo_obj.popup.feature;
        if (!feature) {return;}
        var attrs = feature.attributes;
        if (!attrs) {return;}

        var test_lonlat = null;
        var aux_center = null;
        if (undefined !== attrs.m_original)
        {
            var view_box = geo_obj.map.calculateBounds();
            test_lonlat = new OpenLayers.LonLat(attrs.m_maplon, attrs.m_maplat);
            if (!view_box.containsLonLat(test_lonlat, true))
            {
                var aux_inds = [attrs.m_original, attrs.m_original + attrs.m_count, attrs.m_original + (2 * attrs.m_count)];
                var poi_features = geo_obj.layer.features;
                var test_features = [poi_features[aux_inds[0]], poi_features[aux_inds[1]], poi_features[aux_inds[2]]];
                var test_count = test_features.length;

                for (var tind = 0; tind < test_count; tind++)
                {
                    feature = test_features[tind];
                    if (!feature) {return;}
                    attrs = feature.attributes;
                    if (!attrs) {return;}

                    test_lonlat = new OpenLayers.LonLat(attrs.m_maplon, attrs.m_maplat);
                    if (view_box.containsLonLat(test_lonlat, true))
                    {
                        aux_center = geo_obj.map.getCenter();
                        OpenLayers.HooksPopups.on_feature_select({"feature":feature}, geo_obj, true);
                        geo_obj.map.setCenter(aux_center);
                        break;
                    }
                }

            }
        }
    }

};

OpenLayers.HooksLocal.map_check_pois = function(geo_obj) {
    if (geo_obj.editing)
    {
        var shifts = [0, -360, 360];
        if (2 > geo_obj.map.getZoom())
        {
            shifts = [0];
        }
        var shifts_count = shifts.length;

        var features = geo_obj.layer.features;
        var features_count = features.length;

        var view_box = geo_obj.map.calculateBounds();

        for (var find = 0; find < features_count; find++)
        {
            var feature = features[find];
            var cur_poi_info = geo_obj.poi_markers[find];
            if (!cur_poi_info) {continue;}

            var inf_lon = cur_poi_info['lon'];
            var inf_lat = cur_poi_info['lat'];
            var map_lon = cur_poi_info['map_lon'];
            var map_lat = cur_poi_info['map_lat'];
            var lonlat = new OpenLayers.LonLat(map_lon, map_lat);
            var inf_lon_move = null;
            var pixel = null;

            for (var sind = 0; sind < shifts_count; sind++)
            {
                var one_shift = shifts[sind];

                inf_lon_move = inf_lon + one_shift;
                lonlat = new OpenLayers.LonLat(inf_lon_move, inf_lat);
                lonlat.transform(new OpenLayers.Projection("EPSG:4326"), geo_obj.map.getProjectionObject());
                if (view_box.containsLonLat(lonlat))
                {
                    pixel = geo_obj.map.getViewPortPxFromLonLat(lonlat);
                    feature.move(pixel);
                    if (geo_obj.popup && (feature == geo_obj.popup.feature)) {
                        pixel = geo_obj.map.getLayerPxFromLonLat(lonlat);
                        geo_obj.popup.moveTo(pixel);
                    }
                    break;
                }

            }

        }
    }
};

// our implementation of the proposed hook functions

OpenLayers.Hooks.LayerSwitcher.layerSwitched = function(ctrl) {
    OpenLayers.HooksLocal.on_layer_switch(ctrl.map);
};

OpenLayers.Hooks.Zooms.maxZoom = function(ctrl) {
    var max_layer_zoom = 18;
    try {
        var google_layer_name = ctrl.map.geo_obj.map_view_layer_names_all[ctrl.map.geo_obj.map_view_layer_google];
        if (ctrl.map.baseLayer.name == google_layer_name) {max_layer_zoom = 19;}
    } catch (e) {}

    return max_layer_zoom;
};

OpenLayers.Hooks.Zooms.minZoom = function(ctrl) {
    return 0;
};

OpenLayers.Hooks.PanZoom.buttonDown = function(ctrl) {
    try {
        OpenLayers.HooksLocal.map_feature_redraw(ctrl.map.geo_obj);
    } catch (e) {}
};

OpenLayers.Hooks.PanZoomBar.divClick = function(ctrl) {
    // this change works for firefox under winxp too, but not for linux/firefox
    if ('msie' == OpenLayers.Util.getBrowserName())
    {
        try {
            ctrl.map.geo_obj.ignore_click = true;
        } catch (e) {}
        return false;
    }
    return true;
};

OpenLayers.Hooks.PanZoomBar.buttonDown = function(ctrl) {
    try {
        OpenLayers.HooksLocal.map_feature_redraw(ctrl.map.geo_obj);
    } catch (e) {}
};

OpenLayers.Hooks.PanZoomBar.zoomBarUp = function(ctrl) {
    // this change works for firefox under winxp too, but not for linux/firefox
    if ('msie' == OpenLayers.Util.getBrowserName())
    {
        try {
            ctrl.map.geo_obj.ignore_click = true;
            if (ctrl.map.geo_obj.obj_name) {
                setTimeout("try {" + ctrl.map.geo_obj.obj_name + ".ignore_click = false;} catch (e) {}", 500);
            }
        } catch (e) {}
        return false;
    }
    return true;
};

OpenLayers.Hooks.DragPan.panMap = function(ctrl) {
    OpenLayers.HooksLocal.map_feature_redraw(ctrl.map.geo_obj);
};

OpenLayers.Hooks.DragPan.panMapDone = function(ctrl) {
    OpenLayers.HooksLocal.map_center_update(ctrl.map.geo_obj);
    OpenLayers.HooksLocal.map_check_popup(ctrl.map.geo_obj);
};

