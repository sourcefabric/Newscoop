<!-- This is the fastnews article template -->

{{ if $campsite->article->type_name == "fastnews" }}

	<h1>{{ $campsite->article->name }}</h1>
	<address>{{ $campsite->article->date|camp_date_format:"%W, %e %M %Y" }}</address>

	{{ if $campsite->article->content_accessible }}
		
			
			{{ $campsite->article->body }}
						
			<div class="nextprev">
			{{ if $campsite->article->type->fastnews->field->has_previous_subtitles }}
				<a href="{{ uri options="previous_subtitle body" }}">Previous subhead</a>
				{{ if $campsite->article->type->fastnews->field->has_next_subtitles }} | {{ /if }}
				
			{{ /if }}
			{{ if $campsite->article->type->fastnews->field->has_next_subtitles }}
				<a href="{{ uri options="next_subtitle body" }}">Next subhead</a>
			{{ /if }}
			</div>
	
		
	{{ else }}
	{{ include file="fastnews/not_allowed.tpl" }}
	{{ /if }}
{{ else }}
	You can't use this template with this article!
{{ /if }}
