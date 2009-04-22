<!-- This is the extended article PRINT template -->

<h2>{{ $campsite->article->name }}</h2>
<address>{{ $campsite->article->author }}<br>
{{ $campsite->article->date|camp_date_format:"%W, %M %e %Y" }}</address>

stuff...
{{ if $campsite->article->content_accessible }}
	
		{{ list_subtitles }}
			{{ $campsite->article->text }}
		{{ /list_subtitles }}
	
{{ else }}
  {{ include file="fastnews/not_allowed.tpl" }}
{{ /if }}
