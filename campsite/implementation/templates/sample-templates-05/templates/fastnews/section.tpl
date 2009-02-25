{{ include file="fastnews/htmlheader.tpl" }}

<html>
<head>
<title>{{ $campsite->section->name }}&#32;({{ $campsite->publication->name }}, #{{ $campsite->issue->number }}) {{ if $campsite->issue->is_current }} -- current issue{{ /if }}</title>

{{ include file="fastnews/meta.tpl" }}

</head>
<body>

{{ include file="fastnews/header.tpl" }}

{{ include file="fastnews/sitemap-section.tpl" }}


<td class=csmain valign=top>

<div class="rightfloat">
{{ include file="fastnews/userinfo.tpl" }}
{{ include file="fastnews/printbox.tpl" }}
</div>

<div class=csmain>

<h1>{{ $campsite->section->name }}</h1>
<address>Issue #{{ $campsite->issue->number }}, {{ $campsite->issue->name }}</address>

<!-- List of articles to be shown on section -->

{{ list_articles constraints="OnSection is on" }}
	{{ if $campsite->current_list->at_beginning }}
		<ul>
	{{ /if }}

	<li><b><a href="/tpl/fastnews/article.tpl?{{ urlparameters }}">
	{{ $campsite->article->name }}</a></b>{{ if ! $campsite->article->content_accesible }} <img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}<br>
	{{ if $campsite->article->type_name == "extended" }}
		<i>{{ $campsite->article->author }}</i>, {{ $campsite->article->date|camp_date_format:"%W, %M %e %Y" }}
	{{ /if }}
	{{ if $campsite->article->type_name == "fastnews" }}
		{{ $campsite->article->date|camp_date_format:"%W, %M %e %Y" }}
	{{ /if }}
	<br>{{ $campsite->article->intro }}<br><br>
	{{ /list_articles }}
{{ if $campsite->prev_list_empty }}
	<p>There are no articles to be shown on section page
{{ /if }}
</ul>
</td>
<!-- End of articles list to be shown on section -->

</div>

{{ include file="fastnews/footer.tpl" }}
</body>
</html>