{{ list_articles length="6" ignore_issue="true" ignore_section="true" order="bypopularity desc" constraints="type is news" }}
  <div class="twocol {{ if $gimme->current_list->index == 6 }} last{{ /if }}">
    <p class="section"><a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></p>
      <a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}">
      <img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 200" }}{{ else }}{{ uri options="image 1 width 200" }}{{ /if }}" class="thumbnail">
    </a>
{{ include file="_tpl/link-readmore.tpl" }}
    <h3><a href="{{ uri options="article" }}">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h3>
  </div>
{{ /list_articles }} 