{{* Dynamic map can collect POIs from more articles based on different criteria *}}

<div class="row clearfix">
<script type="text/javascript">
$(document).ready(function(){
  $('.collapsible').click(function() {
    $(this).next().toggle('fast');
    return false;
  }).next().hide();
});

$(document).ready(function(){
  $(".collapsible h6").click(function () {
      $(this).toggleClass("active");
  });
});
</script>
<h3>{{ #dynamicMap# }}</h3>
<div class="eightcol">

{{* All possible constraints for setting the map can seen here:
https://wiki.sourcefabric.org/display/CS/Dynamic+maps+-+Constraints *}}
{{ set_map
    label="{{ #latestLocations# }}"
    issues="_current"
    max_points=5
}}

{{* Options for displaying the map are described here:
https://wiki.sourcefabric.org/display/CS/Dynamic+maps+-+Display *}}
{{ map
    show_locations_list=false
    show_reset_link=true
    area_show="focus"
    width="754"
    height="250"
    show_open_link=true
    open_map_on_click=false
    popup_width="1000"
    popup_height="750"
    max_zoom=15
    map_margin=20
    area_show="focus"
}}
</div>

{{* In this example, we want to show latest 5 locations from current issue,
and then to make a list of these five locations with max 3 belonging articles (those 
that are geo-located to specific locations *}}
<div class="fourcol last location-list">
    <h4>{{ #articlesLatestLocations# }}</h4>
    {{ list_map_locations }}
    {{ assign var="latitude" value=$gimme->location->latitude }}
    {{ assign var="longitude" value=$gimme->location->longitude }}
    <div class="collapsible"><h6><i></i>{{ $gimme->location->name }}</h6></div>
    
    {{* If for example you want to show articles close to current location,
    rectangle in list_articles can be specified like 
    location="$latitude-1/60 $longitude-1/60,$latitude+1/60 $longitude+1/60"
    that should return all articles geolocated 1 minute or closer to the location.
    Details about list_articles are here:
    https://wiki.sourcefabric.org/display/NsLingo/List+of+Articles *}}
    
    {{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bypublishdate desc" location="$latitude $longitude, $latitude $longitude" constraints="type is news" }} 
    {{ if $gimme->current_list->at_beginning }}
    <div class="location-content">
        <ul>{{ /if }}
        <li><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></li>
        {{ if $gimme->current_list->at_end }}</ul>
    </div>{{ /if }}
    {{ /list_articles }}
    
    {{ /list_map_locations }}
</div>
</div>