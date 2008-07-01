<h6>{{ $smarty.template }}</h6>

{{ if $smarty.request.interviewitem_action == 'form' || $campsite->interviewitem_action->defined }}

    {{ include file='interview/moderator/interviewitem-edit.tpl' }}
    
{{ elseif $smarty.request.interviewitem_action == 'list' }}

    {{ include file='interview/moderator/interviewitems-list.tpl' }}

{{ /if }}