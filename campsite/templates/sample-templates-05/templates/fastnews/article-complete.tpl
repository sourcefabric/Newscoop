{{ include file="fastnews/htmlheader.tpl" }}

<!-- This is the article template -->

<html>
<head>
<title>{{ $campsite->article->name }} ({{ $campsite->publication->name }}, #{{ $campsite->issue->number }}) {{ if $campsite->issue->is_current }} -- current issue{{ /if }}</title>

{{ include file="fastnews/meta.tpl" }}

</head>
<body>

{{ include file="fastnews/header.tpl" }}

{{ include file="fastnews/sitemap-article.tpl" }}

<td valign=top>

<div class=rightfloat>
{{ include file="fastnews/userinfo.tpl" }}
{{ include file="fastnews/printbox.tpl" }}
{{ include file="fastnews/subnav.tpl" }}
{{ include file="fastnews/topics.tpl" }}
</div>

<div style="max-width:42em;" align=left>

<h1>{{ $campsite->article->name }}</h1>

{{ if $campsite->article->type_name == "fastnews" }}
	
		{{ list_subtitles }}
			{{ $campsite->article->body }}
		{{ /list_subtitles }}
	
{{ /if }}

{{ if $campsite->article->type_name == "extended" }}
	
		{{ list_subtitles }}
			{{ $campsite->article->text }}
		{{ /list_subtitles }}
	
{{ /if }}

</div>
</td>

{{ include file="fastnews/footer.tpl" }}
</body>
</html>
