OpenLayers.Layer.MapQuest = OpenLayers.Class(OpenLayers.Layer.OSM, {
    name: "MapQuest",
    attribution: "Data CC-By-SA by <a href='http://openstreetmap.org/' target='_blank'>OpenStreetMap</a>. Rendering &copy; 2010 <a href='http://mapquest.com' target='_blank'>MapQuest</a>.",
    sphericalMercator: true,
    url: 'http://otile1.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png',
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

/*
OpenLayers.Layer.MapQuest2 = OpenLayers.Class(OpenLayers.Layer.OSM, {
    initialize: function(name, options) {
        options = OpenLayers.Util.extend({
            attribution: "Data CC-By-SA by <a href='http://openstreetmap.org/' target='_blank'>OpenStreetMap</a>. Rendering &copy; 2010 <a href='http://mapquest.com' target='_blank'>MapQuest</a>.",
            maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
            maxResolution: 156543.0339,
            units: "m",
            projection: "EPSG:900913",
			isBaseLayer: true,
			numZoomLevels: 19,
			displayOutsideMaxExtent: true,
			wrapDateLine: true,
			styleId: 1
        }, options);
		var prefix = [options.key, options.styleId, 256].join('/') + '/';
        var url = [
            "http://otile1.mqcdn.com/tiles/1.0.0/" + prefix,
            "http://otile2.mqcdn.com/tiles/1.0.0/" + prefix,
            "http://otile3.mqcdn.com/tiles/1.0.0/" + prefix,
            "http://otile4.mqcdn.com/tiles/1.0.0/" + prefix
        ];
        var newArguments = [name, url, options];
        OpenLayers.Layer.TMS.prototype.initialize.apply(this, newArguments);
    },

    getURL: function (bounds) {
        var res = this.map.getResolution();
        var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
        var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
        var z = this.map.getZoom();
        var limit = Math.pow(2, z);

        if (y < 0 || y >= limit)
        {
            return "http://cloudmade.com/js-api/images/empty-tile.png";
        }
        else
        {
            x = ((x % limit) + limit) % limit;

            var url = this.url;
            var path = z + "/" + x + "/" + y + ".png";

            if (url instanceof Array)
            {
                url = this.selectUrl(path, url);
            }

            return url + path;
        }
    },

    CLASS_NAME: "OpenLayers.Layer.MapQuest2"
});
*/
