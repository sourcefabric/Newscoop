{{ include file="fastnews/htmlheader.tpl" }}

<!-- This is the issue template -->

<html>
<head>
<title>{{ $campsite->article->name }} ({{ $campsite->publication->name }}, #{{ $campsite->issue->number }}) {{ if $campsite->issue->is_current }} -- current issue{{ /if }}</title>

{{ include file="fastnews/meta.tpl" }}

</head>
<body>

{{ include file="fastnews/header.tpl" }}

{{ include file="fastnews/sitemap-issue.tpl" }}

<!-- This is the presentation of issue -->
<td valign=top class="artlist">

<div class="rightfloat">
{{ include file="fastnews/userinfo.tpl" }}
{{ include file="fastnews/printbox.tpl" }}
</div>

<div class=csmain>

<h1>Welcome to Fastnews</h1>

{{ if $campsite->issue->is_current }}
<p><small>Note, this is a demonstration site only! Don't expect this site to stick around for long!</small></p>
{{ /if }}

{{ local }}
{{ unset_section }}
{{ list_articles constraints="OnFrontPage is on" order="bynumber desc" }}

	<div style="clear:left">
	<p><b><a href="/tpl/fastnews/article.tpl?{{ urlparameters options="reset_subtitle_list" }}">
	{{ $campsite->article->name }}</a></b>{{ if ! $campsite->article->content_accesible }} <img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}<br>
	<small>{{ $campsite->article->date|camp_date_format:"%W, %M %e, %Y" }}</small><br>
	{{ if $campsite->article->translated_to == "ro" }}
		-- [ro]
	{{ /if }}

	{{ $campsite->article->intro }}</p>
	</div>

	{{ /list_articles }}
{{ if $campsite->prev_list_empty }}
	<p>No articles to show on frontpage
{{ /if }}
{{ /local }}
<!-- End of Issue presentation -->
</div>
</td>

{{ include file="fastnews/footer.tpl" }}

</body>
</html>
