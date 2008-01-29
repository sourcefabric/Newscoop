<?php
if (1) { 
    $menu_root->addSplit();
    $menu_modules =& DynMenuItem::Create("Plugins", "", 
        array("icon" => sprintf($iconTemplateStr, "plugin.png"), "id" => "plugins"));
    $menu_root->addItem($menu_modules);
    
	if ($g_user->hasPermission("plugin_interview_admin")) { 
        $menu_item =& DynMenuItem::Create(getGS('Administrate Interviews'), 
            "/$ADMIN/interview/admin/index.php",
            array("icon" => sprintf($iconTemplateStr, "interview.png")));
        $menu_modules->addItem($menu_item);	    
	}
	
	if ($g_user->hasPermission("plugin_interview_moderator")) { 
        $menu_item =& DynMenuItem::Create(getGS('Moderate Interviews'), 
            "/$ADMIN/interview/moderator/index.php",
            array("icon" => sprintf($iconTemplateStr, "interview.png")));
        $menu_modules->addItem($menu_item);	    
	}
	
	if ($g_user->hasPermission("plugin_interview_guest")) { 
        $menu_item =& DynMenuItem::Create(getGS('Interview Guest'), 
            "/$ADMIN/interview/guest/index.php",
            array("icon" => sprintf($iconTemplateStr, "interview.png")));
        $menu_modules->addItem($menu_item);	    
	}
} // if ($showUserMenu) 
?>
