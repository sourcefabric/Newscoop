        <!-- FOOTER -->
        <footer id="main-footer">
            <div class="container">                
                <!-- FOOTER INFO -->
                <div class="row footer-info">

                    <div class="span3 about-us right-separator visible-desktop">
                        <h4>{{ #aboutUs# }}</h4>
                        <p>
                            Developed and used by news organisations like Switzerland’s TagesWoche, ElPeriódico de Guatemala and Yemen Times, Newscoop 4 aims to help independent news organisations manage online publications, enrich content and find new audiences.<br>
                            <a href="/en/static/pages/95/About-us.htm" class="link-color">{{ #moreAboutUs# }}</a>
                        </p>
                    </div>
                    <div class="span4 categories right-separator visible-desktop">
                        <h4>{{ #sections# }}</h4>
                        <div class="row">
                            <div class="span2">
                                <ul>
                                    {{ local }}
                                    {{ set_current_issue }}
                                    {{ list_sections }}
                                    <li><a href="{{ url options='section' }}" title="{{ $gimme->section->name }}"> {{ $gimme->section->name }}</a></li>
                                    {{ /list_sections }}
                                    {{ /local }}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="span3 more-links right-separator visible-desktop">
                        <h4>{{ #moreLinks# }}</h4>
                        <ul>
                            {{ list_articles ignore_issue="true" ignore_section="true" constraints="type is page" }}
                            <li><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></li>
                            {{ /list_articles }}
                            <li><a target="_blank" href="http://twitter.com/sourcefabric ">{{ #followUs# }}</a></li>
                            <li><a target="_blank"href="http://facebook.com/sourcefabric ">{{ #beOurFan# }}</a></li>
                            <li><a href="/en/static/rss">{{ #subscribeToOurFeed# }}</a></li> 
                            <li><a href="/?tpl=6">{{ #archive# }}</a></li>                           
                        </ul>
                    </div>
                    <div class="span2 contact-us visible-desktop">
                        <h4>{{ #contactUs# }}</h4>
                        <div class="info">
                            <span class="link-color">{{ #email# }}</span>
                            contact@sourcefabric.org 
                        </div>

                        <div class="info">
                            <span class="link-color">{{ #address# }}</span>
                            Salvátorská 10, 110 00 Praha 1, Czech Republic
                        </div>

                        <div class="info">
                            <span class="link-color">{{ #phone# }}</span>
                             +420 222 362 540
                        </div>
                    </div>

                    {{ if $gimme->section->name }}
                    <!-- FOOTER TABLET VISIBLE HOME ONLY -->
                    <div class="span12 visible-tablet tablet-map">
                        <div class="widget-map">                            
                            <h4 class="widget-wrap">{{ #newsNearYou# }}</h4>                            
                            <section class="widget-wrap">
                                <figure class="map">
                                {{* All possible constraints for setting the map you can see here:
                                https://wiki.sourcefabric.org/display/CS/Dynamic+maps+-+Constraints *}}
                                {{ set_map
                                    label="Latest locations"
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
                                    max_zoom=16
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
                                    <a class="map-badge" href="#myModal-footer{{ $gimme->current_list->index }}" role="button" data-toggle="modal">{{ $gimme->location->name }} <i class="icon-center"></i></a>
                                    <div id="myModal-footer{{ $gimme->current_list->index }}" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h3 id="myModalLabel">Latest news from {{ $gimme->location->name }}</h3>
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
                            </section>
                            <div class="widget-footer">
                                <hr>
                                <div class="widget-wrap">
                                    <a href="#topnav" class="btn btn-red pull-right">{{ #backToTop# }}</a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ /if }}

                    <!-- FOOTER MOBILE VISIBLE -->
                    <div class="span12 visible-phone action-buttons">
                        <a href="#topnav" class="btn btn-large btn-red">{{ #backToTop# }}</a>
                    </div>
                </div>

                <!-- FOOTER BRAND AND COPYRIGHT -->
                <div class="row footer-brand">
                    <div class="span12">
                        <a href="/" class="visible-desktop">
                            <img class="logo-footer" src="{{ url static_file='_img/newscoop-quetzal-logo-footer.png' }}" alt="{{$gimme->publication->name}}">
                        </a>
                        {{ #copyrightMessage# }}
                    </div>
                </div>
            </div>
        </footer>
