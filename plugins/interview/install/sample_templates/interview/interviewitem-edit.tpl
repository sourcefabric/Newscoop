<!-- {{ $smarty.template }} -->

<p>Interview: <a href="{{ uripath }}?f_interview_id={{ $gimme->interview->identifier }}">{{ $gimme->interview->title }}</a></p>

{{ if $gimme->interviewitem_action->defined }}

    {{ if $gimme->interviewitem_action->is_error }}
        <h6>Form Errors:</h6>
        
        <font color="red">{{ $gimme->interviewitem_action->error_message }}</font>
        <p>
        
        {{ include file='interview/interviewitem-form.tpl' }}
        
    {{ else }}
         <h6>Interviewitem saved</h6>
         {{ include file='interview/interviewitem-details.tpl' }}
    {{ /if }}
    
{{ else }}

    {{ include file='interview/interviewitem-form.tpl' }}
    
{{ /if }}

<!-- /{{ $smarty.template }} -->