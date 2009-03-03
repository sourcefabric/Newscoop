<?PHP
$f_mode = Input::Get('f_mode', 'string');

switch ($f_mode) {
    case 'blog_topic':// Check permissions
        if (!$g_user->hasPermission('plugin_blog_admin')) {
            camp_html_display_error(getGS('You do not have the right to manage blogs.'));
            exit;
        }
        $f_blog_id = Input::Get('f_blog_id', 'int');
        $f_topic_ids = Input::Get('f_topic_ids', 'array', array(), 1);
        $topics = Blog::GetTopicTree();
        $Blog = new Blog($f_blog_id);
        $Blog->setTopics($f_topic_ids);
    break;
    
    case 'entry_topic':
    case 'entry_mood':
        if (!$g_user->hasPermission('plugin_blog_admin') && !$g_user->hasPermission('plugin_blog_moderator')) {
            camp_html_display_error(getGS('You do not have the right to manage blog entries.'));
            exit;
        }
        $f_blogentry_id = Input::Get('f_blogentry_id', 'int');
        $f_topic_ids = Input::Get('f_topic_ids', 'array', array(), 1);
        $topics = Blog::GetTopicTree();
        $BlogEntry = new BlogEntry($f_blogentry_id);
        $BlogEntry->setTopics($f_topic_ids);
    break;   
    
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

?>
<script>
window.opener.location.reload();
window.close();
</script>
