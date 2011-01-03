<?php
require_once($GLOBALS['g_campsiteDir'] . '/db_connect.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/DynMenuItem.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/SystemPref.php');

camp_load_translation_strings('home');
global $ADMIN;
global $g_user;

$showPublishingEnvironmentMenu = ($g_user->hasPermission('ManageTempl')
    || $g_user->hasPermission('DeleteTempl')
    || $g_user->hasPermission('ManageArticleTypes')
    || $g_user->hasPermission('DeleteArticleTypes')
    || $g_user->hasPermission('ManageTopics')
    || $g_user->hasPermission('ManageLanguages')
    || $g_user->hasPermission('DeleteLanguages')
    || $g_user->hasPermission('ManageCountries')
    || $g_user->hasPermission('DeleteCountries'));

$showConfigureMenu = ($showPublishingEnvironmentMenu
    || $g_user->hasPermission('ManageLocalizer')
    || $g_user->hasPermission('ViewLogs'));

$showUserMenu = ($g_user->hasPermission('ManageUsers')
    || $g_user->hasPermission('DeleteUsers')
    || $g_user->hasPermission('ManageSubscriptions')
    || $g_user->hasPermission('ManageUserTypes')
    || $g_user->hasPermission('ManageReaders')
    || $g_user->hasPermission('SyncPhorumUsers'));

$showAdminActions = (($g_user->hasPermission('ManageIssue') && $g_user->hasPermission('AddArticle'))
    || (CampCache::IsEnabled() && $g_user->hasPermission('ClearCache')));

// Creates the Content menu
DynMenuItem::SetMenuType('DynMenuItem_JQueryFG');
$menu_content =& DynMenuItem::Create('', '');

$menu_item =& DynMenuItem::Create(getGS('Publications'), "/$ADMIN/pub/index.php",
    array('icon' => '', 'id' => 'publication'));
$menu_content->addItem($menu_item);

if ($g_user->hasPermission('CommentModerate')) {
    $menu_item =& DynMenuItem::Create(getGS('Comments'), "/$ADMIN/comments/index.php",
        array('icon' => '', 'id' => 'comments'));
    $menu_content->addItem($menu_item);
}

$menu_item =& DynMenuItem::Create(getGS('Media Archive'), "/$ADMIN/media-archive/index.php",
    array('icon' => '', 'id' => 'image_archive'));
$menu_content->addItem($menu_item);

$menu_item =& DynMenuItem::Create(getGS('Search'), "/$ADMIN/universal-list/index.php",
    array('icon' => '', 'id' => 'universal_list'));
$menu_content->addItem($menu_item);

foreach ($Campsite["publications"] as $publication) {
    $pubId = $publication->getPublicationId();
    $menu_item_pub =& DynMenuItem::Create($publication->getName(),
        "/$ADMIN/issues/index.php?Pub=$pubId",
        array('icon' => ''));
    $menu_content->addItem($menu_item_pub);
    if (isset($Campsite['issues'][$pubId])) {
        foreach ($Campsite['issues'][$pubId] as $issue) {
            $issueId = $issue->getIssueNumber();
            $languageId = $issue->getLanguageId();
            $issueIndexLink = "/$ADMIN/sections/index.php?Pub=$pubId&Issue=$issueId&Language=$languageId";
            $menu_item_issue =& DynMenuItem::Create($issue->getIssueNumber().'. '.$issue->getName().' ('.$issue->getLanguageName().')',
                 $issueIndexLink,
                 array('icon' => ''));
            $menu_item_pub->addItem($menu_item_issue);
            if (isset($Campsite['sections'][$pubId][$issueId][$languageId])) {
                foreach ($Campsite['sections'][$pubId][$issueId][$languageId] as $section) {
                    $sectionId = $section->getSectionNumber();
                    $menu_item_section =& DynMenuItem::Create(
                        $section->getSectionNumber().'. '
                        .$section->getName(),
                        "/$ADMIN/articles/index.php"
                        ."?f_publication_id=$pubId"
                        ."&f_issue_number=$issueId"
                        ."&f_language_id=$languageId"
                        ."&f_section_number=$sectionId",
                        array('icon' => ''));
                    $menu_item_issue->addItem($menu_item_section);
                }
                if (count($Campsite['sections'][$pubId][$issueId][$languageId]) > 0) {
                    $menu_item =& DynMenuItem::Create(getGS('More...'), $issueIndexLink,
                        array('icon' => ''));
                    $menu_item_issue->addItem($menu_item);
                }
            }
        }
        if (count($Campsite['issues'][$pubId]) > 0) {
            $menu_item =& DynMenuItem::Create(getGS('More...'),
                "/$ADMIN/issues/index.php?Pub=$pubId",
                array('icon' => ''));
            $menu_item_pub->addItem($menu_item);
        }
    }
}

