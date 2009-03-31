
<html>
<head>
<title>{{ $campsite->article->name }} ({{ $campsite->publication->name }}, #{{ $campsite->issue->number }}) {{ if $campsite->issue->is_current }} -- current issue{{ /if }}</title>

{{ include file="fastnews/rss-meta.tpl" }}

<link rel="stylesheet" media="screen" href="/templates/fastnews/fastnews.css" type="text/css">

</head>
<body>

{{ local }}{{ set_current_issue }}
{{ include file="fastnews/header.tpl" }}

{{ include file="fastnews/sitemap.tpl" }}
{{ /local }}

<td valign=top>

<h1>Log in 
{{ if $campsite->publication->defined }}
	to {{ $campsite->publication->name }}
{{ /if }}
</h1>

{{ include file="fastnews/login-form.tpl" }}

<p>If you don't have a username and password, you'll need to <b>subscribe</b> before you can access certain content:</p>

<p class="opt"><a href="{{ uri options="template fastnews/useredit.tpl" }}">Subscribe</a></p>

{{ include file="fastnews/footer.tpl" }}
</body>
</html>