{{ if $campsite->blog->user_id == $campsite->user->identifier || $campsite->user->has_permission('plugin_blog_admin') }}
    <small>
    <a href="{{ url }}&amp;f_blogcomment_action=delete">delete</a>
    </small>
{{ /if }}