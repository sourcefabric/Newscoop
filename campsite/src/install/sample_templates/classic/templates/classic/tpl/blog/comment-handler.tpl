{{ if $campsite->preview_blogcomment_action->defined }}
    <p>
    {{ if $campsite->preview_blogcomment_action->ok }} 
        <b>Blogcomment preview:</b>
        <p>
        Your Name: {{ $campsite->preview_blogcomment_action->user_name }}<br>
        Your EMail: {{ $campsite->preview_blogcomment_action->user_email }}<br>
        Title: {{ $campsite->preview_blogcomment_action->title }}<br>
        Comment: {{ $campsite->preview_blogcomment_action->content }}<br>
        Mood: {{ $campsite->preview_blogcomment_action->mood->name }}
    {{ else }}
        <b><i>{{ $campsite->preview_blogcomment_action->error_message }}</i></b> 
    {{ /if }}
           
    {{ include file="classic/tpl/blog/comment-form.tpl" }} 
        
{{ elseif $campsite->submit_blogcomment_action->defined }}

    {{ if $campsite->submit_blogcomment_action->ok }}
        <p>
        <i><b>Comment added</b></i>
    {{ else }}
        <p>
        <i><b>{{ $campsite->submit_blogcomment_action->error_message }}</b></i>
        {{ include file="classic/tpl/blog/comment-form.tpl" }} 
    {{ /if }}
    
{{ else }}

    {{ if $campsite->blog->comment_mode == 'login' && !$campsite->user->exists }}  
        You need to register/login to post blog comments.     
    {{ else }}
        {{ include file="classic/tpl/blog/comment-form.tpl" }}  
    {{ /if }}
    
{{ /if }}
