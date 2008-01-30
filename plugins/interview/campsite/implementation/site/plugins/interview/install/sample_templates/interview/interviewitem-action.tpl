<!-- {{ $smarty.template }} -->

{{ if $smarty.request.interviewitem_action == 'form' || $campsite->interviewitem_action->defined }}

    {{ include file='interview/interviewitem-edit.tpl' }}
    
{{ elseif $smarty.request.interviewitem_action == 'list' }}

    {{ include file='interview/interviewitems-list.tpl' }}

{{ /if }}

<!-- /{{ $smarty.template }} -->