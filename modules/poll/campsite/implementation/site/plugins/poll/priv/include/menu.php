<?php
if (1) { 
    $menu_root->addSplit();
    $menu_modules =& DynMenuItem::Create("Plugins", "", 
        array("icon" => sprintf($iconTemplateStr, "community.png"), "id" => "plugins"));
    $menu_root->addItem($menu_modules);
    
	if ($g_user->hasPermission("ManagePoll")) { 
        $menu_item =& DynMenuItem::Create(getGS("Manage Poll"), 
            "/$ADMIN/poll/",
            array("icon" => sprintf($iconTemplateStr, "poll.png")));
        $menu_modules->addItem($menu_item);	    
	}
} // if ($showUserMenu) 
?>
