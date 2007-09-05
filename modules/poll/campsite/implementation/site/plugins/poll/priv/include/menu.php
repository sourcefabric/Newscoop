<?php
if (1) { 
    $menu_root->addSplit();
    $menu_modules =& DynMenuItem::Create("Community", "", 
        array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/community.png"), "id" => "../admin/modules/icon/modules"));
    $menu_root->addItem($menu_modules);
    
	if ($g_user->hasPermission("ManagePhorum")) { 
        $menu_item =& DynMenuItem::Create(getGS("Manage Phorum"), 
            "/$ADMIN/modules/admin/phorum/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/phorum.png")));
        $menu_modules->addItem($menu_item);	    
	}
	if ($g_user->hasPermission("ManagePoll")) { 
        $menu_item =& DynMenuItem::Create(getGS("Manage Poll"), 
            "/$ADMIN/modules/admin/poll/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/poll.png")));
        $menu_modules->addItem($menu_item);	    
	}
	if ($g_user->hasPermission("ManageTimer")) { 
        $menu_item =& DynMenuItem::Create(getGS("Manage Timer"), 
            "/$ADMIN/modules/admin/timer/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/timer.png")));
        $menu_modules->addItem($menu_item);	    
	}
	if ($g_user->hasPermission("VotingRes")) { 
        $menu_item =& DynMenuItem::Create(getGS("Voting Results"), 
            "/$ADMIN/modules/admin/voting/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/voting.png")));
        $menu_modules->addItem($menu_item);	    
	}
	if ($g_user->hasPermission("ManageNewsletter")) { 
        $menu_item =& DynMenuItem::Create(getGS("Send Newsletter"), 
            "/$ADMIN/modules/admin/newsletter/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/newsletter.png")));
        $menu_modules->addItem($menu_item);	    
	}
	if ($g_user->hasPermission("ModerateArticleComments")) { 
        $menu_item =& DynMenuItem::Create(getGS("Moderate Article Comments"), 
            "/$ADMIN/modules/admin/moderate_article_comments/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/article_comments.png")));
        $menu_modules->addItem($menu_item);	    
	}
	if ($g_user->hasPermission("ManageSearchtracker")) { 
        $menu_item =& DynMenuItem::Create(getGS("Manage Searchtracker"), 
            "/$ADMIN/modules/admin/searchtracker/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/searchtracker.png")));
        $menu_modules->addItem($menu_item);	    
	}
	if ($g_user->hasPermission("ProfileAttributes")) { 
        $menu_item =& DynMenuItem::Create(getGS("Edit Profile Attributes"), 
            "/$ADMIN/modules/admin/profile/",
            array("icon" => sprintf($iconTemplateStr, "../admin/modules/icon/attributes.png")));
        $menu_modules->addItem($menu_item);	    
	}
} // if ($showUserMenu) 
?>
