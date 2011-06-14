{{ if $gimme->preview_blogcomment_action->defined }}
    <p>
    {{ if $gimme->preview_blogcomment_action->ok }} 
        <b>{{ if $gimme->language->name == "English" }}Blogcomment preview:{{ else }}Vista previa de blog comentario:{{ /if }}</b>
        <p>{{ if $gimme->language->name == "English" }}Your Name{{ else }}Su nombre{{ /if }}: {{ $gimme->preview{{ if $gimme->language->name == "English" }}Your Name{{ else }}Su nombre{{ /if }}nt_action->user_name }}<br>
        {{ if $gimme->language->name == "English" }}Your e-mail{{ else }}Su e-mail{{ /if }}: {{ $gimme->preview_blogcomment_action->user_email }}<br>
        {{ if $gimme->language->name == "English" }}Title{{ else }}Título{{ /if }}: {{ $gimme->preview_blogcomment_action->title }}<br>
        {{ if $gimme->language->name == "English" }}Comment{{ else }}Comentar{{ /if }}: {{ $gimme->preview_blogcomment_action->content }}<br>
        {{ if $gimme->language->name == "English" }}Mood{{ else }}Estado de ánimo{{ /if }}: {{ $gimme->preview_blogcomment_action->mood->name }}
    {{ else }}
        <b><i>{{ $gimme->preview_blogcomment_action->error_message }}</i></b> 
    {{ /if }}
           
    {{ include file="classic/tpl/blog/comment-form.tpl" }} 
        
{{ elseif $gimme->submit_blogcomment_action->defined }}

    {{ if $gimme->submit_blogcomment_action->ok }}
        <p>
        <i><b>{{ if $gimme->language->name == "English" }}Comment added{{ else }}Comentario añadido{{ /if }}</b></i>
    {{ else }}
        <p>
        <i><b>{{ $gimme->submit_blogcomment_action->error_message }}</b></i>
        {{ include file="classic/tpl/blog/comment-form.tpl" }} 
    {{ /if }}
    
{{ else }}

    {{ if $gimme->blog->comment_mode == 'login' && !$gimme->user->exists }}  
        {{ if $gimme->language->name == "English" }}You need to register/login to post blog comments{{ else }}Es necesario registrarse / iniciar sesión para enviar comentarios del blog{{ /if }}.     
    {{ else }}
        {{ include file="classic/tpl/blog/comment-form.tpl" }}  
    {{ /if }}
    
{{ /if }}
