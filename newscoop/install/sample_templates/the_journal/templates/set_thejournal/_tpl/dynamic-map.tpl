    <div class="widget block">
        <h3>Aggregated Map</h3>
<constraint_examples
    authors="_current"
    authors="James Q. Reporter"
    articles="82,83,84"
    articles="64,65"
    topics="Local News"
    topics="Local News, Funny"
    issues="_current"
    issues="13"
    sections="_current"
    sections="business"
    topics="_current"
    topics="Local News, Funny"
    topics="Geography:en, Fashion:en"
    match_all_topics=false
    match_any_topic=true
    area="rectangle 20 0; 60 100;"
    areas="polygon 60.821620 -13.788758; 60.172394 40.000305; 38.990583 43.515930; 38.785348 24.619445; 40.877387 -13.788758;"
    areas="polygon 61.667199 13.457336; 56.822828 -6.405945; 42.711907 -12.997742; 36.911690 -5.175476; 38.027757 40.527649; 51.533693 48.437805; 60.949910 45.976867;"
    areas="polygon 61.247252 16.972961; 60.172394 44.306945; 48.717424 43.867492; 49.920460 17.676086; polygon 48.543160 0.625305; 51.094208 20.312805; 43.290401 18.730774; 38.579520 11.523742; 42.062752 5.459289;"
    areas="polygon 61.667199 13.457336; 56.822828 -6.405945; 42.711907 -12.997742; 36.911690 -5.175476; 38.027757 40.527649; 51.533693 48.437805; 60.949910 45.976867; polygon 61.247252 16.972961; 60.172394 44.306945; 48.717424 43.867492; 49.920460 17.676086;"
    date="2010-12-24"
    start_date="2010-12-24"
    end_date="2010-12-25"
    area_match="union"
    area_match="intersection"
    icons="marker-green.png, marker-blue.png"
    has_multimedia=true
>

{{ set_map
    label="some display string"
    has_multimedia=true
    max_points=1000
}}

{{ map show_locations_list=true show_reset_link="Show initial Map" width="300" height="450" show_open_link="Pop-up the map" }}

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
    {{ if $gimme->location->enabled}}
        <li>{{ $gimme->location->name }} ({{ $gimme->location->longitude }}, {{ $gimme->location->latitude }})</li>
        <ul>POI multimedia
        {{ foreach from=`$gimme->location->multimedia` item=multimediaitem }}
            <li>{{ $multimediaitem->src }}</li>
            <li>{{ $multimediaitem->type }}</li>
            <li>{{ $multimediaitem->spec }}</li>
            <li>{{ $multimediaitem->width }}</li>
            <li>{{ $multimediaitem->height }}</li>
        {{ /foreach }}
        </ul>
    {{ /if }}
{{ /list_map_locations }}
</ul>
</div>

{{ unset_map }}

    </div>
