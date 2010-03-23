<html><head><title>{{ $campsite->article->title }}</title>
<link rel="stylesheet" media="screen" href="/templates/fastnews/fastnews.css" type="text/css">
</head>
<body>

{{ if $campsite->article->type_name == "extended" }}
	E
	{{ include file="fastnews/article-extended-print.tpl" }}
{{ /if }}

{{ if $campsite->article->type_name == "fastnews" }}
	F
	{{ include file="fastnews/article-fastnews-print.tpl" }}
{{ /if }}

</body>