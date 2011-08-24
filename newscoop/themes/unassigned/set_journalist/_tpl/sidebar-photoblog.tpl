{{ list_articles length="5" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="section is 30" }}
{{ if $gimme->current_list->at_beginning }}
    <h3><a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></h3>
        <ul>
{{ /if }}        
      
        <li class="cat-post-item">
          <a class="post-title" href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a>
          {{ if $gimme->current_list->index == "1" }}
            {{ list_article_images length="1" }}
            <a href="{{ uri options="article" }}"><img style="margin: 5px 0" alt="{{ $gimme->article->image->description }}" src="{{ uri options="image width 180" }}" /></a>
            {{ /list_article_images }}
          {{ /if }}
          <p class="post-date">{{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }}</p>
      </li>
{{ if $gimme->current_list->at_end }}
      </ul>
{{ /if }}      
{{ /list_articles }}                         