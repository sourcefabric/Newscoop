<!-- SIDEBAR -->
<aside class="span4 internal-sidebar sidebar">
    
    <!-- ADVERTISEMENTS -->
    <section class="advertisements visible-desktop">
        <a href="http://www.sourcefabric.org/" target="_blank"><img src="{{ url static_file='_img/sourcefabric-336x280.png' }}"></a>
    </section>

    <!-- RELATED NEWS WIDGET -->

    {{ list_related_articles }}
    {{ if $gimme->current_list->at_beginning }} 
    <section class="widget-more-news visible-desktop">
        <header>
            <h4>{{ #moreNewsLikeThis# }}</h4>
        </header>
        <div class="widget-wrap">
            <div class="widget-content">
                <div class="visible-desktop">
                    {{/if}}
                    <div class="news-box">
                        {{ include file='_tpl/img/img_70x45.tpl' where='aside'}}
                        <div class="pull-right newtitle">
                            <a href="{{ uri options="article"}}">{{ $gimme->article->name }}</a>
                        </div>
                        <div class="clearfix divider"></div>
                        <div class="newdata">
                            <span>{{ $gimme->article->comment_count }} {{ #comments# }}</span> | 
                            <a href="{{ uri options="article"}}" class="link-color">{{ #readMore# }}</a>
                            <span class="date pull-right"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time></span>
                        </div>
                    </div>
                    {{ if $gimme->current_list->at_end }}
                </div>
            </div>
        </div>
    </section>
    {{/if}}
    {{ /list_related_articles}}

    {{ list_related_articles }}
    {{ if $gimme->current_list->at_beginning }} 
    <section class="widget-more-news hidden-tablet hidden-desktop">
        <header>
            <h4>{{ #moreNewsLikeThis# }}</h4>
        </header>
        <div class="widget-wrap">
            <div class="news-content">
                <div class="more-like-this-phone visible-phone">
                    {{/if}}
                    <article>
                        <figure class="article-image">
                            <a href="{{ uri options="article"}}">
                                {{ include file='_tpl/img/img_130x98.tpl' }}
                            </a>
                        </figure>
                        <section class="article-content">
                            <span class="link-color">{{ $gimme->article->section }}</span>
                            <header>
                                <h2><a href="{{ uri options="article"}}">{{ $gimme->article->name }}</a></h2>
                                <span class="article-date"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time></span>
                            </header>
                            <div class="clearfix"></div>
                        </section>
                    </article>
                    {{ if $gimme->current_list->at_end }}
                </div>
            </div>
        </div>
    </section>
    {{/if}}
    {{ /list_related_articles}}

    {{ if $gimme->article->type_name == "debate" }}
    {{ include file="_tpl/debate-voting.tpl" }}
    {{ else }}
    <!-- MAP WIDGET -->
    {{ if $gimme->article->has_map }} 
    <section class="widget-map hidden-tablet">
        <header class="widget-wrap">
            <h4>{{ #newsNearYou# }}</h4>
        </header>
        <section class="widget-wrap">        
            <figure class="map">
                {{ map show_locations_list="false" show_reset_link="false" width="100%" height="250" }}
            </figure>   
        </section>
        <footer>
            <hr>
        </footer>
    </section>
    {{ /if }}
    {{ /if }}
    
</aside>
