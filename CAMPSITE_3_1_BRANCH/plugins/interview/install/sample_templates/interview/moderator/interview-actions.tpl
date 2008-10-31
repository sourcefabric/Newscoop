<h6>{{ $smarty.template }}</h6>

{{ list_interviewitems length=1 constraints='status is draft' }}
    <a href="{{ uripath }}?interviewitem_action=list&amp;f_interviewitem_status=draft&amp;f_interview_id={{ $campsite->interview->identifier }}">
        List new items ({{ $campsite->current_list->count }})</a>
{{ /list_interviewitems }}

<br>

{{ list_interviewitems length=1 }}
    <a href="{{ uripath }}?interviewitem_action=list&amp;f_interview_id={{ $campsite->interview->identifier }}">
        List all items ({{ $campsite->current_list->count }})</a>
{{ /list_interviewitems }}