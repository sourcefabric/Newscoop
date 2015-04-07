{{dynamic}}

{{ if !$gimme->publication->public_comments }}
    <!-- public comments are not allowed-->
    {{ if $gimme->user->logged_in }}
        <!-- user is logged in -->
        {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
            {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                <p><em>{{'commentNotAccepted'|translate}}</em></p>
            {{ /if }}

            {{ if $gimme->submit_comment_action->is_error }}
                <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
            {{ else }}
                {{ if $gimme->submit_comment_action->defined }}
                    {{ if $gimme->publication->moderated_comments }}
                        <p><em>{{'commentSentApproval'|translate}}</em></p>
                    {{ /if }}
                {{ /if }}
            {{ /if }}

            <h3>{{'leaveReply'|translate}}</h3>
            {{ comment_form html_code="id=\"commentform\"" submit_button="{{'submit'|translate}}" button_html_code="class=\"clearfix\" tabindex=\"6\"" }}
                <div class="form-element clearfix">
                    <label for="comment">{{'comment'|translate}}</label>
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" }}
                </div>

                <div class="form-element clearfix">
                    <label for="f_captcha_code">{{'enterCode'|translate}}</label>
                    {{ recaptcha }}
                </div>
            {{ /comment_form }}

        {{ else }}
            <p>{{'commentsLockedDisabled'|translate}}</p>
        {{ /if }}

    {{ else }}
        <!-- user is not logged in -->
        <p>{{'notLoggedInMessage'|translate}}</p>
    {{ /if }}
{{ else }}
    <!-- public comments are allowed-->
    {{ if $gimme->user->logged_in }}
        <!-- user is logged in -->
        {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
            {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                <p><em>{{'commentNotAccepted'|translate}}</em></p>
            {{ /if }}

            {{ if $gimme->submit_comment_action->is_error }}
                <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
            {{ else }}
                {{ if $gimme->submit_comment_action->defined }}
                    {{ if $gimme->publication->moderated_comments }}
                        <p><em>{{'commentSentApproval'|translate}}</em></p>
                    {{ /if }}
                {{ /if }}
            {{ /if }}

            <h3>{{'leaveReply'|translate}}</h3>
            {{ comment_form html_code="id=\"commentform\"" submit_button="{{'submit'|translate}}" button_html_code="tabindex=\"6\" class=\"clearfix\" " }}
                <div class="form-element clearfix">
                    <label for="comment">{{'comment'|translate}}</label>
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" }}
                </div>

                    <div class="form-element clearfix">
                    <label for="f_captcha_code">{{'enterCode'|translate}}</label>
                    {{ recaptcha }}
                </div>
            {{ /comment_form }}

        {{ else }}
            <p>{{'commentsLockedDisabled'|translate}}</p>
        {{ /if }}

    {{ else }}
        <!-- user is not logged in -->
        {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
            {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                <p><em>{{'commentNotAccepted'|translate}}</em></p>
            {{ /if }}

            {{ if $gimme->submit_comment_action->is_error }}
                <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
            {{ else }}
                {{ if $gimme->submit_comment_action->defined }}
                    {{ if $gimme->publication->moderated_comments }}
                        <p><em>{{'commentSentApproval'|translate}}</em></p>
                    {{ /if }}
                {{ /if }}
            {{ /if }}

            <h3>{{'leaveReply'|translate}}</h3>
            {{ comment_form html_code="id=\"commentform\"" submit_button="{{'submit'|translate}}" button_html_code="tabindex=\"6\" class=\"clearfix\"" }}
                <div class="form-element clearfix">
                    <label for="author"><small>{{'nameRequired'|translate}}</small></label>
                    {{ camp_edit object="comment" attribute="nickname" html_code="id=\"author\" tabindex=\"1\"" }}
                </div>

                <div class="form-element clearfix">
                    <label for="email"><small>{{'emailRequired'|translate}}</small></label>
                    {{ camp_edit object="comment" attribute="reader_email" html_code="id=\"email\" tabindex=\"2\"" }}
                </div>

                <div class="form-element clearfix">
                    <label for="comment">{{'comment'|translate}}</label>
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" }}
                </div>

                <div class="form-element clearfix">
                    <label for="f_captcha_code">{{'enterCode'|translate}}</label>
                    {{ recaptcha }}
                </div>

            {{ /comment_form }}

        {{ else }}
            <p>{{'commentsLockedDisabled'|translate}}</p>
        {{ /if }}
    {{ /if }}
{{ /if }}
{{/dynamic}}
