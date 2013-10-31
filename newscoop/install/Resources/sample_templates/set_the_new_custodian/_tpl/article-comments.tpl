<div id="comments" class="twelvecol">

<h3>{{ $gimme->article->comment_count }} {{ #responsesTo# }} “{{ $gimme->article->name }}”</h3>
{{ list_article_comments order="bydate desc"}}
    {{ if $gimme->current_list->at_beginning }}
        <section id="comment-list">
    {{ /if }}

    <article id="comment-{{ $gimme->current_list->index }}" class="clearfix">
        <header>
            <h4>
            {{ if $gimme->comment->user->identifier }}
                <a href="http://{{ $gimme->publication->site }}/user/profile/{{ $gimme->comment->user->uname|urlencode }}">{{ $gimme->comment->user->uname }}</a>
            {{ else }}
                {{ $gimme->comment->nickname }} {{ #anonymous# }}
            {{ /if }}
            </h4>
            <time datetime="{{ $gimme->comment->submit_date|camp_date_format:"%Y-%m-%dT%H:%iZ" }}">{{ $gimme->comment->submit_date|camp_date_format:"%e.%m.%Y at %H:%i" }}</time>
        </header>
        <p>{{ $gimme->comment->content }}</p>
    </article>                    

    {{ if $gimme->current_list->at_end }}      
            </section>
    {{ /if }}            
{{ /list_article_comments }}

{{ if !$gimme->publication->public_comments }}
    <!-- public comments are not allowed-->
    {{ if $gimme->user->logged_in }}
        <!-- user is logged in -->
        {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
            {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                <p><em>{{ #commentNotAccepted# }}</em></p>
            {{ /if }}

            {{ if $gimme->submit_comment_action->is_error }}
                <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
            {{ else }}
                {{ if $gimme->submit_comment_action->defined }}
                    {{ if $gimme->publication->moderated_comments }}
                        <p><em>{{ #commentSentApproval# }}</em></p>
                    {{ /if }}
                {{ /if }}   
            {{ /if }}
          
            <h3>{{ #leaveReply# }}</h3>
            {{ comment_form html_code="id=\"commentform\"" submit_button="{{ #submit# }}" button_html_code="tabindex=\"6\"" }}
                <div class="form-element clearfix">
                    <label for="comment">{{ #comment# }}</label>
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" }}
                </div>

                <div class="form-element clearfix">
                    <label for="f_captcha_code">{{ #enterCode# }}</label>
                    {{ recaptcha }}
                </div>
            {{ /comment_form }}

        {{ else }}
            <p>{{ #commentsLockedDisabled# }}</p>
        {{ /if }}
           
    {{ else }}
        <!-- user is not logged in -->
        <p>{{ #notLoggedInMessage# }}</p>
    {{ /if }}
{{ else }}
    <!-- public comments are allowed-->
    {{ if $gimme->user->logged_in }}
        <!-- user is logged in -->
        {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
            {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                <p><em>{{ #commentNotAccepted# }}</em></p>
            {{ /if }}

            {{ if $gimme->submit_comment_action->is_error }}
                <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
            {{ else }}
                {{ if $gimme->submit_comment_action->defined }}
                    {{ if $gimme->publication->moderated_comments }}
                        <p><em>{{ #commentSentApproval# }}</em></p>
                    {{ /if }}
                {{ /if }}   
            {{ /if }}
          
            <h3>{{ #leaveReply# }}</h3>
            {{ comment_form html_code="id=\"commentform\"" submit_button="{{ #submit# }}" button_html_code="tabindex=\"6\"" }}
                <div class="form-element clearfix">
                    <label for="comment">{{ #comment# }}</label>
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" }}
                </div>

            	<div class="form-element clearfix">
                    <label for="f_captcha_code">{{ #enterCode# }}</label>
                    {{ recaptcha }}
                </div>
            {{ /comment_form }}

        {{ else }}
            <p>{{ #commentsLockedDisabled# }}</p>
        {{ /if }}
           
    {{ else }}
        <!-- user is not logged in -->
        {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
            {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                <p><em>{{ #commentNotAccepted# }}</em></p>
            {{ /if }}

            {{ if $gimme->submit_comment_action->is_error }}
                <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
            {{ else }}
                {{ if $gimme->submit_comment_action->defined }}
                    {{ if $gimme->publication->moderated_comments }}
                        <p><em>{{ #commentSentApproval# }}</em></p>
                    {{ /if }}
                {{ /if }}   
            {{ /if }}
          
            <h3>{{ #leaveReply# }}</h3>
            {{ comment_form html_code="id=\"commentform\"" submit_button="{{ #submit# }}" button_html_code="tabindex=\"6\"" }}
                <div class="form-element clearfix">
                    <label for="author"><small>{{ #nameRequired# }}</small></label>
                    {{ camp_edit object="comment" attribute="nickname" html_code="id=\"author\" tabindex=\"1\"" }}
                </div>

                <div class="form-element clearfix">
                    <label for="email"><small>{{ #emailRequired# }}</small></label>
                    {{ camp_edit object="comment" attribute="reader_email" html_code="id=\"email\" tabindex=\"2\"" }}
                </div>
                        
                <div class="form-element clearfix">
                    <label for="comment">{{ #comment# }}</label>
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" }}
                </div>

                <div class="form-element clearfix">
                    <label for="f_captcha_code">{{ #enterCode# }}</label>
                    {{ recaptcha }}
                </div>
            {{ /comment_form }}

        {{ else }}
            <p>{{ #commentsLockedDisabled# }}</p>
        {{ /if }}
    {{ /if }}
{{ /if }}
                  
</div><!-- /#comments -->
