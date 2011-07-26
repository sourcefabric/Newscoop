{{ list_articles length="3" ignore_section="true" constraints="onsection is on" }}
  <div class="post">
    <p class="section"><a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a></p>    
    <div class="sidebar-image"><a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}"><img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2  width 100"}}{{ else }}{{ uri options="image 1 width 100"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="thumbnail"></a></div>
    <h3><a href="{{ uri options="article" }}">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h3>
{{ include file="_tpl/link-readmore.tpl" }}
  </div><!--post-->
{{ /list_articles }}
<p>You find more articles in the <a href="{{ uri options="template archive.tpl" }}">Archive</a></p>