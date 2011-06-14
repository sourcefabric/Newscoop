    <div class="widget block">
        <h3>Dynamic Map</h3>

{{ set_map
    label="Europe in the section"
    areas="polygon 73.174784 25.608215; 66.859571 -27.829285; 35.314228 -15.876160; 28.146112 47.053528; 39.092978 52.326965; 70.079252 45.647278;"
    sections="_current"
    area_exact=true
    max_points=1000
}}

{{ map
    show_locations_list=true
    show_reset_link="Show initial Map"
    width="300"
    height="300"
    show_open_link="Pop-up the map"
    open_map_on_click=false
    popup_width="1000"
    popup_height="750"
    max_zoom=15
    map_margin=20
    area_show="focus_empty"
}}

<div class="dynamic_map_articles_list">
<ul>Map Articles
{{ local }}
{{ list_map_articles length="200" }}
        <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>
{{ /list_map_articles }}
{{ /local }}
</ul>
</div>

<div class="dynamic_map_locations_list">
<ul>Map Locations
{{ list_map_locations length="200" }}
    {{ if $gimme->location->enabled }}
        <li class="geo_poi_list_name_pos">{{ $gimme->location->name }} ({{ $gimme->location->longitude }}, {{ $gimme->location->latitude }})</li>
        {{ if $gimme->location->multimedia }}
        <ul>POI multimedia
        {{ foreach from=`$gimme->location->multimedia` item=multimediaitem }}
            <li>type: {{ $multimediaitem->type }}</li>
            {{ if "video" == $multimediaitem->type }}
            <li>provider: {{ $multimediaitem->spec }}</li>
            {{ /if }}
            <li>source: {{ $multimediaitem->src }}</li>
            {{ if "0" != $multimediaitem->width }}
            <li>width: {{ $multimediaitem->width }}</li>
            {{ /if }}
            {{ if "0" != $multimediaitem->height }}
            <li>height: {{ $multimediaitem->height }}</li>
            {{ /if }}
        {{ /foreach }}
        </ul>
        {{ /if }}
    {{ /if }}
{{ /list_map_locations }}
</ul>
</div>

{{ unset_map }}

    </div>
