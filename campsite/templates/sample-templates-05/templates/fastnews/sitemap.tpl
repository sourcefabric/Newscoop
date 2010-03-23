<td class=sitemap valign=top>

{{ include file="fastnews/homelink.tpl" }}

<h4>Sections</h4>
<ul>
{{ list_sections }}
<li><a href="{{ uri options="section" }}">{{ $campsite->section->name }}</a></li>
{{ /list_sections }}
</ul>

{{ include file="fastnews/search-sidebar.tpl" }}

{{ include file="fastnews/archive-link.tpl" }}

{{ include file="fastnews/rss-link.tpl" }}

</td>