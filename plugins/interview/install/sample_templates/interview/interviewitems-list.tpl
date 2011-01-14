<!-- {{ $smarty.template }} -->

<p>Interview: <a href="{{ uripath }}?f_interview_id={{ $gimme->interview->identifier }}">{{ $gimme->interview->title }}</a></p>

{{ list_interviewitems length=10 }}
    {{ include file='interview/interviewitem-details.tpl' }}
    <br>
{{ /list_interviewitems }}

{{ if $gimme->prev_list_empty }}
    No Items
{{ /if }}


<!-- /{{ $smarty.template }} -->