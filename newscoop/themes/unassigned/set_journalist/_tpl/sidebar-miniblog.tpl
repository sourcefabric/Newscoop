{{ list_articles length="5" ignore_issue="true" ignore_section="true" order="bypublishdate desc" constraints="section is 20" }}
{{ if $gimme->current_list->at_beginning }}
    <h3>{{ $gimme->section->name }}</h3>
        <ul>
{{ /if }}
        <li class="cat-post-item">
          <a class="post-title" href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a>
          <p class="post-date">{{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }}</p>
      </li>
{{ if $gimme->current_list->at_end }}      
      </ul>      
{{ /if }}      
{{ /list_articles }}                   

