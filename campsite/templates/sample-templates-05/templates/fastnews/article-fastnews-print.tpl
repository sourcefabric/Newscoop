<!-- This is the extended article PRINT template -->

<h2>{{ $campsite->article->name }}</h2>
{{ $campsite->article->date|camp_date_format:"%W, %M %e %Y" }}

{{ if $campsite->article->content_accessible }}
  
    {{ list_subtitles }}
      {{ $campsite->article->body }}
	{{ /list_subtitles }}
  
{{ else }}
  {{ include file="fastnews/not_allowed.tpl" }}
{{ /if }}