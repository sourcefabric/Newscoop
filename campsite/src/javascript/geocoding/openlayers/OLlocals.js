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

OpenLayers.IMAGE_RELOAD_ATTEMPTS = 40;
OpenLayers.Util.originalOnImageLoadError = OpenLayers.Util.onImageLoadError;
OpenLayers.Util.onImageLoadError = function() {
    if (this.src.match(/^http:\/\/otile[1-4]\.mqcdn\.com\//)) {
        if (!this._attempts) {this._attempts = 0;}
        var mq_start = "http://otile" + Math.floor(1 + (4 * Math.random())) + ".mqcdn.com/tiles/1.0.0/osm/";
        if ((4 + this._attempts) > OpenLayers.IMAGE_RELOAD_ATTEMPTS)
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


