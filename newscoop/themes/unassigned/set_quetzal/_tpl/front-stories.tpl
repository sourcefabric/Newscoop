<div class="span8 home-featured-news">
{{ list_playlist_articles name="Front page" length="4" }}   
{{ if $gimme->current_list->at_beginning }}
    <div class="row">
        <article class="span8 most-prominent">
            <figure class="pull-left article-image">
                <a href="{{uri option='article'}}">
                    {{ include file='_tpl/img/img_202x152.tpl'}}
                    {{ include file='_tpl/img/img_225x150.tpl'}}
                </a>
            </figure>                                        
            <header>
                <span class="link-color">{{ $gimme->article->section->name}}</span>
                {{ if !$gimme->article->content_accessible }}
                <span class="label label-important normal-weight">{{ #premium# }}</span>
                {{ /if }} 
                <h2><a href="{{ uri option='article'}}">{{ $gimme->article->name }}</a></h2>
                <span class="article-date"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time> </span>
            </header>
            <div class="article-excerpt hidden-phone">
                {{ $gimme->article->full_text|truncate:100:"...":true }}
            </div>  
            <div class="article-links hidden-phone">
                <hr>
                <a href="{{ uri option='article'}}#comments" class="comments-link">{{ $gimme->article->comment_count }} {{ #comments# }}</a> | <a href="{{uri option='article'}}" class="link-color">{{ #readMore# }}</a>
            </div>                                        
            <div class="clearfix"></div>
        </article>
    </div>
    <div class="row">
        <div class="span8 other-featured">
{{else}}
            <article class="pull-left">
                <div class="article-content">
                    <figure class="article-image pull-left">
                        <a href="{{ uri option='article'}}">
                            {{ include file='_tpl/img/img_202x152.tpl'}}
                            {{ include file='_tpl/img/img_225x150.tpl'}}
                        </a>
                    </figure>                                                
                    <header>
                        <span class="link-color">{{ $gimme->article->section->name}}</span>
                        <h2><a href="{{url option='article'}}">{{ $gimme->article->name}}</a></h2>
                        {{ if !$gimme->article->content_accessible }}
                        <span class="label label-important normal-weight">{{ #premium# }}</span>
                        {{ /if }} 
                        <span class="article-date"><time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time>
</span>
                    </header>
                    <div class="article-excerpt hidden-phone">
                        {{ $gimme->article->full_text|truncate:100:"...":true }}
                    </div>
                    <div class="article-links hidden-phone">
                        <hr>
                        <a href="{{url option='article'}}#comments" class="comments-link">{{ $gimme->article->comment_count }} {{ #comments# }}</a> | 
                        <a href="{{url option='article'}}" class="link-color">{{ #readMore# }}</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </article>                                        
{{/if}}
{{ if $gimme->current_list->at_end }}
        </div>
    </div>
{{/if}}
{{ /list_playlist_articles }}
</div><!-- end front-stories.tpl -->
