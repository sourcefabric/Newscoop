    <div class="dynamic_map">
        <h3>Aggregated Map</h3>
<constraint_examples
    authors="_current"
    articles="82,83,84"
    issues="_current"
    sections="_current"
    topics="_current"
    areas="rectangle 20 0; 60 100;"
>

{{ set_map
    label="some display string"
    topics="_current"
    areas="rectangle -20 175; 60 40;"
    max_points=1000
}}

{{ map show_locations_list=true show_reset_link="Show initial Map" width="550" height="450" show_open_link="Pop-up the map" open_map_on_click=false }}

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
    {{ /if }}
{{ /list_map_locations }}
</ul>
</div>

{{ unset_map }}

    </div>
