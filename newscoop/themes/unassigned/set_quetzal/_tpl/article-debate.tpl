<!-- MAIN ARTICLE -->
<div class="span8 article-container">
    <article class="main-article single debate">                                    
        {{ if $gimme->article->content_accessible }} 
        <header>
            <span class="article-info">
                <time datetime="{{$gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ"}}">{{ $gimme->article->publish_date|camp_date_format:"%d %M %Y" }}</time> 
                By {{ list_article_authors }} {{ if $gimme->author->user->defined}}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}" class="link-color">{{/if}}{{ $gimme->author->name }}{{if $gimme->author->user->defined }}</a>{{/if}} ({{ $gimme->author->type|lower }}) {{ if !$gimme->current_list->at_end }}, {{/if}}{{/list_article_authors}}
            {{ if $gimme->article->has_map }}
            <span class="pull-right visible-desktop">{{ #locations# }}: {{ list_article_locations }}{{ if $gimme->location->enabled }}{{ $gimme->location->name }}{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}{{ /if }}{{ /list_article_locations }}</span> <span class="visible-phone">{{ #locations# }}: {{ list_article_locations }}{{ if $gimme->location->enabled }}{{ $gimme->location->name }}{{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /if }}{{ /list_article_locations }}</span>
            {{/if}}
            </span>
            <div class="clearfix"></div>
        </header>

        <section class="article-content">

            {{ include file="_tpl/_edit-article.tpl" }}

            <em class="teaser">{{ $gimme->article->teaser }}</em>

            <div class="debate-right">
                                <h1>PRO: {{ $gimme->article->pro_title }}</h1>
                {{ list_article_authors }}
                    {{ if $gimme->current_list->index == "1"}}
                    <div class="well pull-right ">
                        <figure>
                          <img rel="resizable" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" width=97 height=97 />
                        </figure>
                        <h5>{{ #proArgumentsBy# }}</h5>
                        <p>{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}" class="link-color">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}</p>
                        <p>{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>
                    </div>
                    {{ /if }}

                {{ /list_article_authors }}

                <div class="clearfix">{{ $gimme->article->pro_text }}</div>
            </div>

            <div class="debate-left">
                <h1>CONTRA: {{ $gimme->article->contra_title }}</h1>
                {{ list_article_authors }}
                    {{ if $gimme->current_list->index == "2"}}
                    <div class="well pull-left">
                        <figure>
                          <img rel="resizable" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" width=97 height=97 />
                        </figure>
                        <h5>{{ #contraArgumentsBy# }}</h5>
                        <p>{{ if $gimme->author->user->defined }}<a class="link-color" href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}</p>
                        <p>{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>
                    </div>
                    {{ /if }}
                {{ /list_article_authors }}

                
                <div class="clearfix">{{ $gimme->article->contra_text }}</div>
            </div>
        </section>
                <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style">
            
           
            <a class="addthis_button_facebook_like" fb:like:width="112" fb:like:layout="button_count"></a>
            <a class="addthis_button_tweet"></a>
            <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
        </div>
        <script src="//s7.addthis.com/js/200/addthis_widget.js#pubid=undefined"></script>
        <script>
            var addthis_config = {ui_language: "{{ $gimme->language->code }}"}
        </script>
        <!-- AddThis Button END -->

        {{ include file="_tpl/article-comments.tpl" }}
        </section>
        {{ else }}
        <header>
            <p>{{ #infoOnLockedArticles# }}</p>
        </header>
        {{ /if }} {{* end content_accesible *}}
    </article>
</div>
