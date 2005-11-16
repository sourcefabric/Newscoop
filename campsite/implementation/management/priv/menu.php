<?php
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DynMenuItem.php");
load_common_include_files("home");
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	exit;
}

$showPublishingEnvironmentMenu = ($User->hasPermission("ManageTempl") 
	|| $User->hasPermission("DeleteTempl") 
	|| $User->hasPermission("ManageArticleTypes") 
	|| $User->hasPermission("DeleteArticleTypes") 
	|| $User->hasPermission("ManageTopics") 
	|| $User->hasPermission("ManageLanguages") 
	|| $User->hasPermission("DeleteLanguages") 
	|| $User->hasPermission("ManageCountries") 
	|| $User->hasPermission("DeleteCountries"));

$showConfigureMenu = ($showPublishingEnvironmentMenu
	|| $User->hasPermission("ManageLocalizer") 
	|| $User->hasPermission("ViewLogs"));

$showUserMenu = ($User->hasPermission("ManageUsers") 
	|| $User->hasPermission("DeleteUsers") 
	|| $User->hasPermission("ManageSubscriptions") 
	|| $User->hasPermission("ManageUserTypes")
	|| $User->hasPermission("ManageReaders"));
	
$iconTemplateStr = '<img src="'.$Campsite['ADMIN_IMAGE_BASE_URL'].'/%s" align="middle" style="padding-bottom: 3px;" width="22" height="22" />';

DynMenuItem::SetMenuType("DynMenuItem_JsCook");
$menu_root =& DynMenuItem::Create('', '');
$menu_item =& DynMenuItem::Create(getGS('Home'), "/$ADMIN/home.php", 
                array('icon' => sprintf($iconTemplateStr, 'home.png'), 'id' => 'home'));
$menu_root->addItem($menu_item);
$menu_root->addSplit();
$menu_content =& DynMenuItem::Create(getGS('Content'), '', 
                array('icon' => sprintf($iconTemplateStr, 'content.png'), 'id' => 'content'));
$menu_root->addItem($menu_content);
$menu_item =& DynMenuItem::Create(getGS('Publications'), "/$ADMIN/pub/index.php", 
                array('icon' => sprintf($iconTemplateStr, 'publication.png'), 'id' => 'publication'));
$menu_content->addItem($menu_item);
$menu_item =& DynMenuItem::Create(getGS('Image Archive'), "/$ADMIN/imagearchive/index.php", 
                array('icon' => sprintf($iconTemplateStr, 'image_archive.png'), 'id' => 'image_archive'));
$menu_content->addItem($menu_item);
$menu_content->addSplit();

$publications = Publication::GetPublications();
$issues = array();
$sections = array();
foreach ($publications as $publication) {
	$issues[$publication->getPublicationId()] = 
		Issue::GetIssues($publication->getPublicationId(), null, null, null, 
			array('ORDER BY'=>array('Number'=>'DESC'), 'LIMIT' => '5'));
	foreach ($issues[$publication->getPublicationId()] as $issue) {
		$sections[$issue->getPublicationId()][$issue->getIssueNumber()][$issue->getLanguageId()] = 
			Section::GetSections($issue->getPublicationId(), 
				$issue->getIssueNumber(), $issue->getLanguageId());
	}
}

$icon_bullet = '<img src="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/tol.gif" align="middle" style="padding-bottom: 3px;" width="16" height="16" />';
foreach ($publications as $publication) {
    $pubId = $publication->getPublicationId();
    $menu_item_pub =& DynMenuItem::Create(htmlspecialchars($publication->getName()), 
                                          "/$ADMIN/issues/index.php?Pub=$pubId",
                                          array("icon" => $icon_bullet));
    $menu_content->addItem($menu_item_pub);
	if (isset($issues[$pubId])) {
		foreach ($issues[$pubId] as $issue) {
			$issueId = $issue->getIssueNumber();
			$languageId = $issue->getLanguageId();
			$menu_item_issue =& DynMenuItem::Create(htmlspecialchars($issue->getName()),
			     "/$ADMIN/sections/index.php?Pub=$pubId&Issue=$issueId&Language=$languageId",
			     array("icon" => $icon_bullet));
			$menu_item_pub->addItem($menu_item_issue);
			if (isset($sections[$pubId][$issueId][$languageId])) {
				foreach ($sections[$pubId][$issueId][$languageId] as $section) {
				    $sectionId = $section->getSectionNumber();
				    $menu_item_section =& DynMenuItem::Create(
				        htmlspecialchars($section->getName()),
				        "/$ADMIN/articles/index.php"
				        ."?f_publication_id=$pubId"
				        ."&f_issue_number=$issueId"
				        ."&f_language_id=$languageId"
				        ."&f_section_number=$sectionId",
				        array("icon" => $icon_bullet));
				    $menu_item_issue->addItem($menu_item_section);
				}
			}
			$menu_item_issue->addSplit();
			$menu_item =& DynMenuItem::Create(getGS("More..."), 
                "/$ADMIN/issues/index.php?Pub=$pubId",
                array("icon" => $icon_bullet));
            $menu_item_issue->addItem($menu_item);            
		}
	}
}	    
$menu_root->addSplit();
$menu_actions =& DynMenuItem::Create(getGS("Actions"), '', 
    array("icon" => sprintf($iconTemplateStr, "actions.png"), "id" => "actions"));
