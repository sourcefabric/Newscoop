<?php
require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
require_once($GLOBALS['g_campsiteDir']."/classes/DynMenuItem.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");

camp_load_translation_strings("home");
global $ADMIN;
global $g_user;

$showPublishingEnvironmentMenu = ($g_user->hasPermission("ManageTempl")
    || $g_user->hasPermission("DeleteTempl")
    || $g_user->hasPermission("ManageArticleTypes")
    || $g_user->hasPermission("DeleteArticleTypes")
    || $g_user->hasPermission("ManageTopics")
    || $g_user->hasPermission("ManageLanguages")
    || $g_user->hasPermission("DeleteLanguages")
    || $g_user->hasPermission("ManageCountries")
    || $g_user->hasPermission("DeleteCountries"));

$showConfigureMenu = ($showPublishingEnvironmentMenu
    || $g_user->hasPermission("ManageLocalizer")
    || $g_user->hasPermission("ViewLogs"));

$showUserMenu = ($g_user->hasPermission("ManageUsers")
    || $g_user->hasPermission("DeleteUsers")
    || $g_user->hasPermission("ManageSubscriptions")
    || $g_user->hasPermission("ManageUserTypes")
    || $g_user->hasPermission("ManageReaders")
    || $g_user->hasPermission("SyncPhorumUsers"));

$showAdminActions = (($g_user->hasPermission("ManageIssue") && $g_user->hasPermission("AddArticle"))
             || (CampCache::IsEnabled() && $g_user->hasPermission("ClearCache")));

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
                array('icon' => sprintf($iconTemplateStr, 'publications.png'), 'id' => 'publication'));
$menu_content->addItem($menu_item);

if ($g_user->hasPermission('CommentModerate')) {
    $menu_item =& DynMenuItem::Create(getGS('Comments'), "/$ADMIN/comments/index.php",
                    array('icon' => sprintf($iconTemplateStr, 'comments.png'), 'id' => 'comments'));
    $menu_content->addItem($menu_item);
}

$menu_item =& DynMenuItem::Create(getGS('Image Archive'), "/$ADMIN/imagearchive/index.php",
                array('icon' => sprintf($iconTemplateStr, 'image_archive.png'), 'id' => 'image_archive'));
$menu_content->addItem($menu_item);

$menu_item =& DynMenuItem::Create(getGS('Universal List'), "/$ADMIN/universal-list/index.php",
                array('icon' => sprintf($iconTemplateStr, 'logs.png'), 'id' => 'universal_list'));
$menu_content->addItem($menu_item);

$menu_content->addSplit();

$icon_bullet = '<img src="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/tol.gif" style="padding:3px;" width="8" height="8" border="0" />';
foreach ($Campsite["publications"] as $publication) {
    $pubId = $publication->getPublicationId();
    $menu_item_pub =& DynMenuItem::Create($publication->getName(),
                                          "/$ADMIN/issues/index.php?Pub=$pubId",
                                          array("icon" => $icon_bullet));
    $menu_content->addItem($menu_item_pub);
    if (isset($Campsite["issues"][$pubId])) {
        foreach ($Campsite["issues"][$pubId] as $issue) {
            $issueId = $issue->getIssueNumber();
            $languageId = $issue->getLanguageId();
            $issueIndexLink = "/$ADMIN/sections/index.php?Pub=$pubId&Issue=$issueId&Language=$languageId";
            $menu_item_issue =& DynMenuItem::Create($issue->getIssueNumber().". ".$issue->getName()." (".$issue->getLanguageName().")",
                 $issueIndexLink,
                 array("icon" => $icon_bullet));
            $menu_item_pub->addItem($menu_item_issue);
            if (isset($Campsite["sections"][$pubId][$issueId][$languageId])) {
                foreach ($Campsite["sections"][$pubId][$issueId][$languageId] as $section) {
                    $sectionId = $section->getSectionNumber();
                    $menu_item_section =& DynMenuItem::Create(
                        $section->getSectionNumber().". "
                        .$section->getName(),
                        "/$ADMIN/articles/index.php"
                        ."?f_publication_id=$pubId"
                        ."&f_issue_number=$issueId"
                        ."&f_language_id=$languageId"
                        ."&f_section_number=$sectionId",
                        array("icon" => $icon_bullet));
                    $menu_item_issue->addItem($menu_item_section);
                }
                if (count($Campsite["sections"][$pubId][$issueId][$languageId]) > 0) {
                    $menu_item_issue->addSplit();
                    $menu_item =& DynMenuItem::Create(getGS("More..."), $issueIndexLink,
                        array("icon" => $icon_bullet));
                    $menu_item_issue->addItem($menu_item);
                }
            }
        }
        if (count($Campsite["issues"][$pubId]) > 0) {
            $menu_item_pub->addSplit();
            $menu_item =& DynMenuItem::Create(getGS("More..."),
                "/$ADMIN/issues/index.php?Pub=$pubId",
                array("icon" => $icon_bullet));
            $menu_item_pub->addItem($menu_item);
        }
    }
}
$menu_root->addSplit();
$menu_actions =& DynMenuItem::Create(getGS("Actions"), '',
    array("icon" => sprintf($iconTemplateStr, "actions.png"), "id" => "actions"));
