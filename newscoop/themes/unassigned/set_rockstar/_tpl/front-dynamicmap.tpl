{{* All possible constraints for setting the map you can see here:
https://wiki.sourcefabric.org/display/CS/Dynamic+maps+-+Constraints *}}
{{ set_map
    label="{{ #latestLocations# }}"
    issues="_current"
    max_points=5
}}            
            <div class="title">
            	<h2>{{ #hotSpots# }}</h2>
            </div>            
            
            <div class="event-map">
{{* Options for displaying the map are described here:
https://wiki.sourcefabric.org/display/CS/Dynamic+maps+-+Display *}}
{{ map
    show_locations_list=false
    show_reset_link=false
    area_show="focus"
    width="930"
    height="275"
    show_open_link=true
    open_map_on_click=false
    popup_width="1000"
    popup_height="750"
    max_zoom=15
    map_margin=20
    area_show="focus"
}}            
            	<p class="left">{{ #latestLocations# }}</a></p>
                <p class="right">{{ list_map_locations }}{{ $gimme->location->name }}{{ if !($gimme->current_list->at_end) }}  +  {{ /if }}{{ /list_map_locations }}</p>
            
            </div>