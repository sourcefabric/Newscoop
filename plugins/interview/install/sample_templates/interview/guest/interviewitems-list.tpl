<h6>{{ $smarty.template }}</h6>

<p>Interview: <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}">{{ $campsite->interview->title }}</a></p>

{{ if strlen($smarty.request.f_interviewitem_status) }}
    {{ assign var='_constraints' value="status is `$smarty.request.f_interviewitem_status`" }}
{{ /if }}

{{ list_interviewitems length=10 constraints=$_constraints}}
    {{ include file='interview/interviewitem-details.tpl' }}
    {{ include file='interview/guest/interviewitem-actions.tpl' }}
{{ /list_interviewitems }}  