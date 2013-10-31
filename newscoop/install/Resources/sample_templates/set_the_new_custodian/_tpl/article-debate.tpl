          <article class="art-item clearfix">
                <header>
                  <hgroup>
                        <h1>{{ $gimme->article->name }}</h1>

                        <p><span class="right">{{ include file="_tpl/article-icons.tpl" }}</span>{{ #publishedOn# }} <time datetime="{{$gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ"}}">{{ $gimme->article->publish_date|camp_date_format:"%d %M %Y" }}</time></p>
                         {{ if $gimme->article->has_map }} 
                         {{ list_article_locations }}{{ if $gimme->current_list->at_beginning }}<p>{{ #locationS# }} {{ /if }}{{ if $gimme->location->enabled }}{{ $gimme->location->name }}{{ if $gimme->current_list->at_end }}</p>{{ else }}, {{ /if }}{{ /if }}{{ /list_article_locations }}
                        {{ /if }}
                        
                        {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<p>{{ #topicS# }} {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</p>{{ else }}, {{ /if }}{{ /list_article_topics }}                        

                    </hgroup>
                </header>
                
{{ if $gimme->article->content_accessible }}                
                
                <div class="clearfix">{{ include file="_tpl/_edit-article.tpl" }}<em>{{ $gimme->article->teaser }}</em></div>

                    {{ list_article_authors }}
                    {{ if $gimme->current_list->index == "1" }}
                        <div class="inner-highlight right">                        
                            <figure>
                          <img rel="resizable" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" width=97 height=97 />
                            </figure>
                            <h5>{{ #proArgumentsBy# }}</h5>
                            <p>{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}</p>
                            <p>{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>
                        </div>
                     {{ /if }}
                     {{ /list_article_authors }}                    
                                    
                <h3>PRO: {{ $gimme->article->pro_title }}</h3>
                <div class="clearfix">{{ $gimme->article->pro_text }}</div>

                    {{ list_article_authors }}
                    {{ if $gimme->current_list->index == "2" }}                        
                        <div class="inner-highlight left">
                            <figure>
                          {{ if $gimme->author->user->defined || $gimme->author->picture->imageurl }}
                          <img rel="resizable" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" width=97 height=97 />
                          {{ /if }}                            
                            </figure>
                            <h5>{{ #contraArgumentsBy# }}</h5>
                            <p>{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}</p>
                            <p>{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>
                        </div>
                     {{ /if }}
                     {{ /list_article_authors }}                
                <h3>CONTRA: {{ $gimme->article->contra_title }}</h3>
                <div class="clearfix">{{ $gimme->article->contra_text }}</div>
            </article>

            <div id="social-group">
                <div id="twitter">
                    <script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
                    <a href="http://twitter.com/share" class="twitter-share-button" data-text="{{ $gimme->article->name }}" data-via="{{ $gimme->publication->name }}">{{ #tweet# }}</a> 
                </div><!-- /#twitter -->
                <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=100924830001723&amp;xfbml=1"></script><fb:like href="http://{{ $gimme->publication->site }}{{ uri }}" send="true" width="385" show_faces="true" font=""></fb:like>       
            </div><!-- /#social-group -->
            
{{ else }}        
            <p>{{ #infoOnLockedArticles# }}</p>
{{ /if }}    
