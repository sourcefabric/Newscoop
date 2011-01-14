{{ if $gimme->blog->user_id == $gimme->user->identifier || $gimme->user->has_permission('plugin_blog_admin') }}
    <small>
    <a href="{{ url }}&amp;f_blogcomment_action=delete">delete</a>
    </small>
{{ /if }}