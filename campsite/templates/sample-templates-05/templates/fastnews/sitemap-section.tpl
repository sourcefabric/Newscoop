<td valign=top class="sitemap">

{{ include file="fastnews/homelink.tpl" }}

<h4>Sections</h4>
<ul>
{{ list_sections }}
<li><a href="{{ uri options="section" }}">{{ $campsite->section->name }}</a>
{{ /list_sections }}
</ul>

<h4>{{ $campsite->section->name }}</h4>
<ul>
{{ list_articles }}
	<li><a href="{{ urlparameters options="article" }}">{{ $campsite->article->name }}</a>{{ if ! $campsite->article->content_accessible }}&nbsp;<img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}
{{ /list_articles }}
</ul>

{{ include file="fastnews/search-sidebar.tpl" }}

{{ include file="fastnews/archive-link.tpl" }}

{{ include file="fastnews/rss-link.tpl" }}

</td>
