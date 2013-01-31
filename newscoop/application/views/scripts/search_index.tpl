{{extends file="layout.tpl"}}

{{block content}}
<h1>Search results</h1>

{{ list_search_results_solr qf="title^5 deck^3 full_text" start=$smarty.get.start  }}
  {{ if $gimme->current_list->at_beginning }}
  <ul>
  {{ /if }}
    <li class="{{ cycle values="odd,even" }}">
      <article>
        <h2 class="title"><a href="{{ uri options="article" }}">{{ $gimme->article->title|escape }}</a></h2>
        <h5 class="author">{{ list_article_authors }}{{ $gimme->author->name|escape }}{{ /list_article_authors }}</h5>
        <p>{{ $gimme->article->deck|strip_tags|truncate:200:"...":false }}</p>
      </article>
    </li>
  {{ if $gimme->current_list->at_end }}
  </ul>
  {{ /if }}
{{ /list_search_results_solr }}

{{/block}}