$menu_root->addItem($menu_actions);

if ($g_user->hasPermission("AddArticle")) {
    $menu_item =& DynMenuItem::Create(getGS('Add new article'), "/$ADMIN/articles/add_move.php",
        array("icon" => sprintf($iconTemplateStr, "add_article.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManageTempl")) {
    $menu_item =& DynMenuItem::Create(getGS('Upload new template'),
        "/$ADMIN/templates/upload_templ.php?Path=/look/&Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "upload_template.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManagePub")) {
    $menu_item =& DynMenuItem::Create(getGS("Add new publication"),
        "/$ADMIN/pub/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_publication.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManageUsers")) {
    $menu_item =& DynMenuItem::Create(getGS("Add new staff member"),
        "/$ADMIN/users/edit.php?uType=Staff&Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_user.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManageUsers")) {
    $menu_item =& DynMenuItem::Create(getGS("Add new subscriber"),
        "/$ADMIN/users/edit.php?uType=Subscribers&Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_user.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManageUserTypes")) {
    $menu_item =& DynMenuItem::Create(getGS("Add new user type"),
        "/$ADMIN/user_types/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_user_type.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManageArticleTypes")) {
    $menu_item =& DynMenuItem::Create(getGS("Add new article type"),
        "/$ADMIN/article_types/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_article_type.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManageCountries")) {
    $menu_item =& DynMenuItem::Create(getGS("Add new country"),
        "/$ADMIN/country/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_country.png")));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission("ManageLanguages")) {
    $menu_item =& DynMenuItem::Create(getGS("Add new language"),
        "/$ADMIN/languages/add_modify.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_language.png")));
    $menu_actions->addItem($menu_item);
}

$menu_item =& DynMenuItem::Create(getGS("Change your password"),
    "/$ADMIN/users/edit.php?uType=Staff&User=".$g_user->getUserId(),
    array("icon" => sprintf($iconTemplateStr, "change_password.png")));
$menu_actions->addItem($menu_item);

if ($showAdminActions) {
    $menu_actions->addSplit();

    if ($g_user->hasPermission("ManageIssue") && $g_user->hasPermission("AddArticle")) {
        $menu_item =& DynMenuItem::Create(getGS('Import XML'), "/$ADMIN/articles/la_import.php",
                      array("icon" => sprintf($iconTemplateStr, "import_archive.png")));
    $menu_actions->addItem($menu_item);
    }

    if ((CampCache::IsEnabled() || CampTemplateCache::factory()) && $g_user->hasPermission("ClearCache")) {
        $menu_item =& DynMenuItem::Create(getGS("Clear system cache"),
                      "/$ADMIN/home.php?clear_cache=yes",
                      array("icon" => sprintf($iconTemplateStr, "actions.png")));
    $menu_actions->addItem($menu_item);
    }

    if ($g_user->hasPermission("ManageBackup")) {
        $menu_item =& DynMenuItem::Create(getGS("Backup/Restore"),
                      "/$ADMIN/backup.php",
                      array("icon" => sprintf($iconTemplateStr, "actions.png")));
    $menu_actions->addItem($menu_item);
    }
}

