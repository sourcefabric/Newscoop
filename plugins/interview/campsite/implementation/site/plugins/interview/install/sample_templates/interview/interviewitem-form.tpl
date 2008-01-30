<!-- {{ $smarty.template }} -->

{{ if $campsite->user->defined }}

    {{ interviewitem_form }}
        <input type="hidden" name="interviewitem_action" value="liss">
        {{ interviewitem_edit attribute='question' }}
        <br>
    {{ /interviewitem_form }}
    
{{ else }}

    <font color="red">You need to be logged in to submit your question.</font>

{{ /if }}

<!-- /{{ $smarty.template }} -->