                <div class="comments">
{{ list_article_comments order="bydate desc"}}     
    {{ if $gimme->current_list->at_beginning }}           
                <div class="title">
                    <h2><span>{{ $gimme->article->comment_count }}</span> {{ #comments# }}</h2>
                    <p>{{ #somethingToSay# }} <a href="#">+ {{ #writeComment# }}</a></p>
                </div>
                	<ol>
    {{ /if }}                	

                    	<li>
                        	<img src="{{ url static_file="pictures/comment-avatar.jpg" }}" alt="" />
            {{ if $gimme->comment->user->identifier }}
                <h5><a href="http://{{ $gimme->publication->site }}/user/profile/{{ $gimme->comment->user->uname|urlencode }}">{{ $gimme->comment->user->uname }}</a></h5>
            {{ else }}
                <h5>{{ $gimme->comment->nickname }} {{ #anonymous# }}</h5>
            {{ /if }}                        	
                            <p>{{ $gimme->comment->content }}</p>
                            <span class="date">{{ include file="_tpl/relative_date.tpl" date=$gimme->comment->submit_date }}</span>
                        </li>

    {{ if $gimme->current_list->at_end }}      
                    </ol>
    {{ /if }}            
{{ /list_article_comments }}                    

<script type="text/javascript">
 var RecaptchaOptions = {
    theme : 'white',
    custom_theme_widget: 'recaptcha_widget'
 };
</script>
                    
                    <div class="title">
                        <h2>{{ #writeAComment# }}</h2>
                    </div>

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
          
            {{ comment_form submit_button="{{ #publish# }}" }}
               <fieldset class="clearfix">
                  <ul>
                    	<li class="left">               
                        {{ camp_edit object="comment" attribute="content" html_code="placeholder=\"{{ #commentPlaceholder# }}\"" }}
                     </li>
                     <li class="right">{{ recaptcha }}
            {{ /comment_form }}
                     </li>
                  </ul>
               </filedset>                  

        {{ else }}
            <p>{{ #commentsLockedDisabled# }}</p>
        {{ /if }}
           
    {{ else }}
        <!-- user is not logged in -->
        <p>{{ #haveToRegister# }}</p>
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
          
            {{ comment_form submit_button="{{ #publish# }}" }}
               <fieldset class="clearfix">
                  <ul>
                    	<li class="left">               
                        {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" html_code="placeholder=\"{{ #commentPlaceholder# }}\"" }}
                     </li>
                     <li class="right">{{ recaptcha }}
            {{ /comment_form }}
							</li>
                  </ul>
                </filedset>            

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
          
            {{ comment_form submit_button="{{ #publish# }}" }}
               <fieldset class="clearfix">
                  <ul>
                    	<li class="left">               
                        {{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" tabindex=\"4\"" html_code="placeholder=\"{{ #commentPlaceholder# }}\"" }}
                     </li>
                    
                     <li class="right">
                         {{ camp_edit object="comment" attribute="nickname" html_code="placeholder=\"{{ #nicknamePlaceholder# }}\"" }}
                         {{ camp_edit object="comment" attribute="reader_email" html_code="placeholder=\"{{ #emailPlaceholder# }}\"" }}                     
                         {{ recaptcha }}
            {{ /comment_form }}
                     </li>
                  </ul>
                </filedset>            
            
        {{ else }}
            <p>{{ #commentsLockedDisabled# }}</p>
        {{ /if }}
    {{ /if }}
{{ /if }}
                
                </div>
