{{ if $campsite->preview_blogcomment_action->defined }}
    <p>
    {{ if $campsite->preview_blogcomment_action->ok }} 
        <b>{{ if $campsite->language->name == "English" }}Blogcomment preview:{{ else }}Vista previa de blog comentario:{{ /if }}</b>
        <p>{{ if $campsite->language->name == "English" }}Your Name{{ else }}Su nombre{{ /if }}: {{ $campsite->preview{{ if $campsite->language->name == "English" }}Your Name{{ else }}Su nombre{{ /if }}nt_action->user_name }}<br>
        {{ if $campsite->language->name == "English" }}Your e-mail{{ else }}Su e-mail{{ /if }}: {{ $campsite->preview_blogcomment_action->user_email }}<br>
        {{ if $campsite->language->name == "English" }}Title{{ else }}Título{{ /if }}: {{ $campsite->preview_blogcomment_action->title }}<br>
        {{ if $campsite->language->name == "English" }}Comment{{ else }}Comentar{{ /if }}: {{ $campsite->preview_blogcomment_action->content }}<br>
        {{ if $campsite->language->name == "English" }}Mood{{ else }}Estado de ánimo{{ /if }}: {{ $campsite->preview_blogcomment_action->mood->name }}
    {{ else }}
        <b><i>{{ $campsite->preview_blogcomment_action->error_message }}</i></b> 
    {{ /if }}
           
    {{ include file="classic/tpl/blog/comment-form.tpl" }} 
        
{{ elseif $campsite->submit_blogcomment_action->defined }}

    {{ if $campsite->submit_blogcomment_action->ok }}
        <p>
        <i><b>{{ if $campsite->language->name == "English" }}Comment added{{ else }}Comentario añadido{{ /if }}</b></i>
    {{ else }}
        <p>
        <i><b>{{ $campsite->submit_blogcomment_action->error_message }}</b></i>
        {{ include file="classic/tpl/blog/comment-form.tpl" }} 
    {{ /if }}
    
{{ else }}

    {{ if $campsite->blog->comment_mode == 'login' && !$campsite->user->exists }}  
        {{ if $campsite->language->name == "English" }}You need to register/login to post blog comments{{ else }}Es necesario registrarse / iniciar sesión para enviar comentarios del blog{{ /if }}.     
    {{ else }}
        {{ include file="classic/tpl/blog/comment-form.tpl" }}  
    {{ /if }}
    
{{ /if }}
