<td valign=top class="sitemap">

{{ include file="fastnews/homelink.tpl" }}

<h4>Sections</h4>
<ul>
{{ list_sections }}
<li><a href="{{ uri options="section reset_article_list" }}">{{ $campsite->section->name }}</a>
{{ /list_sections }}
</ul>

<h4>{{ $campsite->section->name }}</h4>
<ul>
{{ list_articles }}
	<li><a href="/tpl/fastnews/article.tpl?{{ urlparameters options="article reset_subtitle_list" }}">{{ $campsite->article->name }}</a>{{ if ! $campsite->article->content_accesible }}&nbsp;<img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}
{{ /list_articles }}
</ul>

{{ include file="fastnews/search-sidebar.tpl" }}

{{ include file="fastnews/archive-link.tpl" }}

{{ include file="fastnews/rss-link.tpl" }}

</td>
