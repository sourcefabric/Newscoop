{{ list_related_articles length="6" }}
{{ if $gimme->current_list->at_beginning }}
<div class="block">
<h3>Related Articles</h3>
<ul>
{{ /if }}
  <div class="post">
    <div class="sidebar-image"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2  width 100"}}{{ else }}{{ uri options="image 1  width 100"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="thumbnail"></a></div>
    <h4><a href="{{ uri options="article" }}">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h4>
{{ include file="_tpl/link-readmore.tpl" }}
  </div>
{{ if $gimme->current_list->at_end }}
</ul>        
</div><!--widget block-->
{{ /if }}
{{ /list_related_articles }}
