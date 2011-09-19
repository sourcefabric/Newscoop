<link rel="stylesheet" href="{{ url static_file='_css/dynmap.css' }}" type="text/css" media="screen">
{{ assign var="curtop" value=$gimme->topic->name }}
<div id="dynmap">
{{ set_map
    label="Points from this issue articles"
    topics=$curtop
    max_points=100
}}

{{ assign var="oneorzero" value=0 }}
{{ list_map_locations length="1" }}
{{ if $gimme->current_list->at_beginning }}
{{ assign var="oneorzero" value=1 }}
{{ /if }}
{{ /list_map_locations }}

{{ if $oneorzero == 1 }}
{{ map
    show_locations_list=true
    show_reset_link=false
    show_open_link="Pop-up the map"
    open_map_on_click=false
    width="480"
    height="240"    
    popup_width="1000"
    popup_height="750"
    max_zoom=15
    map_margin=10
    area_show="focus"
}}
{{ /if }}

{{ unset_map }}
</div><!-- /#dynmap -->