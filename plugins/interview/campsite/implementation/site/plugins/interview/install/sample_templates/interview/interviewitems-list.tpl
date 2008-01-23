<h6>{{ $smarty.template }}</h6>

<p>Interview: <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}">{{ $campsite->interview->title }}</a></p>

{{ list_interviewitems length=10 }}
    {{ include file='interview/interviewitem-details.tpl' }}
{{ /list_interviewitems }}  