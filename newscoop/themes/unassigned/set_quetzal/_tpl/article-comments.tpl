<!-- _tpl/article-comments.tpl-->
<section id="comments">
    <div class="row">
        <div class="span6">
            <h2>{{ $gimme->article->comment_count}} {{ #comments# }}</h2>
        </div>
        <div class="span2 write-comment">
            <a href="#comment-form" class="link-color">{{ #writeComment# }}</a>
        </div>
    </div>
    {{list_article_comments order="bydate desc"}}
    <div class="row comment-box" id="comment-{{$gimme->current_list->index}}">
        <div class="span1">
            {{ if $gimme->comment->user->identifier }}
            <a href="http://{{ $gimme->publication->site }}/user/profile/{{ $gimme->comment->user->uname|urlencode }}" class="avatar">
                <img src="{{ include file='_tpl/user-image.tpl' user=$user width=60 height=60 }}" alt="{{ $gimme->comment->user->uname }}">
            </a>
            {{ else }}
                <img src="{{ include file='_tpl/user-image.tpl' user=$user width=60 height=60 }}" alt="{{ $gimme->comment->user->uname }}">
            {{ /if }}
        </div>
        <div class="span7 comment-content">                                                
            <h4 class="pull-left comment-author link-color">
            {{ if $gimme->comment->user->identifier }}
                <a href="http://{{ $gimme->publication->site }}/user/profile/{{ $gimme->comment->user->uname|urlencode }}">{{ $gimme->comment->user->uname }}</a>
            {{ else }}
                {{ $gimme->comment->nickname }} ({{ #anonymous# }})
            {{ /if }}
            </h4>
            <div class="pull-right comment-date">
                <time class="timeago" datetime="{{ $gimme->comment->submit_date }}">{{ $gimme->comment->submit_date }}</time>
            </div>
            <div class="clearfix"></div>
            <div class="comment-body">
             {{ $gimme->comment->content}}
            </div>                                                
        </div>
    </div>
    {{/list_article_comments}}
    
    <div class="divider"></div>

    <!-- COMMENT FORM -->
    <section id="comment-form">
        <div class="row">
            <div class="span6">
                <h2>{{ #writeComment# }}</h2>
            </div>
            <div class="span2 write-comment">
                {{ if !$gimme->user->logged_in}}
                <a href="{{ $view->url(['controller' => 'auth', 'action' =>'index'], 'default') }}" class="link-color">{{ #loginOrSignUp# }}</a>
                {{/if}}
            </div>                                                
        </div>
        {{ if !$gimme->publication->public_comments }}
            <!-- public comments are not allowed-->
            {{ if $gimme->user->logged_in }}
                <!-- user is logged in -->
                {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
                    {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                    <p><em>{{ #yourCommentHasNotBeenAccepted# }}</em></p>
                    {{ /if }}

                    {{ if $gimme->submit_comment_action->is_error }}
                        <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
                    {{ else }}
                        {{ if $gimme->submit_comment_action->defined }}
                            {{ if $gimme->publication->moderated_comments }}
                                <p><em>{{ #yourCommentHasBeenSentForApproval# }}</em></p>
                            {{ /if }}
                        {{ /if }}   
                    {{ /if }}
            {{ comment_form html_code="id=\"commentform\"" _button="submit" button_html_code="tabindex=\"6\" class=\"btn btn-large pull-right\" " }}
            <div class="row">                                                
                <div class="span4">
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\" placeholder=\"Write your message here\" " }}
                </div>
                <div class="span4">
                    {{ recaptcha }}
                </div>
            </div>
            {{ /comment_form }}
            {{ else }}
                <p>{{ #commentsLocked# }}</p>
            {{ /if }}
        {{ else }}
            <p>{{ #registrationNeeded# }}</p>
        {{ /if }}
    {{ else }}
        <!-- public comments are allowed-->
        {{ if $gimme->user->logged_in }}
            {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
            {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                <p><em>{{ #yourCommentHasNotBeenAccepted# }}</em></p>
            {{ /if }}

            {{ if $gimme->submit_comment_action->is_error }}
                <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
            {{ else }}
                {{ if $gimme->submit_comment_action->defined }}
                    {{ if $gimme->publication->moderated_comments }}
                        <p><em>{{ #yourCommentHasBeenSentForApproval# }}</em></p>
                    {{ /if }}
                {{ /if }}   
            {{ /if }}

            {{ comment_form html_code="id=\"commentform\"" _button="submit" button_html_code="tabindex=\"6\" class=\"btn btn-large pull-right\" " }}
            <div class="row">                                                
                <div class="span4 login-textarea">
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\" placeholder=\"Write your message here\" " }}
                </div>
                <div class="span4">
                    {{ recaptcha }}
                </div>
            </div>
            {{ /comment_form }}
            {{ else }}
                <p>{{ #commentsLocked# }}</p>
            {{ /if }}
        {{ else }}
            <!-- user is not logged in -->
            {{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
                {{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
                    <p><em>{{ #yourCommentHasNotBeenAccepted# }}</em></p>
                {{ /if }}

                {{ if $gimme->submit_comment_action->is_error }}
                    <p><em>{{ $gimme->submit_comment_action->error_message }}</em></p>
                {{ else }}
                    {{ if $gimme->submit_comment_action->defined }}
                        {{ if $gimme->publication->moderated_comments }}
                            <p><em>{{ #yourCommentHasBeenSentForApproval# }}</em></p>
                        {{ /if }}
                    {{ /if }}   
                {{ /if }}


            {{ comment_form html_code="id=\"commentform\"" _button="submit" button_html_code="tabindex=\"6\" class=\"btn btn-large pull-right\" " }}
            <div class="row">                                                
                <div class="span4">
                    {{ camp_edit object="comment" attribute="nickname" html_code="id=\"author\" tabindex=\"1\" placeholder=\"Your name\" " }}
                    {{ camp_edit object="comment" attribute="reader_email" html_code="id=\"email\" tabindex=\"2\" placeholder=\"Your Email\"" }}
                    {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\" placeholder=\"Write your message here\" " }}
                </div>
                <div class="span4">
                    {{ recaptcha }}
                </div>
            </div>

        {{ /comment_form }}
            {{ else }}
            <p>{{ #commentsLocked# }}</p>
        {{ /if }}
    {{ /if }}
{{ /if }}

</section>
<!--  end _tpl/article-comments.tpl-->
