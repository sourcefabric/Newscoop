<h1>{{ $gimme->issue->name }}</h1>
<div class="issue-content">
{{ list_sections }}  
{{ list_articles }}
{{ if $gimme->current_articles_list->at_beginning }}


<h3>{{ $gimme->section->name }}</h3>      
<ul>

{{ /if }}    
    
<li><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a> <time datetime="{{ $gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }}</time> <span class="posts">{{ $gimme->article->comment_count }} {{ #commentS# }}</span></li>

{{ if $gimme->current_articles_list->at_end }}

</ul>
    
{{ /if }}
{{ /list_articles }}    
{{ /list_sections }}
</div>