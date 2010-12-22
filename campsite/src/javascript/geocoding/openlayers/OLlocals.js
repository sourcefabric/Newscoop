OpenLayers.Layer.MapQuest = OpenLayers.Class(OpenLayers.Layer.OSM, {
    name: "MapQuest",
    attribution: "Data CC-By-SA by <a href='http://openstreetmap.org/' target='_blank'>OpenStreetMap</a>. Rendering by <a href='http://open.mapquest.co.uk/' target='_blank'>MapQuest</a>.",
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

