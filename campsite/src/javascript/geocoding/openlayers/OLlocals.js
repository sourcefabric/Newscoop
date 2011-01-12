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
    //var match_res = this.src.match(/^http:\/\/otile([1-4])\.mqcdn\.com\/tiles\/1\.0\.0\/osm\/([\-\d]+)\/([\-\d]+)\/([\-\d]+)/);
    var match_res = this.src.match(/^http:\/\/otile([1-4])\.mqcdn\.com\//);
    if (match_res) {
        if (!this._attempts) {this._attempts = 0;}
        var mqm_base_start = "http://otile";
        var mqm_base_middle = ".mqcdn.com/tiles/1.0.0/osm/";

/*
        var t_val = parseInt(match_res[1]);
        var z_val = parseInt(match_res[2]);
        var x_val = parseInt(match_res[3]);
        var y_val = parseInt(match_res[4]);

        var z_mod = Math.pow(2, z_val);
        while (x_val < 0) {
            x_val += z_mod;
        }
        while (y_val < 0) {
            y_val += z_mod;
        }
        while (x_val >= z_mod) {
            x_val -= z_mod;
        }
        while (y_val >= z_mod) {
            y_val -= z_mod;
        }

        var mqm_base_str = mqm_base_start + t_val + mqm_base_middle;
        var mqm_base_len = mqm_base_str.length;
        this.src = this.src.substring(0, mqm_base_len) + "" + z_val + "/" + x_val + "/" + y_val + ".png";
*/

        var mq_start = mqm_base_start + Math.floor(1 + (4 * Math.random())) + mqm_base_middle;
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


