{{ if $campsite->blog->user_id == $campsite->user->identifier || $campsite->user->has_permission('plugin_blog_admin') }}
    <small>
    <a href="{{ url }}&amp;f_blogaction=entry_delete">delete</a>
    </small>
{{ /if }}