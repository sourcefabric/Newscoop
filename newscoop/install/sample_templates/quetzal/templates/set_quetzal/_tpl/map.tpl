<section class="span4 hidden-tablet">
    <!-- MAP WIDGET -->
    <div class="widget-map">
        <header class="widget-wrap">
            <h4>{{ #newsNearYou# }}</h4>
        </header>
        <div class="widget-wrap">
            <figure class="map">
            {{* All possible constraints for setting the map you can see here:
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
                show_reset_link=false
                area_show="focus"
                width="100%"
                height="250"
                show_open_link=true
                open_map_on_click=false
                popup_width="1000"
                popup_height="750"
                max_zoom=15
                map_margin=20
                area_show="focus"
            }}
            </figure>
            <div class="badges">
                {{ list_map_locations }}
                {{ assign var="latitude" value=$gimme->location->latitude }}
                {{ assign var="longitude" value=$gimme->location->longitude }}
                {{* If for example you want to show articles close to current location,
                rectangle in list_articles can be specified like 
                location="$latitude-1/60 $longitude-1/60,$latitude+1/60 $longitude+1/60"
                that should return all articles geolocated 1 minute or closer to the location.
                Details about list_articles are here:
                https://wiki.sourcefabric.org/display/NsLingo/List+of+Articles *}}
                <a class="map-badge" href="#myModal{{ $gimme->current_list->index }}" role="button" data-toggle="modal">{{ $gimme->location->name }} <i class="icon-center"></i></a>
                <div id="myModal{{ $gimme->current_list->index }}" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="myModalLabel">{{ #latestNewsFrom# }} {{ $gimme->location->name }}</h3>
                    </div>
                    <div class="modal-body">
                        {{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bypublishdate desc" location="$latitude $longitude, $latitude $longitude" constraints="type is news" }}
                        <article class="section-article">
                            <figure class="pull-left article-image">
                                <a href="{{ uri options="article" }}">
                                    {{ include file='_tpl/img/img_202x152.tpl'}} 
                                    {{ include file='_tpl/img/img_225x150.tpl'}} 
                                </a>
                            </figure>
                            <header>
                                <h2><a href="{{ uri options="article" }}">{{$gimme->article->name}}</a></h2>
                            </header>
                            <div class="article-excerpt">
                                {{ $gimme->article->full_text|truncate:200:"...":true}}
                            </div>  
                            <div class="article-links">
                                <a href="{{ uri options="article" }}" class="link-color">Read more +</a>
                            </div>
                            <div class="clearfix"></div>
                        </article>
                        {{ /list_articles }} 
                    </div>
                </div>
                {{ /list_map_locations }}
            </div>
        </div>

    </div>
</section>