$menu_root->addItem($menu_actions);

if ($User->hasPermission("AddArticle")) { 
    $menu_item =& DynMenuItem::Create(getGS('Add new article'), "/$ADMIN/pub/add_article.php",
        array("icon" => sprintf($iconTemplateStr, "actions.png")));
    $menu_actions->addItem($menu_item);
}
    
if ($User->hasPermission("ManageTempl")) { 
    $menu_item =& DynMenuItem::Create(getGS('Upload new template'), 
        "/$ADMIN/templates/upload_templ.php?Path=/look/&Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "upload_template.png")));
    $menu_actions->addItem($menu_item);
}
    
if ($User->hasPermission("ManagePub")) { 
    $menu_item =& DynMenuItem::Create(getGS("Add new publication"), 
        "/$ADMIN/pub/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_publication.png")));
    $menu_actions->addItem($menu_item);
}    
	
if ($User->hasPermission("ManageUsers")) { 
    $menu_item =& DynMenuItem::Create(getGS("Add new staff member"), 
        "/$ADMIN/users/edit.php?uType=Staff&Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_user.png")));
    $menu_actions->addItem($menu_item);
}    
	
if ($User->hasPermission("ManageUsers")) { 
    $menu_item =& DynMenuItem::Create(getGS("Add new subscriber"), 
        "/$ADMIN/users/edit.php?uType=Subscribers&Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_user.png")));
    $menu_actions->addItem($menu_item);
}    
	
if ($User->hasPermission("ManageUserTypes")) { 
    $menu_item =& DynMenuItem::Create(getGS("Add new user type"), 
        "/$ADMIN/user_types/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_user_type.png")));
    $menu_actions->addItem($menu_item);
}    
	
if ($User->hasPermission("ManageArticleTypes")) { 
    $menu_item =& DynMenuItem::Create(getGS("Add new article type"), 
        "/$ADMIN/article_types/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_article_type.png")));
    $menu_actions->addItem($menu_item);
}    
	
if ($User->hasPermission("ManageCountries")) { 
    $menu_item =& DynMenuItem::Create(getGS("Add new country"), 
        "/$ADMIN/country/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_country.png")));
    $menu_actions->addItem($menu_item);
}    
	
if ($User->hasPermission("ManageLanguages")) { 
    $menu_item =& DynMenuItem::Create(getGS("Add new language"), 
        "/$ADMIN/languages/add_modify.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_language.png")));
    $menu_actions->addItem($menu_item);
}    

$menu_item =& DynMenuItem::Create(getGS("Change your password"), 
    "/$ADMIN/users/edit.php?uType=Staff&User=".$User->getId(),
    array("icon" => sprintf($iconTemplateStr, "change_password.png")));
$menu_actions->addItem($menu_item);

if ($User->hasPermission("InitializeTemplateEngine")) { 
    $menu_item =& DynMenuItem::Create(getGS('Restart the template engine'), "/$ADMIN/home.php?restart_engine=yes",
        array("icon" => sprintf($iconTemplateStr, "actions.png")));
    $menu_actions->addItem($menu_item);
}

if ($showConfigureMenu) { 
    $menu_root->addSplit();
    $menu_config =& DynMenuItem::Create(getGS("Configure"), "",
        array("icon" => sprintf($iconTemplateStr, "configure.png"), "id"=>"configure"));
    $menu_root->addItem($menu_config);
    
    if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl")) { 
        $menu_item =& DynMenuItem::Create(getGS("Templates"), 
            "/$ADMIN/templates/",
            array("icon" => sprintf($iconTemplateStr, "templates.png")));
        $menu_config->addItem($menu_item);
    }
    if ($User->hasPermission("ManageArticleTypes") || $User->hasPermission("DeleteArticleTypes")) { 
        $menu_item =& DynMenuItem::Create(getGS("Article Types"), 
            "/$ADMIN/article_types/",
            array("icon" => sprintf($iconTemplateStr, "article_types.png")));
        $menu_config->addItem($menu_item);
    }        
    if ($User->hasPermission("ManageTopics")) { 
        $menu_item =& DynMenuItem::Create(getGS("Topics"), 
            "/$ADMIN/topics/",
            array("icon" => sprintf($iconTemplateStr, "topics.png")));
        $menu_config->addItem($menu_item);
    }
    if ($User->hasPermission("ManageLanguages") || $User->hasPermission("DeleteLanguages")) { 
        $menu_item =& DynMenuItem::Create(getGS("Languages"), 
            "/$ADMIN/languages/",
            array("icon" => sprintf($iconTemplateStr, "languages.png")));
        $menu_config->addItem($menu_item);        
    }
    if ($User->hasPermission("ManageCountries") || $User->hasPermission("DeleteCountries")) { 
        $menu_item =& DynMenuItem::Create(getGS("Countries"), 
            "/$ADMIN/country/",
            array("icon" => sprintf($iconTemplateStr, "countries.png")));
        $menu_config->addItem($menu_item);        
    }
    if ($showPublishingEnvironmentMenu) { 
        $menu_config->addSplit();
    }
	if ($User->hasPermission("ManageLocalizer")) { 
        $menu_item =& DynMenuItem::Create(getGS("Localizer"), 
            "/$ADMIN/localizer/",
            array("icon" => sprintf($iconTemplateStr, "localizer.png")));
        $menu_config->addItem($menu_item);	    
	}
	if ($User->hasPermission("ViewLogs")) { 
        $menu_item =& DynMenuItem::Create(getGS("Logs"), 
            "/$ADMIN/logs/",
            array("icon" => sprintf($iconTemplateStr, "logs.png")));
        $menu_config->addItem($menu_item);	    
	}
} // if ($showConfigureMenu) 

