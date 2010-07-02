{{ if $campsite->blog->comment_mode == 'registered' && !$campsite->user->defined }}  

    <p><i><b>You need to register/login to post blog comments.</b></i></p> 
     
{{ else }}

    {{ blogcomment_form submit_button="Submit" preview_button="Preview" template="classic/tpl/blog/section-blog.tpl" }}
        <table>
            <tr><th colspan="2" align="left">Add Comment:</th></tr>
            
            {{ if !$campsite->user->defined }}
                <tr><td>Your Name</td><td>{{ blogcomment_edit attribute=user_name }}</td></tr>
                <tr><td>Your EMail</td><td> {{ blogcomment_edit attribute=user_email }}</td></tr>
            {{ /if }}
            
            <tr><td>Comment</td><td> {{ blogcomment_edit attribute=content  wysiwyg=1 html_code="rows=6 cols=30" }}</td></tr>
            <tr><td>Mood</td><td> {{ blogcomment_edit attribute=mood  html_code="size=39" }}</td></tr>
            
            {{ if $campsite->blog->captcha_enabled }}
                <tr><td><img src="{{ captcha_image_link }}"></td><td>{{ camp_edit object="captcha" attribute="code" }}</td></tr>
            {{ /if }}
            
        </table>
    {{ /blogcomment_form }}
    
{{ /if }}
