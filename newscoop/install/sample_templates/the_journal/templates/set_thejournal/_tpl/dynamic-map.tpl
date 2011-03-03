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
    topics="running"
    areas="rectangle 20 0; 60 100;"
}}

{{ map show_locations_list="true" show_reset_link="Show initial Map" width="300" height="450" }}

{{ list_locations }}
    {{ if $gimme->location->enabled}}
        <li>{{ $gimme->location->name }} ({{ $gimme->location->longitude }}, {{ $gimme->location->latitude }})</li>
    {{ /if }}
{{ /list_locations }}
{{ unset_map }}

    </div>
