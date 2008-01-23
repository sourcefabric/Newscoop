<h6>{{ $smarty.template }}</h6>

{{ if $campsite->user->defined }}

    {{ interviewitem_form }}
        <input type="hidden" name="interviewitem_action" value="liss">
        <table border=0>
            <tr><td width="200">Question</td><td>{{ interviewitem_edit attribute='question' }}</td></tr>
        </table>
    {{ /interviewitem_form }}
    
{{ else }}

    <font color="red">You need to be logged in to submit your question.</font>

{{ /if }}