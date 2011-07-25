<div class="block">
<span class="title">Most Read</span>
{{ local }}
{{ set_current_issue }}
<ul id="mostread">
{{ list_articles length="5" order="bypopularity desc" constraints="type is news" }}
  <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></li>
{{ /list_articles }}
</ul> 
{{ /local }}
</div>