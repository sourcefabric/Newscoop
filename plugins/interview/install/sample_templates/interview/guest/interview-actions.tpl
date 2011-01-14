<h6>{{ $smarty.template }}</h6>

{{ if $gimme->interview->status == 'draft' }}
    <a href="{{ uripath }}?f_interviewstatus=pending&amp;f_interview_id={{ $gimme->interview->identifier }}">Activate</a>
{{ /if }}

{{ list_interviewitems length=1 constraints='status is pending' }}
    <a href="{{ uripath }}?interviewitem_action=list&amp;f_interviewitem_status=pending&amp;f_interview_id={{ $gimme->interview->identifier }}">
        List items awaiting answer ({{ $gimme->current_list->count }})</a>
{{ /list_interviewitems }}

<br>

{{ list_interviewitems length=1 }}
    <a href="{{ uripath }}?interviewitem_action=list&amp;f_interview_id={{ $gimme->interview->identifier }}">
        List all items ({{ $gimme->current_list->count }})</a>
{{ /list_interviewitems }}