if ($showUserMenu) { 
    $menu_root->addSplit();
    $menu_users =& DynMenuItem::Create("Users", "", 
        array("icon" => sprintf($iconTemplateStr, "users.png"), "id" => "users"));
    $menu_root->addItem($menu_users);
	if ($User->hasPermission("ManageUsers") || $User->hasPermission("DeleteUsers")) { 
        $menu_item =& DynMenuItem::Create(getGS("Staff"), 
            "/$ADMIN/users/?uType=Staff",
            array("icon" => sprintf($iconTemplateStr, "users.png")));
        $menu_users->addItem($menu_item);	    
	}
	if ($User->hasPermission("ManageReaders") || $User->hasPermission("ManageSubscriptions")) { 
        $menu_item =& DynMenuItem::Create(getGS("Subscribers"), 
            "/$ADMIN/users/?uType=Subscribers",
            array("icon" => sprintf($iconTemplateStr, "users.png")));
        $menu_users->addItem($menu_item);	    
	}
	if ($User->hasPermission("ManageUserTypes")) { 
        $menu_item =& DynMenuItem::Create(getGS("Staff User Types"), 
            "/$ADMIN/user_types/",
            array("icon" => sprintf($iconTemplateStr, "user_types.png")));
        $menu_users->addItem($menu_item);	    
	}
} // if ($showUserMenu) 

$menu_root->addSplit();
$menu_help =& DynMenuItem::Create("Help", "", 
    array("icon" => sprintf($iconTemplateStr, "help.png"), "id" => "help"));
$menu_root->addItem($menu_help);
$menu_item =& DynMenuItem::Create(getGS("Help"), $Campsite['HELP_URL'],
    array("icon" => sprintf($iconTemplateStr, "help.png"), "target" => "_blank"));
$menu_help->addItem($menu_item);
$menu_item =& DynMenuItem::Create(getGS("About"), $Campsite['ABOUT_URL'],
    array("icon" => sprintf($iconTemplateStr, "about.png"), "target" => "_blank"));
$menu_help->addItem($menu_item);


?>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<script language="JavaScript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/JSCookMenu.js" type="text/javascript"></script>
	<LINK REL="stylesheet" HREF="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/ThemeOffice/theme.css" TYPE="text/css">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
<script language="JavaScript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/ThemeOffice/theme.js" type="text/javascript"></script>
    <?php echo $menu_root->createMenu("myMenu"); ?>
	<TITLE>Campsite <?php p($Campsite['VERSION']); ?></TITLE>
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table cellpadding="0" cellspacing="0" bgcolor="#8BAED1" width="100%">
<tr>
    <td style="padding-left: 13px; padding-top: 1px; padding-bottom: 2px;">
        <a href="/<?php p($ADMIN) ?>/home.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/sign_big3.gif" BORDER="0" align="middle"></a>
    </td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 2px solid #D5E2EE; padding-top: 4px;" bgcolor="#d5e2ee"> 
<tr>
	<td valign="top" align="left" width="70%">
	   <table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="padding-left: 13px; padding-top: 0px; padding-bottom: 0px;" valign="top">
			<DIV ID="myMenuID"></DIV>
			<SCRIPT LANGUAGE="JavaScript"><!--
				cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
			--></SCRIPT>
			</td>
		</tr>
		</table>
	</td>
	<td align="right" valign="bottom" width="30%" style="padding-bottom: 3px;">
        <table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
			<td align="right" style="padding-top: 0px;">
                <table cellpadding="0" cellspacing="0">
				<TR>
            		<td align="right" style="font-size: 8pt; padding-right: 5px; padding-top: 0px;" colspan="4"><?php putGS("Signed in: $1", "<b>".$User->getName()."</b>"); ?></td>		
					<td style="padding-left: 10px;"><A HREF="/<?php p($ADMIN); ?>/logout.php"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/logout.png" width="22" height="22" border="0" alt="<?php putGS('Logout'); ?>"></a></td>
					<td style="font-weight: bold; padding-left: 2px; padding-right: 10px;"><A HREF="/<?php p($ADMIN); ?>/logout.php" style="color: black; text-decoration: none;"><?php putGS('Logout'); ?></a></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>