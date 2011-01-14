<h6>{{ $smarty.template }}</h6>

{{ if $gimme->interview_action->defined }}

OK: {{ if $gimme->interview_action->ok }} true {{ else }} false {{ /if }}<br>


    {{ if $gimme->interview_action->error }}
        <h6>Form Errors:</h6>
        
        <font color="red">{{ $gimme->interview_action->error->message }}</font>
        <p>
        
        {{ include file='interview/admin/interview-form.tpl' }}
        
    {{ else }}
         <h6>Interview saved</h6>
         {{ include file='interview/interview-details.tpl' }}
         {{ include file='interview/admin/interview-actions.tpl' }}
    {{ /if }}
    
{{ else }}

    {{ include file='interview/admin/interview-form.tpl' }}
    
{{ /if }}