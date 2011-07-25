<div class="block">
<span class="title">Recent Comments</span>
{{ local }}
{{ set_current_issue }}
  <ul id="recentcomments">
{{ list_articles length="5" order="byLastComment desc" constraints="type is news" }}
    <li class="recentcomments">
      {{ list_article_comments length="1" order="bydate desc"}}<span class="commentnick">{{ $gimme->comment->nickname }}</span>{{ /list_article_comments }}: 
      <a href="{{ uri options="article" }}#comments" class="artilename">{{ $gimme->article->name }}</a></li>
{{ /list_articles }}
  </ul>
{{ /local }}
</div>