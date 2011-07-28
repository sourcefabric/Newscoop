{{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
    <div class="ui-body ui-body-e"><h3>Error posting comment</h3>Your comment has not been accepted.</div>
{{ /if }}

{{ if $gimme->submit_comment_action->is_error }}
    <div class="ui-body ui-body-e"><h3>Comment problem</h3>{{ $gimme->submit_comment_action->error_message }}
        <span class="posterrorcode" style="display: none;">{{ $gimme->submit_comment_action->error_code }}</span>
   </div>
{{ else }}
    {{ if $gimme->submit_comment_action->defined }}
        {{ if $gimme->publication->moderated_comments }}
            <div class="ui-body ui-body-e"><h3>Thank you</h3>Your comment has been sent for approval.</div>
        {{ /if }}
    {{ /if }}   
{{ /if }}