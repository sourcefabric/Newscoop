<div class="span8 home-featured-news section-articles">
    <!-- SECTION ARTICLES -->                                
    <div class="row section-articles">
    {{ list_articles length="5" ignore_issue="true" constraints="type not poll" }}
        {{ if $gimme->current_list->at_beginning }}            

        <article class="span8 section-article section-featured">                                        
            <figure class="pull-left article-image">
                <a href="{{ uri options="article" }}">
                    {{ include file='_tpl/img/img_330x215.tpl'}} 
                </a>
            </figure>
            <span class="link-color">{{ $gimme->article->section->name }}</span>
            {{ if !$gimme->article->content_accessible }}
            <span class="label label-important normal-weight">{{ #premium# }}</span>
            {{ /if }}
            <header>
                <h2><a href="{{ uri option='article'}}">{{ $gimme->article->name }}</a></h2>
                <span class="article-date"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time> </span>
            </header>
            <div class="article-excerpt hidden-phone">
                {{ $gimme->article->full_text|truncate:250:"...":true }}
            </div>  
            <div class="article-links hidden-phone">
                <hr>
                <a href="{{ uri options="article" }}#comments" class="comments-link">{{ $gimme->article->comment_count }} {{ #comments# }}</a> | <a href="{{ uri options="article" }}" class="link-color">{{ #readMore# }}</a>
            </div>                                        
            <div class="clearfix"></div>
        </article>
        {{ else }}

        <article class="span8 section-article">
            <figure class="pull-left article-image">
                <a href="{{ uri options="article" }}">
                    {{ include file='_tpl/img/img_202x152.tpl'}} 
                    {{ include file='_tpl/img/img_225x150.tpl'}} 
                </a>
            </figure>
            <span class="link-color">{{ $gimme->article->section->name }}</span>
            {{ if !$gimme->article->content_accessible }}
            <span class="label label-important normal-weight">{{ #premium# }}</span>
            {{ /if }}
            <header>
                <h2><a href="{{ uri option='article'}}">{{ $gimme->article->name }}</a></h2>
                <span class="article-date"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time> </span>
            </header>
            <div class="article-excerpt hidden-phone">
                {{ $gimme->article->full_text|truncate:190:"...":true}}
            </div>  
            <div class="article-links hidden-phone">
                <hr>
                <a href="{{ uri options="article"}}#comments" class="comments-link">{{ $gimme->article->comment_count }} {{ #comments# }}</a> | <a href="{{ uri options="article" }}" class="link-color">{{ #readMore# }}</a>
            </div>
            <div class="clearfix"></div>
        </article>
        {{ /if}}

        {{ if $gimme->current_list->at_end }}            

        {{* PAGINATION *}}
        {{ $pages=ceil($gimme->current_list->count/5) }}
        {{ $curpage=intval($gimme->url->get_parameter($gimme->current_list_id())) }}
        {{ if $pages gt 1 }}
        <nav class="span8">
            <div class="pagination">
                <ul>
                    {{ if $gimme->current_list->has_previous_elements }}<li class="prev"><a href="{{ uripath options="section" }}?{{ urlparameters options="previous_items" }}">&laquo;</a></li>{{ /if }}
                    {{ for $i=0 to $pages - 1 }}
                        {{ $curlistid=$i*5 }}
                        {{ $gimme->url->set_parameter($gimme->current_list_id(),$curlistid) }}
                        {{ if $curlistid != $curpage }}
                    <li><a href="{{ uripath options="section" }}?{{ urlparameters }}">{{ $i+1 }}</a></li>
                        {{ else }}
                    <li class="active disable"><a href="{{ uripath options="section" }}?{{ urlparameters }}">{{ $i+1 }}</a></li>
                        {{ $remi=$i+1 }}
                        {{ /if }}
                    {{ /for }}
                    {{ if $gimme->current_list->has_next_elements }}<li class="next"><a href="{{ uripath options="section" }}?{{ urlparameters options="next_items" }}">&raquo;</a></li>{{ /if }}
                </ul>
            </div>
        </nav>
        {{ $gimme->url->set_parameter($gimme->current_list_id(),$curpage) }}
        {{ /if }}

        {{ /if }}

        {{ /list_articles }}    
    </div>
</div>
