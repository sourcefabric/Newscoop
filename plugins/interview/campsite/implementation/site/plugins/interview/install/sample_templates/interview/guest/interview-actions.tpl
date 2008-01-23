<h6>{{ $smarty.template }}</h6>

{{ list_interviewitems length=1 constraints='status is pending' }}
    <a href="{{ uripath }}?interviewitem_action=list&amp;f_interviewitem_status=pending&amp;f_interview_id={{ $campsite->interview->identifier }}">
        List items awaiting answer ({{ $campsite->current_list->count }})</a>
{{ /list_interviewitems }}

<br>

{{ list_interviewitems length=1 }}
    <a href="{{ uripath }}?interviewitem_action=list&amp;f_interview_id={{ $campsite->interview->identifier }}">
        List all items ({{ $campsite->current_list->count }})</a>
{{ /list_interviewitems }}