
{{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
    <div class="message messagecomment messageerror">Your comment has not been accepted.</div>
{{ /if }}
{{ if $gimme->submit_comment_action->is_error }}
    <div class="message messagecomment messageerror">{{ $gimme->submit_comment_action->error_message }}
        <div class="posterrorcode">(error code: {{ $gimme->submit_comment_action->error_code }})</div>
   </div>
{{ else }}
    {{ if $gimme->submit_comment_action->defined }}
        {{ if $gimme->publication->moderated_comments }}
            <div class="message messagecomment messageinformation">Your comment has been sent for approval.</div>
        {{ /if }}
    {{ /if }}   
{{ /if }}