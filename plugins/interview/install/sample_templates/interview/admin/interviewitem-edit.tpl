<h6>{{ $smarty.template }}</h6>

{{ if $campsite->interviewitem_action->defined }}

OK: {{ if $campsite->interviewitem_action->ok }} true {{ else }} false {{ /if }}<br>


    {{ if $campsite->interviewitem_action->error }}
        <h6>Form Errors:</h6>
        
        <font color="red">{{ $campsite->interviewitem_action->error->message }}</font>
        <p>
        
        {{ include file='interview/admin/interviewitem-form.tpl' }}
        
    {{ else }}
         <h6>Interviewitem saved</h6>
         {{ include file='interview/interviewitem-details.tpl' }}
         {{ include file='interview/admin/interviewitem-actions.tpl' }}
    {{ /if }}
    
{{ else }}

    {{ include file='interview/admin/interviewitem-form.tpl' }}
    
{{ /if }}