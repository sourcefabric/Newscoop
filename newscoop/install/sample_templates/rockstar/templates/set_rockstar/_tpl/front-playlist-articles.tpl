{{ list_playlist_articles name="Front page" length="4" }} 
{{ if $gimme->current_list->at_beginning }}

          <section class="grid-1">
            
              <article>
                    {{ include file="_tpl/img/img_big.tpl" where="topfront" }}
                    <small><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /list_article_topics }}</small>
                    <h2><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h2>
                    <span class="date">{{ include file="_tpl/relative_date.tpl" date=$gimme->article->publish_date }} {{ list_article_authors }}{{ if $gimme->current_list->at_beginning }} / {{ #by# }} {{ /if }}{{ if $gimme->current_list->at_end }}{{ if $gimme->current_list->index > 1 }} {{ #and# }} {{ /if }}{{ else }}{{ if $gimme->current_list->index > 1 }}, {{ /if }}{{ /if }}{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}{{ if $gimme->current_list->at_end }}. {{ /if }}{{ /list_article_authors }}</span>
                    <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->deck }}</p>
                    <span class="more"><a href="{{ url options="article" }}">+  {{ #readMore# }}</a> or <a href="{{ url options="article" }}#comments">{{ #addComment# }} ({{ $gimme->article->comment_count }})</a></span>
                </article>
            
            </section><!-- / 1 article grid -->

            <div class="title">
              <h2>{{ #moreNewStories# }}</h2>
                <p>{{ #findMoreStories# }} <a href="{{ url options="template archive.tpl" }}">+  {{ #goToArchives# }}</a></p>
            </div>
            <section class="grid-3">
            
{{ else }}            
            
              <article>
                    {{ include file="_tpl/img/img_onethird.tpl" }} 
                    <small><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /list_article_topics }}</small>
                    <h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h4>
                    <span class="time">{{ include file="_tpl/relative_date.tpl" date=$gimme->article->publish_date }} / <a href="{{ url options="article" }}#comments">{{ $gimme->article->comment_count }} comments</a></span>
                </article>

{{ if $gimme->current_list->at_end }}            
            </section><!-- / 3 articles grid -->
{{ /if }}

{{ /if }}  
{{ /list_playlist_articles }}                            