OpenLayers.Layer.MapQuest = OpenLayers.Class(OpenLayers.Layer.OSM, {
    name: "MapQuest",
    attribution: "Data CC-By-SA by <a href='http://openstreetmap.org/' target='_blank'>OpenStreetMap</a>. Rendered by <a href='http://open.mapquest.co.uk/' target='_blank'>MapQuest</a>.",
    sphericalMercator: true,
    //url: 'http://otile1.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
    url: [
        'http://otile1.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
        'http://otile2.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
        'http://otile3.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
        'http://otile4.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png'
    ],
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
    clone: function(obj) {
        if (obj == null) {
            obj = new OpenLayers.Layer.MapQuest(
                this.name, this.url, this.getOptions());
        }
        obj = OpenLayers.Layer.OSM.prototype.clone.apply(this, [obj]);
        return obj;
    },
    CLASS_NAME: "OpenLayers.Layer.MapQuest"
});

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

OpenLayers.Util.map_center_update = function (geo_obj)
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

OpenLayers.Util.redraw_times = {
    time_drag_delay: 500,
    map_dragging_last: 0
};

OpenLayers.Util.map_feature_redraw = function(geo_obj) {
    var cur_date = new Date();
    var cur_time = cur_date.getTime();

    var time_delay = OpenLayers.Util.redraw_times.time_drag_delay;

    if (time_delay <= (cur_time - OpenLayers.Util.redraw_times.map_dragging_last))
    {
        OpenLayers.Util.map_center_update(geo_obj);
        OpenLayers.Util.redraw_times.map_dragging_last = cur_time;
    }

};

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

        // this change works for firefox under winxp too, but not for linux/firefox
        if ('msie' == OpenLayers.Util.getBrowserName())
        {
            try {
                this.map.geo_obj.ignore_click = true;
            } catch (e) {}
        }
        else
        {
            OpenLayers.Event.stop(evt);
        }

	},

    buttonDown: function(evt) {

        try {
            OpenLayers.Util.map_feature_redraw(this.map.geo_obj);
        } catch (e) {}

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

            var max_layer_zoom = 18;
            try {
                var google_layer_name = this.map.geo_obj.map_view_layer_names_all[this.map.geo_obj.map_view_layer_google];
                if (this.map.baseLayer.name == google_layer_name) {max_layer_zoom = 19;}

/*
                if (this.geo_obj)
                {
                    google_layer_name = this.geo_obj.map_view_layer_names_all[this.geo_obj.map_view_layer_google];
                    if (this.map.baseLayer.name == google_layer_name) {max_layer_zoom = 19;}
                }
                else
                {
                    google_layer_name = geo_locations.map_view_layer_names_all[geo_locations.map_view_layer_google];
                    if (geo_locations.map.baseLayer.name == google_layer_name) {max_layer_zoom = 19;}
                }
*/
            } catch (e) {}
            if (max_layer_zoom < zoomLevel) {zoomLevel = max_layer_zoom;}
            if (0 > zoomLevel) {zoomLevel = 0;}

            this.map.zoomTo(zoomLevel);
            this.mouseDragStart = null;
            this.zoomStart = null;

            // this change works for firefox under winxp too, but not for linux/firefox
            if ('msie' == OpenLayers.Util.getBrowserName())
            {
                try {
                    this.map.geo_obj.ignore_click = true;
                } catch (e) {}
                setTimeout("try {geo_locations.ignore_click = false;} catch (e) {}", 500);
            }
            else
            {
                OpenLayers.Event.stop(evt);
            }
        }
    },

    CLASS_NAME: "OpenLayers.Control.PanZoomBarMod"
});

OpenLayers.Control.DragPanMod = OpenLayers.Class(OpenLayers.Control.DragPan, {
    //drag_map.panMapDone = geo_hook_map_dragged; // OpenLayers.Util.map_center_update(geo_obj);
    //drag_map.panMap = geo_hook_map_dragging;

    panMapDone: function(pixel) {
        OpenLayers.Util.map_center_update(this.geo_obj);
        //geo_hook_map_dragged(geo_obj, pixel)};
    },

    //drag_map.panMap = function(xy) {geo_hook_map_dragging(drag_map, geo_obj, xy)};

    panMap: function(xy) {
        this.panned = true;

        this.map.pan(this.handler.last.x - xy.x, this.handler.last.y - xy.y, {dragging: this.handler.dragging, animate: false});

        OpenLayers.Util.map_feature_redraw(this.geo_obj);
    },

    CLASS_NAME: "OpenLayers.Control.DragPanMod"
});


