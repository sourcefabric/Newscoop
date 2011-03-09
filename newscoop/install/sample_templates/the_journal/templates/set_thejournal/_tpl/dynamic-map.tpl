    <div class="widget block">
        <h3>Aggregated Map</h3>
<constraint_examples
    authors="running"
    articles="82,83,84"
    issues="running"
    sections="running"
    topics="running"
    areas="rectangle 20 0; 60 100;"
>

{{ set_map
    label="some display string"
    topics="running"
    areas="rectangle 20 0; 60 100;"
}}

{{ map show_locations_list="true" show_reset_link="Show initial Map" width="300" height="450" }}

<div>
<ul><b>Map Articles</b>
{{ local }}
{{ list_map_articles }}
        <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>
{{ /list_map_articles }}
{{ /local }}
</ul>
</div>

<div>
<br /><ul><b>Map Locations</b>
{{ list_locations }}
    {{ if $gimme->location->enabled}}
        <li>{{ $gimme->location->name }} ({{ $gimme->location->longitude }}, {{ $gimme->location->latitude }})</li>
    {{ /if }}
{{ /list_locations }}
</ul>
</div>

{{ unset_map }}

    </div>
