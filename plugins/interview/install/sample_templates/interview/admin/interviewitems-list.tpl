<h6>{{ $smarty.template }}</h6>

<p>Interview: <a href="{{ uripath }}?f_interview_id={{ $gimme->interview->identifier }}">{{ $gimme->interview->title }}</a></p>

{{ list_interviewitems length=10 }}
    {{ include file='interview/interviewitem-details.tpl' }}
    {{ include file='interview/admin/interviewitem-actions.tpl' }}
{{ /list_interviewitems }}  