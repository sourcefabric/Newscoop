<!-- This is the extended article template -->
{{ if $campsite->article->type_name == "extended" }}

	<h1>{{ $campsite->article->name }}</h1>
	<address>{{ $campsite->article->author }}<br>
	{{ $campsite->article->date|camp_date_format:"%W, %M %e %Y" }}</address>

	{{ if $campsite->article->content_accesible }}
		
		
			{{ $campsite->article->text }}
	
			<div class="nextprev">
			{{ if $campsite->article->type->extended->field->has_previous_subtitles }}
			<a href="{{ uri options="previous_subtitle text" }}">Previous subhead</a>
				{{ if $campsite->article->type->extended->field->has_next_subtitles }}
				|
				{{ /if }}
			{{ /if }}
			{{ if $campsite->article->type->extended->field->has_next_subtitles }}
			<a href="{{ uri options="next_subtitle text" }}">Next subhead</a>
			{{ /if }}
			</div>
	
		
	{{ else }}
		{{ include file="fastnews/not_allowed.tpl" }}
	{{ /if }}
{{ /if }}
