{{ if $campsite->article->content_accesible }}

{{ local }}

{{ if $campsite->article->type_name == "fastnews" }}
	
		{{ list_subtitles }}
			{{ if $campsite->current_list->at_beginning }}
				<div class="subheads">
				<p class="opt"><b>Article Outline</b></p>
				<ol>
			{{ /if }}
	
			{{ if $campsite->subtitle->number == $campsite->article->body->subtitle_number }}
				<li value="{{ $campsite->list->index }}">{{ $campsite->subtitle->name }}</li>
			{{ else }}
				<li value="{{ $campsite->list->index }}"><a href="{{ uri }}">{{ $campsite->subtitle->name }}</a></li>
			{{ /if }}
	
			{{ if $campsite->current_list->at_end }}
				</ol>
				<p class=opt><small><a href="{{ uri options="template article-complete.tpl" }}">single page view</a></small></p>
				</div>
			{{ /if }}
		{{ /list_subtitles }}
	
{{ /if }}

{{ if $campsite->article->type_name == "extended" }}
	
		{{ list_subtitles }}
			{{ if $campsite->current_list->at_beginning }}
				<div class="subheads">
				<p class="opt"><b>Article Outline</b></p>
				<ol>
			{{ /if }}
	
			{{ if $campsite->subtitle->number == $campsite->article->text->subtitle_number }}
				<li value="{{ $campsite->list->index }}">{{ $campsite->subtitle->name }}</li>
			{{ else }}
				<li value="{{ $campsite->list->index }}"><a href="{{ uri }}">{{ $campsite->subtitle->name }}</a></li>
			{{ /if }}
	
			{{ if $campsite->current_list->at_end }}
				</ol>
				<p class=opt><small><a href="{{ uri options="reset_subtitle_list template article-complete.tpl" }}">single page view</a></small></p>
				</div>
			{{ /if }}
		{{ /list_subtitles }}
	
{{ /if }}

{{ /local }}
{{ /if }}