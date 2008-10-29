<?php

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
    if (strpos($call_script, '/blog/guest/') !== false && $g_user->hasPermission('plugin_blog_guest')) {
        $is_guest = true;   
    }
    
    switch ($p_action) {    
        case 'blogs_delete':
            if ($is_admin) {
                return true;    
            }
        break;
        
        case 'items_delete':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        break;
        
        case 'blogs_setdraft':
        case 'blogs_setpending':
        case 'blogs_setpublished':
        case 'blogs_setrejected':
            if ($is_admin) {
                return true;    
            }
        break;
        
        case 'items_setdraft':
        case 'items_setpending':
        case 'items_setpublished':
        case 'items_setrejected':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        break;
        
        case 'item_move_up_rel':
        case 'item_move_down_rel':
        case 'item_move_abs':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        return false;
        
        case 'blog_move_up_rel':
        case 'blog_move_down_rel':
        case 'blog_move_abs':
            if ($is_admin || $is_moderator) {
                return true;    
            }
        return false;
    }  
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
    
    case 'items_delete':
        $f_items = Input::Get('f_items', 'array');
        
        foreach ($f_items as $item_id) {
            $BlogItem = new BlogItem(null, $item_id);
            $BlogItem->delete();   
        }
    break;
    
    case 'blogs_setdraft':
    case 'blogs_setpending':
    case 'blogs_setpublished':
    case 'blogs_setrejected':
        $f_blogs = Input::Get('f_blogs', 'array');
        $status = substr($f_action, 14);
        
        foreach ($f_blogs as $blog_id) {
            $Blog = new Blog($blog_id);
            $Blog->setProperty('status', $status);   
        }
    break;
    
    case 'items_setdraft':
    case 'items_setpending':
    case 'items_setpublished':
    case 'items_setrejected':
        $f_items = Input::Get('f_items', 'array');
        $status = substr($f_action, 9);
        
        foreach ($f_items as $item_id) {
            $BlogItem = new BlogItem(null, $item_id);
            $BlogItem->setProperty('status', $status);   
        }
    break;
    
    case 'item_move_up_rel':
    case 'item_move_down_rel':
        $f_items = Input::Get('f_items', 'array');
        list(,,$dir,) = explode('_', $f_action);
       
        foreach ($f_items as $item_id) {
            $BlogItem = new BlogItem(null, $item_id);
            $BlogItem->positionRelative($dir);   
        }
        
    break;
    
    case 'item_move_abs':
        $f_items = Input::Get('f_items', 'array');
        $f_new_pos = Input::Get('f_new_pos', 'int');
       
        foreach ($f_items as $item_id) {
            $BlogItem = new BlogItem(null, $item_id);
            $BlogItem->positionAbsolute($f_new_pos);   
        }
        
    break;
    
    case 'blog_move_up_rel':
    case 'blog_move_down_rel':
        $f_blogs = Input::Get('f_blogs', 'array');
        list(,,$dir,) = explode('_', $f_action);
       
        foreach ($f_blogs as $blog_id) {
            $Blog = new Blog($blog_id);
            $Blog->positionRelative($dir);   
        }
        
    break;
    
    case 'blog_move_abs':
        $f_blogs = Input::Get('f_blogs', 'array');
        $f_new_pos = Input::Get('f_new_pos', 'int');
       
        foreach ($f_blogs as $blog_id) {
            $Blog = new Blog($blog_id);
            $Blog->positionAbsolute($f_new_pos);   
        }
        
    break;
}

// Need to exit to avoid output of the menue.
exit;
?>
