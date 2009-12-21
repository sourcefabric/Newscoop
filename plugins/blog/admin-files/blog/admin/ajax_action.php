<?php
camp_load_translation_strings("plugin_blog");

function camp_blog_permission_check($p_action)
{
    global $g_user, $call_script;
    
    // User role depend on path to this file. Tricky: moderator and guest folders are just symlink to admin files!
    if (strpos($call_script, '/blog/admin/') !== false && $g_user->hasPermission('plugin_blog_admin')) {
        $is_admin = true;   
    }
    if (strpos($call_script, '/blog/moderator/') !== false && $g_user->hasPermission('plugin_blog_moderator')) {
        $is_moderator = true;   
    }
    
    switch ($p_action) {    
        case 'blogs_delete':
            if ($is_admin) {
                return true;    
            }
        break;
        
        case 'entries_delete':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        break;
        
        case 'comments_delete':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        break;
       
        case 'blogs_set_online':
        case 'blogs_set_offline':
        case 'blogs_set_moderated':
        case 'blogs_set_readonly':
        case 'blogs_set_pending':
            if ($is_admin) {
                return true;    
            }
        break;
        
        case 'entries_set_online':
        case 'entries_set_offline':
        case 'entries_set_pending':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        break;
        
        case 'comments_set_online':
        case 'comments_set_offline':
        case 'comments_set_pending':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        break;
        
    } 
    return false; 
}

$f_action = Input::Get('f_action', 'string');

if (!camp_blog_permission_check($f_action)) {
    echo getGS('You do not have the right to perform this action.');
    exit;    
}

switch ($f_action) {    
    case 'blogs_delete':
        $f_blogs = Input::Get('f_blogs', 'array');
        
        foreach ($f_blogs as $blog_id) {
            $Blog = new Blog($blog_id);
            $Blog->delete();   
        }
    break;
    
    case 'entries_delete':
        $f_entries = Input::Get('f_entries', 'array');
        
        foreach ($f_entries as $entry_id) {
            $BlogEntry = new BlogEntry($entry_id);
            $BlogEntry->delete();   
        }
    break;

        
    case 'comments_delete':
        $f_comments = Input::Get('f_comments', 'array');
        
        foreach ($f_comments as $comment_id) {
            $BlogComment = new BlogComment($comment_id);
            $BlogComment->delete();   
        }
    break;

    
    case 'blogs_set_online':
    case 'blogs_set_offline':
    case 'blogs_set_moderated':
    case 'blogs_set_readonly':
    case 'blogs_set_pending':
        $f_blogs = Input::Get('f_blogs', 'array');
        $status = substr($f_action, 10);
        
        foreach ($f_blogs as $blog_id) {
            $Blog = new Blog($blog_id);
            $Blog->setProperty('admin_status', $status);   
        }
    break;
    
   
    case 'entries_set_online':
    case 'entries_set_offline':
    case 'entries_set_pending':
        $f_entries = Input::Get('f_entries', 'array');
        $status = substr($f_action, 12);
        
        foreach ($f_entries as $entry_id) {
            $BlogEntry = new BlogEntry($entry_id);
            $BlogEntry->setProperty('admin_status', $status);   
        }
    break;

    case 'comments_set_online':
    case 'comments_set_offline':
    case 'comments_set_pending':
        $f_comments = Input::Get('f_comments', 'array');
        $status = substr($f_action, 13);
        
        foreach ($f_comments as $comment_id) {
            $BlogComment = new BlogComment($comment_id);
            $BlogComment->setProperty('admin_status', $status);   
        }
    break;
}

// Need to exit to avoid output of the menue.
exit;
?>
