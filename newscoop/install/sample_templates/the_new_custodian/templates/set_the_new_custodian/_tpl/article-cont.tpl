          <article class="art-item clearfix">
                <header>
                  <hgroup>
                        <h1>{{ $gimme->article->name }}</h1>
                        {{ if $gimme->article->type_name == "news" }}
                        <p><span class="right">{{ include file="_tpl/article-icons.tpl" }}</span>Published on <time datetime="{{$gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ"}}">{{ $gimme->article->publish_date|camp_date_format:"%d %M %Y" }}</time> by {{ list_article_authors }}{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }} ({{ $gimme->author->type|lower }}){{ if !$gimme->current_list->at_end }}, {{ /if }}{{ /list_article_authors }}</p>
                         {{ if $gimme->article->has_map }} 
                        <p>Location(s): {{ list_article_locations }}{{ if $gimme->location->enabled }}{{ $gimme->location->name }}{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}{{ /if }}{{ /list_article_locations }}</p>
                        {{ /if }}
                        
                        {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<p>Topic(s): {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}{{ /list_article_topics }}</p>                        
                        
                        {{ /if }}
                    </hgroup>
                </header>
                
{{ if $gimme->article->content_accessible }}                
                
                {{ if $gimme->article->type_name == "news" }}
                {{ include file="_tpl/img/img_600x400.tpl" }}
                {{ /if }}
                <div class="clearfix">{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->full_text }}</div>
            </article>

            <div id="social-group">
                <div id="twitter">
                    <script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
                    <a href="http://twitter.com/share" class="twitter-share-button" data-text="{{ $gimme->article->name }}" data-via="{{ $gimme->publication->name }}">Tweet</a> 
                </div><!-- /#twitter -->
                <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=100924830001723&amp;xfbml=1"></script><fb:like href="http://{{ $gimme->publication->site }}{{ uri }}" send="true" width="385" show_faces="true" font=""></fb:like>       
            </div><!-- /#social-group -->
            
{{ else }}        
            <p><em>This article is locked and is accessible only to <mark>registered</mark> and <mark>logged in</mark> users, sorry!</em></p>
{{ /if }}    
