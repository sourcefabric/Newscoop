<h6>{{ $smarty.template }}</h6>

{{ if $smarty.request.interview_action == 'form' || $campsite->interview_action->defined }}

    {{ include file='interview/admin/interview-edit.tpl' }}

{{ elseif $smarty.request.interview_action == 'list_items' }}

    {{ list_interviewitems length=10 }}
        {{ include file='interview/interviewitem-details.tpl' }}
        {{ include file='interview/admin/interviewitem-actions.tpl' }}
    {{ /list_interviewitems }}
    
{{ else }}

    {{ include file='interview/interview-details.tpl' }}
    {{ include file='interview/admin/interview-actions.tpl' }}

{{ /if }}