if ($showConfigureMenu) {
    $menu_root->addSplit();
    $menu_config =& DynMenuItem::Create(getGS("Configure"), "",
        array("icon" => sprintf($iconTemplateStr, "configure.png"), "id"=>"configure"));
    $menu_root->addItem($menu_config);

    if ($g_user->hasPermission("ChangeSystemPreferences")) {
        $menu_item =& DynMenuItem::Create(getGS("System Preferences"),
            "/$ADMIN/system_pref/",
            array("icon" => sprintf($iconTemplateStr, "preferences.png")));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission("ManageTempl") || $g_user->hasPermission("DeleteTempl")) {
        $menu_item =& DynMenuItem::Create(getGS("Templates"),
            "/$ADMIN/templates/",
            array("icon" => sprintf($iconTemplateStr, "templates.png")));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission("ManageArticleTypes") || $g_user->hasPermission("DeleteArticleTypes")) {
        $menu_item =& DynMenuItem::Create(getGS("Article Types"),
            "/$ADMIN/article_types/",
            array("icon" => sprintf($iconTemplateStr, "article_types.png")));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission("ManageTopics")) {
        $menu_item =& DynMenuItem::Create(getGS("Topics"),
            "/$ADMIN/topics/",
            array("icon" => sprintf($iconTemplateStr, "topics.png")));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission("ManageLanguages") || $g_user->hasPermission("DeleteLanguages")) {
        $menu_item =& DynMenuItem::Create(getGS("Languages"),
            "/$ADMIN/languages/",
            array("icon" => sprintf($iconTemplateStr, "languages.png")));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission("ManageCountries") || $g_user->hasPermission("DeleteCountries")) {
        $menu_item =& DynMenuItem::Create(getGS("Countries"),
            "/$ADMIN/country/",
            array("icon" => sprintf($iconTemplateStr, "countries.png")));
        $menu_config->addItem($menu_item);
    }
    if ($showPublishingEnvironmentMenu) {
        $menu_config->addSplit();
    }
    if ($g_user->hasPermission("ManageLocalizer")) {
        $menu_item =& DynMenuItem::Create(getGS("Localizer"),
            "/$ADMIN/localizer/",
            array("icon" => sprintf($iconTemplateStr, "localizer.png")));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission("ViewLogs")) {
        $menu_item =& DynMenuItem::Create(getGS("Logs"),
            "/$ADMIN/logs/",
            array("icon" => sprintf($iconTemplateStr, "logs.png")));
        $menu_config->addItem($menu_item);
    }
} // if ($showConfigureMenu)

