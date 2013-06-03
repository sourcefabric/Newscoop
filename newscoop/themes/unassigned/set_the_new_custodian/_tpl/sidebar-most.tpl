      <section id="most">
        <div id="tabs">
          <ul>
            <li><a href="#most-comm">{{ #recentlyCommented# }}</a></li>
            <li><a href="#most-pop">{{ #mostRead# }}</a></li>
          </ul>
          <section id="most-comm">
                        <p><em>{{ #recentlyCommentedArticles# }}</em></p>
                        <ul>

{{ local }}
{{ unset_topic }}                        
{{ list_articles length="5" ignore_issue="true" ignore_section="true" order="byLastComment desc" constraints="type is news" }}
                       
                            <li>{{ list_article_comments length="1" order="bydate desc"}}<b>{{ if $gimme->comment->user->identifier }}
                <a href="http://{{ $gimme->publication->site }}/user/profile/{{ $gimme->comment->user->uname|urlencode }}">{{ $gimme->comment->user->uname }}</a>
            {{ else }}
                {{ $gimme->comment->nickname }} {{ #anonymous# }}
            {{ /if }}</b>{{ /list_article_comments }} on <a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}
                        
                        </ul> 
          </section><!-- /#most-comm -->
          <section id="most-pop">
                        <p><em>{{ #mostOpenedArticles# }}</em></p>                    
                        <ul>
                        
{{ set_current_issue }}
{{ list_articles length="5" ignore_section="true" order="bypopularity desc" constraints="type is news" }}
                       
                            <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }} ({{ $gimme->article->reads }})</a></li>

{{ /list_articles }}
{{ /local }}                         
                        
                        </ul> 
          </section><!-- /#most-pop -->
        </div><!-- /#tabs -->
      </section><!-- /#most -->