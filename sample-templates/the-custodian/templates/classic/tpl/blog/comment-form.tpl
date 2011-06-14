{{ if $gimme->blog->comment_mode == 'registered' && !$gimme->user->defined }}  

    <p><i><b>{{ if $gimme->language->name == "English" }}You need to register/login to post blog comments{{ else }}Es necesario registrarse / iniciar sesión para enviar comentarios del blog{{ /if }}.</b></i></p> 
     
{{ else }}

    {{ blogcomment_form submit_button="Submit" preview_button="Preview" template="classic/tpl/blog/section-blog.tpl" }}
        <table>
            <tr><th colspan="2" align="left">{{ if $gimme->language->name == "English" }}Add Comment{{ else }}Añadir comentario{{ /if }}:</th></tr>
            
            {{ if !$gimme->user->defined }}
                <tr><td>{{ if $gimme->language->name == "English" }}Your Name{{ else }}Su nombre{{ /if }}</td><td>{{ blogcomment_edit attribute=user_name }}</td></tr>
                <tr><td>{{ if $gimme->language->name == "English" }}Your Email{{ else }}Su email{{ /if }}</td><td> {{ blogcomment_edit attribute=user_email }}</td></tr>
            {{ /if }}
           
            <tr><td>{{ if $gimme->language->name == "English" }}Title{{ else }}Título{{ /if }}</td><td> {{ blogcomment_edit attribute=title }}</td></tr>
            <tr><td>{{ if $gimme->language->name == "English" }}Comment{{ else }}Comentar{{ /if }}</td><td> {{ blogcomment_edit attribute=content  wysiwyg=1 html_code="rows=6 cols=30" }}</td></tr>
            <tr><td>{{ if $gimme->language->name == "English" }}Mood{{ else }}Estado de ánimo{{ /if }}</td><td> {{ blogcomment_edit attribute=mood  html_code="size=39" }}</td></tr>
            
            {{ if $gimme->blog->captcha_enabled }}
                <tr><td><img src="{{ captcha_image_link }}"></td><td>{{ camp_edit object="captcha" attribute="code" }}</td></tr>
            {{ /if }}
            
        </table>
    {{ /blogcomment_form }}
    
{{ /if }}