if ($showUserMenu) {
    $menu_root->addSplit();
    $menu_users =& DynMenuItem::Create(getGS("Users"), "",
        array("icon" => sprintf($iconTemplateStr, "users.png"), "id" => "users"));
    $menu_root->addItem($menu_users);
    if ($g_user->hasPermission("ManageUsers") || $g_user->hasPermission("DeleteUsers")) {
        $menu_item =& DynMenuItem::Create(getGS("Staff"),
            "/$ADMIN/users/?uType=Staff&reset_search=true",
            array("icon" => sprintf($iconTemplateStr, "users.png")));
        $menu_users->addItem($menu_item);
    }
    if (($g_user->hasPermission("ManageReaders") || $g_user->hasPermission("ManageSubscriptions"))
            && SystemPref::Get("ExternalSubscriptionManagement") != 'Y') {
        $menu_item =& DynMenuItem::Create(getGS("Subscribers"),
            "/$ADMIN/users/?uType=Subscribers&reset_search=true",
            array("icon" => sprintf($iconTemplateStr, "users.png")));
        $menu_users->addItem($menu_item);
    }
    if ($g_user->hasPermission("ManageUserTypes")) {
        $menu_item =& DynMenuItem::Create(getGS("Staff User Types"),
            "/$ADMIN/user_types/",
            array("icon" => sprintf($iconTemplateStr, "user_types.png")));
        $menu_users->addItem($menu_item);
    }
    if ($g_user->hasPermission("SyncPhorumUsers")) {
        $menu_item =& DynMenuItem::Create(getGS('Synchronize Campsite and Phorum users'), "/$ADMIN/home.php?sync_users=yes",
        array("icon" => sprintf($iconTemplateStr, "sync_users.png")));
        $menu_users->addItem($menu_item);
    }

    if ($g_user->hasPermission("EditAuthors")) {
        $menu_item =& DynMenuItem::Create("Manage Authors",
        "/$ADMIN/users/authors.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array("icon" => sprintf($iconTemplateStr, "add_user_type.png")));
        $menu_users->addItem($menu_item);
    }
} // if ($showUserMenu)

// plugins: extend menu
CampPlugin::createPluginMenu($menu_root, $iconTemplateStr);

$menu_root->addSplit();
$menu_help =& DynMenuItem::Create(getGS("Help"), "",
    array("icon" => sprintf($iconTemplateStr, "help.png"), "id" => "help"));
$menu_root->addItem($menu_help);
$menu_item =& DynMenuItem::Create(getGS("Help"), $Campsite['HELP_URL'],
    array("icon" => sprintf($iconTemplateStr, "help.png"), "target" => "_blank"));
$menu_help->addItem($menu_item);
$menu_item =& DynMenuItem::Create(getGS("About"), $Campsite['ABOUT_URL'],
    array("icon" => sprintf($iconTemplateStr, "about.png"), "target" => "_blank"));
$menu_help->addItem($menu_item);
$menu_item =& DynMenuItem::Create(getGS("Feedback"), 'mailto:campsite-support@lists.sourcefabric.org',
    array('icon' => sprintf($iconTemplateStr, "mail_generic.png")));
$menu_help->addItem($menu_item);

$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Campsite") . $Campsite['VERSION'];
?>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <script type="text/javascript">var website_url = "<?php echo $Campsite['WEBSITE_URL'];?>";</script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/JSCookMenu.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.5.custom.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/adm/jquery-ui-1.8.6.custom.css" />
  <link rel="stylesheet" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/ThemeOffice/theme.css" type="text/css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css" />
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/ThemeOffice/theme.js" type="text/javascript"></script>
  <?php echo $menu_root->createMenu("myMenu"); ?>
  <title><?php p($siteTitle); ?></title>
</head>
<body class="yui-skin-sam">
<table cellpadding="0" cellspacing="0" class="logoTable">
<tbody>
<tr>
  <td>
    <a href="/<?php p($ADMIN) ?>/home.php"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/campsite_logo.png" alt="<?php putGS('Logo'); ?>" /></a>
  </td>
</tr>
</tbody>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="naviHolder">
<tbody>
<tr>
  <td valign="top" align="left" width="70%">
    <table border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
      <td style="padding-left: 13px; padding-top: 0px; padding-bottom: 0px;" valign="top">
        <div id="myMenuID"></div>
        <script type="text/javascript"><!--
          cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
        --></script>
      </td>
    </tr>
    </tbody>
    </table>
  </td>
  <td align="right" valign="bottom" width="30%" style="padding-bottom: 3px;">
    <table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody>
    <tr>
      <td align="right">
        <table cellpadding="0" cellspacing="0" border="0" class="personal">
        <tbody>
        <tr>
          <td><?php putGS("Signed in: $1", "<b>".$g_user->getRealName()."</b>"); ?></td>
          <td><a href="/<?php p($ADMIN); ?>/logout.php"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/logout.png" width="22" height="22" border="0" alt="<?php putGS('Logout'); ?>" /></a></td>
          <td><a href="/<?php p($ADMIN); ?>/logout.php" style="color: black; text-decoration: none;"><?php putGS('Logout'); ?></a></td>
        </tr>
        </tbody>
        </table>
      </td>
    </tr>
    </tbody>
    </table>
  </td>
</tr>
</tbody>
</table>