// Creates the Actions menu
$menu_actions =& DynMenuItem::Create(getGS('Actions'), '',
    array('icon' => '', 'id' => 'actions'));

if ($g_user->hasPermission('AddArticle')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new article'), "/$ADMIN/articles/add_move.php",
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManageTempl')) {
    $menu_item =& DynMenuItem::Create(getGS('Upload new template'),
        "/$ADMIN/templates/upload_templ.php?Path=/look/&Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManagePub')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new publication'),
        "/$ADMIN/pub/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManageUsers')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new staff member'),
        "/$ADMIN/users/edit.php?uType=Staff&Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManageUsers')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new subscriber'),
        "/$ADMIN/users/edit.php?uType=Subscribers&Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManageUserTypes')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new user type'),
        "/$ADMIN/user_types/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManageArticleTypes')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new article type'),
        "/$ADMIN/article_types/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManageCountries')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new country'),
        "/$ADMIN/country/add.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

if ($g_user->hasPermission('ManageLanguages')) {
    $menu_item =& DynMenuItem::Create(getGS('Add new language'),
        "/$ADMIN/languages/add_modify.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
    $menu_actions->addItem($menu_item);
}

$menu_item =& DynMenuItem::Create(getGS('Change your password'),
    "/$ADMIN/users/edit.php?uType=Staff&User=".$g_user->getUserId(),
    array('icon' => ''));
$menu_actions->addItem($menu_item);

if ($showAdminActions) {
    if ($g_user->hasPermission('ManageIssue') && $g_user->hasPermission('AddArticle')) {
        $menu_item =& DynMenuItem::Create(getGS('Import XML'),
            "/$ADMIN/articles/la_import.php",
            array("icon" => ''));
        $menu_actions->addItem($menu_item);
    }

    if ((CampCache::IsEnabled() || CampTemplateCache::factory()) && $g_user->hasPermission('ClearCache')) {
        $menu_item =& DynMenuItem::Create(getGS('Clear system cache'),
            "/$ADMIN/home.php?clear_cache=yes",
            array('icon' => ''));
        $menu_actions->addItem($menu_item);
    }

    if ($g_user->hasPermission('ManageBackup')) {
        $menu_item =& DynMenuItem::Create(getGS('Backup/Restore'),
            "/$ADMIN/backup.php",
            array('icon' => ''));
        $menu_actions->addItem($menu_item);
    }
}

// Creates the Configure menu
if ($showConfigureMenu) {
    $menu_config =& DynMenuItem::Create(getGS('Configure'), '',
        array('icon' => '', 'id' => 'configure'));

    if ($g_user->hasPermission('ChangeSystemPreferences')) {
        $menu_item =& DynMenuItem::Create(getGS('System Preferences'),
            "/$ADMIN/system_pref/",
            array('icon' => ''));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission('ManageTempl') || $g_user->hasPermission('DeleteTempl')) {
        $menu_item =& DynMenuItem::Create(getGS('Templates'),
            "/$ADMIN/templates/",
            array('icon' => ''));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission('ManageArticleTypes') || $g_user->hasPermission('DeleteArticleTypes')) {
        $menu_item =& DynMenuItem::Create(getGS('Article Types'),
            "/$ADMIN/article_types/",
            array('icon' => ''));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission('ManageTopics')) {
        $menu_item =& DynMenuItem::Create(getGS('Topics'),
            "/$ADMIN/topics/",
            array('icon' => ''));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission('ManageLanguages') || $g_user->hasPermission('DeleteLanguages')) {
        $menu_item =& DynMenuItem::Create(getGS('Languages'),
            "/$ADMIN/languages/",
            array('icon' => ''));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission('ManageCountries') || $g_user->hasPermission('DeleteCountries')) {
        $menu_item =& DynMenuItem::Create(getGS('Countries'),
            "/$ADMIN/country/",
            array('icon' => ''));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission('ManageLocalizer')) {
        $menu_item =& DynMenuItem::Create(getGS('Localizer'),
            "/$ADMIN/localizer/",
            array("icon" => ''));
        $menu_config->addItem($menu_item);
    }
    if ($g_user->hasPermission('ViewLogs')) {
        $menu_item =& DynMenuItem::Create(getGS('Logs'),
            "/$ADMIN/logs/",
            array('icon' => ''));
        $menu_config->addItem($menu_item);
    }
}

if ($showUserMenu) {
    $menu_users =& DynMenuItem::Create(getGS('Users'), '',
        array('icon' => '', 'id' => 'users'));
    if ($g_user->hasPermission('ManageUsers') || $g_user->hasPermission('DeleteUsers')) {
        $menu_item =& DynMenuItem::Create(getGS('Staff'),
            "/$ADMIN/users/?uType=Staff&reset_search=true",
            array('icon' => ''));
        $menu_users->addItem($menu_item);
    }
    if (($g_user->hasPermission('ManageReaders') || $g_user->hasPermission('ManageSubscriptions'))
            && SystemPref::Get('ExternalSubscriptionManagement') != 'Y') {
        $menu_item =& DynMenuItem::Create(getGS('Subscribers'),
            "/$ADMIN/users/?uType=Subscribers&reset_search=true",
            array('icon' => ''));
        $menu_users->addItem($menu_item);
    }
    if ($g_user->hasPermission('ManageUserTypes')) {
        $menu_item =& DynMenuItem::Create(getGS('Staff User Types'),
            "/$ADMIN/user_types/",
            array('icon' => ''));
        $menu_users->addItem($menu_item);
    }
    if ($g_user->hasPermission('SyncPhorumUsers')) {
        $menu_item =& DynMenuItem::Create(getGS('Synchronize Campsite and Phorum users'), "/$ADMIN/home.php?sync_users=yes",
        array('icon' => ''));
        $menu_users->addItem($menu_item);
    }

    if ($g_user->hasPermission('EditAuthors')) {
        $menu_item =& DynMenuItem::Create('Manage Authors',
        "/$ADMIN/users/authors.php?Back=".urlencode($_SERVER['REQUEST_URI']),
        array('icon' => ''));
        $menu_users->addItem($menu_item);
    }
}

// Creates the Plugins menu
$menu_plugins = CampPlugin::CreatePluginMenu();

// Page title
$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Campsite") . $Campsite['VERSION'];

// locale setting for datepicker
$locale = trim(getGS('en'), ' (*)');
?>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <title><?php p($siteTitle); ?></title>

  <script type="text/javascript"><!--
    var website_url = "<?php echo $Campsite['WEBSITE_URL'];?>";
  //--></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/ColVis.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.6.custom.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/fg.menu.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/i18n/jquery.ui.datepicker-<?php echo $locale; ?>.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.widgets.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/admin.js" type="text/javascript"></script>

  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/jquery-ui-1.8.6.custom.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/fg.menu.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/ColVis.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/widgets.css" />
  <style type="text/css">
	#menuLog {
	    font-size:1.4em;
	    margin:20px;
	}
	.hidden {
	    position:absolute;
	    top:0;
	    left:-9999px;
	    width:1px;
	    height:1px;
	    overflow:hidden;
	}
	.fg-button {
	    margin:0 2px 0 0;
	    padding: .4em 1em;
	    text-decoration:none !important;
	    cursor:pointer;
	    position: relative;
	    text-align: center;
	    zoom: 1;
	}
	a.fg-button {
	    font-size:11px !important;
	    font-weight:bold !important;
	    text-transform: uppercase;
	}
	.fg-button .fg-button-ui-icon {
	    position: absolute;
	    top: 50%;
	    margin-top: -8px;
	    left: 50%;
	    margin-left: -8px;
	}
	a.fg-button { float:left;  }
	button.fg-button {
	    width:auto;
	    overflow:visible;
	} /* removes extra button width in IE */
	
	.fg-button-icon-left { padding-left: 2.1em; }
	.fg-button-icon-right { padding-right: 2.1em; }
	.fg-button-icon-left .fg-button-ui-icon { right: auto; left: .2em; margin-left: 0; }
	.fg-button-icon-right .fg-button-ui-icon { left: auto; right: .2em; margin-left: 0; }
	.fg-button-icon-solo { display:block; width:8px; text-indent: -9999px; }	 /* solo icon buttons must have block properties for the text-indent to work */	
	
	.fg-button.ui-state-loading .fg-button-ui-icon { background: url(spinner_bar.gif) no-repeat 0 0; }
	</style>
	
	<!-- style exceptions for IE 6 -->
	<!--[if IE 6]>
	<style type="text/css">
		.fg-menu-ipod .fg-menu li { width: 95%; }
		.fg-menu-ipod .ui-widget-content { border:0; }
	</style>
	<![endif]-->	

  <script type="text/javascript">
  <!--
    var g_admin_url = '/<?php echo $ADMIN; ?>';
    var g_security_token = '<?php echo SecurityToken::GetToken(); ?>';
    <?php if (strpos($_SERVER['HTTP_REFERER'], 'login.php') !== FALSE) { ?>
    if (opener && !opener.closed && opener.setSecurityToken) {
        opener.setSecurityToken(g_security_token);
        opener.focus();
        window.close();
    }
    <?php } ?>
    var g_admin_img = '<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>';
    var popupFlash = false;
    $(document).ready(function() {
        $.datepicker.setDefaults( $.datepicker.regional['<?php echo $locale; ?>'] );
    });
    //-->
  </script>
  <script type="text/javascript">
  <!--
  $(function(){
      // BUTTONS
      $('.fg-button').hover(
          function(){ $(this).removeClass('fg-button-ui-state-default').addClass('fg-button-ui-state-focus'); },
          function(){ $(this).removeClass('fg-button-ui-state-focus').addClass('fg-button-ui-state-default'); }
      );

      // MENUS
      $('#newscoop_menu_content').menu({
          content: $('#newscoop_menu_content').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php if ($showAdminActions) { ?>
      $('#newscoop_menu_action').menu({
          content: $('#newscoop_menu_action').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php
      }
      if ($showConfigureMenu) {
      ?>
      $('#newscoop_menu_configure').menu({
          content: $('#newscoop_menu_configure').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php
      }
      if ($showUserMenu) {
      ?>
      $('#newscoop_menu_users').menu({
          content: $('#newscoop_menu_users').next().html(),
          flyOut: true,
          showSpeed: 150
      });
      <?php } ?>
      $('#newscoop_menu_plugins').menu({
          content: $('#newscoop_menu_plugins').next().html(),
          flyOut: true,
          showSpeed: 150
      });
  });
  //-->
  </script>
  <script type="text/javascript"> 
  $(document).ready(function() {
      $(window).scroll(function() {
          if ($(window).scrollTop() > $(".smartLegendIdentifier").offset({ scroll: false }).top) {
              $(".sticky").css("position", "fixed");
              $(".sticky").css("top", "0");
          }
          if ($(window).scrollTop() <= $(".smartLegendIdentifier").offset({ scroll: false }).top) {
              $(".sticky").css("position", "relative");
              $(".sticky").css("top", $(".smartLegendIdentifier").offset);
          }
      });
  });
  </script>
</head>
<body>
<div class="meta-bar">
<ul>
  <li><a href="/<?php p($ADMIN); ?>/logout.php"><?php putGS('Logout'); ?></a></li>
  <li><a href="#"><?php putGS('Help'); ?></a></li>
  <li><?php putGS("Signed in: $1", '<strong>' . $g_user->getRealName() . '</strong>'); ?></li>
</ul>
</div>

<!--MAIN MENU-->
<div class="main-menu-bar">
  <a tabindex="0" href="#"
      class="fg-button ui-widget fg-button-ui-state-default fg-button-ui-corner-all" id="newscoop_menu_dashboard"><?php putGS('Dashboard'); ?></a>
  <a tabindex="1" href="#my-menu"
      class="fg-button fg-button-icon-right ui-widget fg-button-ui-state-default fg-button-ui-corner-all" id="newscoop_menu_content"><span class="fg-button-ui-icon fg-button-ui-icon-triangle-1-s"></span><?php putGS('Content'); ?></a>
  <div id="my-menu" class="hidden">
    <?php echo $menu_content->createMenu('menuContent'); ?>
  </div>
<?php
if ($showAdminActions) {
?>        
  <a tabindex="2" href="#actions-submenu"
      class="fg-button fg-button-icon-right ui-widget fg-button-ui-state-default fg-button-ui-corner-all" id="newscoop_menu_action"><span class="fg-button-ui-icon fg-button-ui-icon-triangle-1-s"></span><?php putGS('Actions'); ?></a>
  <div id="actions-submenu" class="hidden">
    <?php echo $menu_actions->createMenu('menuActions'); ?>
  </div>
<?php
}
if ($showConfigureMenu) {
?>
  <a tabindex="2" href="#configure-submenu" class="fg-button fg-button-icon-right ui-widget fg-button-ui-state-default fg-button-ui-corner-all" id="newscoop_menu_configure"><span class="fg-button-ui-icon fg-button-ui-icon-triangle-1-s"></span><?php putGS('Configure'); ?></a>
  <div id="configure-submenu" class="hidden">
    <?php echo $menu_config->createMenu('menuConfigure'); ?>
  </div>
<?php
}
if ($showUserMenu) {
?>
  <a tabindex="2" href="#users-submenu"
      class="fg-button fg-button-icon-right ui-widget fg-button-ui-state-default fg-button-ui-corner-all" id="newscoop_menu_users"><span class="fg-button-ui-icon fg-button-ui-icon-triangle-1-s"></span><?php putGS('Users'); ?></a>
  <div id="users-submenu" class="hidden">
    <?php echo $menu_users->createMenu('menuUsers'); ?>
  </div>
<?php
}
if (is_object($menu_plugins)) {
?>
  <a tabindex="2" href="#plugins-submenu" class="fg-button fg-button-icon-right ui-widget fg-button-ui-state-default fg-button-ui-corner-all" id="newscoop_menu_plugins"><span class="fg-button-ui-icon fg-button-ui-icon-triangle-1-s"></span><?php putGS('Plugins'); ?></a>
  <div id="plugins-submenu" class="hidden">
    <?php echo $menu_plugins->createMenu('menuPlugins');  ?>
  </div>
<?php } ?>
</div>
<!--END MAIN MENU-->
