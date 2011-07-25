<div class="block">
<h3>Also in {{ $gimme->section->name }}</h3>
<ul>
  {{ assign var="curartno" value=$gimme->article->number }}
  {{ list_articles length="4" ignore_issue="true" order="bypublishdate desc" constraints="number not $curartno" }}
  <div class="post">
    <div class="sidebar-image"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2  width 100"}}{{ else }}{{ uri options="image 1  width 100"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="thumbnail"></a></div>
    <h4><a href="{{ uri options="article" }}">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h4>
{{ include file="_tpl/link-readmore.tpl" }}
  </div>

  {{ /list_articles }} 
</ul>        
</div><!--widget block-->