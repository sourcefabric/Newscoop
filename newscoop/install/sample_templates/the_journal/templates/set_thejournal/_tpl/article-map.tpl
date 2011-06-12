    <div class="widget block">
        <h3>Map</h3>

{{ map show_locations_list=true show_reset_link="Show initial Map" width="300" height="250" auto_focus=null show_open_link=true open_map_on_click=true max_zoom=12 }}


<ul>
{{ list_article_locations }}
    <li>{{ $gimme->location->name }} ({{ $gimme->location->longitude }}, {{ $gimme->location->latitude }})</li>
{{ /list_article_locations }}
</ul>
    </div>
