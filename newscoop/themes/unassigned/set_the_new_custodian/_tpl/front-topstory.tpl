{{ list_playlist_articles id="1" length="1" }}            

            <section id="topstory" class="clearfix">
          {{ include file="_tpl/img/img_500x333.tpl" }}
              <div class="fourcol last">
              <header>
                  <hgroup>
                        <h2><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></h2>
                        <p class="article-info"><em>{{ include file="_tpl/article-icons.tpl" }} {{ #publishedOn# }} {{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }} {{ #in# }} <a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></em></p>
                    </hgroup>
                </header>
                <div id="topstory-intro">
                  <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->deck }}</p>
                    <p><em>{{ list_article_authors }}{{ if $gimme->current_list->at_beginning }}{{ #by# }} {{ /if }}{{ if $gimme->current_list->at_end }}{{ if $gimme->current_list->index > 1 }} {{ #and# }} {{ /if }}{{ else }}{{ if $gimme->current_list->index > 1 }}, {{ /if }}{{ /if }}{{ if $gimme->author->user->defined }}<a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">{{ /if }}{{ $gimme->author->name }}{{ if $gimme->author->user->defined }}</a>{{ /if }}{{ if $gimme->current_list->at_end }}. {{ /if }}{{ /list_article_authors }}</em></p>
                </div>
                </div>
            </section><!-- /#topstory -->
            
{{ /list_playlist_articles }}             
                
           