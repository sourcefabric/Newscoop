<td valign=top class="sitemap">

{{ include file="fastnews/homelink.tpl" }}

<h4>Sections</h4>
<ul>
{{ list_sections }}
{{ if $campsite->default_section == $campsite->section }}
	<li><b><a href="/tpl/fastnews/section.tpl?{{ urlparameters options="section reset_article_list" }}">{{ $campsite->section->name }}</a></b>
{{ else }}
	<li><a href="/tpl/fastnews/section.tpl?{{ urlparameters options="section reset_article_list" }}">{{ $campsite->section->name }}</a>
{{ /if }}
{{ /list_sections }}
</ul>

<h4>{{ $campsite->section->name }}</h4>
<ul>
{{ list_articles }}
{{ if $campsite->default_article == $campsite->article }}
	<li>{{ $campsite->article->name }}{{ if ! $campsite->article->content_accesible }}&nbsp;<img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}
{{ else }}
	<li><a href="{{ uri options="article reset_subtitle_list" }}">{{ $campsite->article->name }}</a>{{ if ! $campsite->article->content_accesible }}&nbsp;<img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}
{{ /if }}
{{ /list_articles }}
</ul>

{{ include file="fastnews/search-sidebar.tpl" }}

{{ include file="fastnews/archive-link.tpl" }}

{{ include file="fastnews/rss-link.tpl" }}

</td>