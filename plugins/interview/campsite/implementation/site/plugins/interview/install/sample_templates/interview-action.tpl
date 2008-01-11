<h4>{{ $smarty.template }}</h4>

{{ if $campsite->interview_action->defined || $smarty.request.action == 'interview_create' || $smarty.request.action == 'interview_edit'}}

    {{ include file=interview-edit.tpl }}

{{ elseif $campsite->interviewstatus_action->defined }}

    {{ include file='interview-details.tpl' show_actions=true }}    
        
{{ elseif $action == 'add_question'}}
    
    {{ if $campsite->interviewitem->store }}
            saved
    {{ else }}
        {{ interviewquestion_form }}{{ /interviewquestion_form }}
    {{ /if }}
       
{{ /if }}