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

        <div class="addthis_toolbox addthis_default_style">
            <!--- Twitter button -->
            <div style="float:left; width:90px;">
            <a href="https://twitter.com/share" class="twitter-share-button" data-dnt="true">Tweet</a>
            </div>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
            </script>

            <!--- Facebook button -->
            <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=100924830001723&amp;xfbml=1"></script><fb:like href="http://{{ $gimme->publication->site }}{{ uri }}" send="false" layout="button_count" show_faces="false"></fb:like> 
    
            <!--- Google+ button -->
            <div class="g-plusone" data-size="medium" data-annotation="inline" data-width="120"></div>
            <script type="text/javascript">
              (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
              })();
            </script>
        </div>

        {{ include file="_tpl/article-comments.tpl" }}
        </section>
        {{ else }}
        <header>
            <div class="alert">{{ #infoOnLockedArticles# }}</div>
        </header>
        {{ /if }} {{* end content_accesible *}}
    </article>
</div>
