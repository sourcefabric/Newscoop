<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR");
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
	|| $User->hasPermission("ManageUserTypes"));
	
$showObsoleteMenu = ($User->hasPermission("ManageDictionary") 
	|| $User->hasPermission("DeleteDictionary") 
	|| $User->hasPermission("ManageClasses"));
	
?>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<script language="JavaScript" src="<?php echo $Campsite["website_url"]; ?>/javascript/JSCookMenu/JSCookMenu.js" type="text/javascript"></script>
	<LINK REL="stylesheet" HREF="<?php echo $Campsite["website_url"]; ?>/javascript/JSCookMenu/ThemeOffice/theme.css" TYPE="text/css">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"]; ?>/css/admin_stylesheet.css">
<script language="JavaScript" src="<?php echo $Campsite["website_url"]; ?>/javascript/JSCookMenu/ThemeOffice/theme.js" type="text/javascript"></script>
	<SCRIPT LANGUAGE="JavaScript"><!--
	var myMenu =
	[
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/content.gif" align="middle" style="padding-bottom: 3px;" />', ' <?php putGS('Content'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/publication.gif" />', '<?php putGS('Publications'); ?>', '/<?php p($ADMIN); ?>/pub/index.php' ],
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/image_archive.gif" />', '<?php putGS('Image archive'); ?>', '/<?php p($ADMIN); ?>/imagearchive/index.php' ]
	    ],
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/actions.gif" align="middle" />', ' <?php putGS('Actions'); ?>', '', '', '',
	    	<?php if ($User->hasPermission("AddArticle")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_article.gif" />', '<?php putGS('Add new article'); ?>', '/<?php p($ADMIN); ?>/pub/add_article.php'],
	    	<?php } ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/change_password.gif" />', '<?php putGS('Change your password'); ?>', '/<?php p($ADMIN); ?>/users/chpwd.php']
	    ],
	    <?php if ($showConfigureMenu) { ?>
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/configure.gif" align="middle" />', ' <?php putGS('Configure'); ?>', '', '', '',
	    	<?php if ($showPublishingEnvironmentMenu) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/publishing_environment.gif" />', '<?php putGS('Publishing environment'); ?>', '', '', '',
	    		<?php if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/templates.gif" />', '<?php putGS('Templates');?>', '/<?php p($ADMIN); ?>/templates/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageArticleTypes") || $User->hasPermission("DeleteArticleTypes")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/article_types.gif" />', '<?php putGS('Article Types'); ?>', '/<?php p($ADMIN); ?>/a_types/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageTopics")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/topics.gif" />', '<?php putGS('Topics'); ?>', '/<?php p($ADMIN); ?>/topics/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageLanguages") || $User->hasPermission("DeleteLanguages")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/languages.gif" />', '<?php putGS('Languages'); ?>', '/<?php p($ADMIN); ?>/languages/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageCountries") || $User->hasPermission("DeleteCountries")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/countries.gif" />', '<?php putGS('Countries'); ?>', '/<?php p($ADMIN); ?>/country/' ],
	    		<?php } ?>
	    	],
	    	<?php } // if ($showPublishingEnvironmentMenu) ?>
	    	<?php if ($User->hasPermission("ManageLocalizer")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/localizer.gif" />', '<?php putGS('Localizer'); ?>', '/<?php p($ADMIN); ?>/localizer/',  ],
	    	<?php } ?>
	    	<?php if ($User->hasPermission("ViewLogs")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/logs.gif" />', '<?php putGS('Logs'); ?>', 'logs/', ]
	    	<?php } ?>
	    ],
	    <?php } // if ($showConfigureMenu) ?>
	    <?php if ($showUserMenu) { ?>
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/users.gif" align="middle" />', ' <?php putGS('Users'); ?>', '', '', '',
	    	<?php if ($User->hasPermission("ManageUsers") || $User->hasPermission("DeleteUsers") || $User->hasPermission("ManageSubscriptions")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/users.gif" />', '<?php putGS('Users'); ?>', '/<?php p($ADMIN); ?>/users/' ], 
	    	<?php } ?>
	    	<?php if ($User->hasPermission("ManageUserTypes")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/user_types.gif" />', '<?php putGS('User Types'); ?>', '/<?php p($ADMIN); ?>/u_types/' ], 
	    	<?php } ?>
	    ],
	    <?php } // if ($showUserMenu) ?>
	    <?php if ($showObsoleteMenu)  { ?>
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/obsolete.gif" align="middle" />', ' <?php putGS('Obsolete'); ?>', '', '', '',
	    	<?php if ($User->hasPermission("ManageDictionary") || $User->hasPermission("DeleteDictionary")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/glossary.gif" />', '<?php putGS('Glossary'); ?>', '/<?php p($ADMIN); ?>/glossary/' ], 
	    	<?php } ?>
	    	<?php if ($User->hasPermission("ManageClasses")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/infotypes.gif" />', '<?php putGS('Infotype'); ?>', '/<?php p($ADMIN); ?>/infotype/' ], 
	    	<?php } ?>
	    ]
	    <?php } // if ($showObsoleteMenu) ?>
	];
	--></SCRIPT>

	<TITLE>Campsite <?php p($Campsite['version']); ?></TITLE>
</HEAD>

<BODY BGCOLOR="white" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE" style="margin: 0px;" margintop="0" marginright="0" marginleft="0">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid black;">
<tr>
	<td valign="top" align="left" width="100%" style="padding-top: 0px; ">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="padding-left: 10px; padding-right: 20px; padding-top: 2px; padding-bottom: 2px; font-size: 14pt; font-weight: bold; color: black; font-style: Verdana;" align="left" valign="middle">
				<IMG SRC="/admin/img/sign_big.gif" BORDER="0" align="middle">
				Campsite v<?php p($Campsite['version']); ?>
			</td>
			<td style="padding-left: 5px;">
			<DIV ID="myMenuID"></DIV>
			<SCRIPT LANGUAGE="JavaScript"><!--
				cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
			--></SCRIPT>
			</td>
		</tr>
		</table>
	</td>
	<td align="right">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<A HREF="/<?php p($ADMIN); ?>/home.php"><img src="/<?php p($ADMIN); ?>/img/icon/home.gif" border="0" alt="<?php putGS('Home'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px;">
				<A HREF="/<?php p($ADMIN); ?>/home.php" style="color: black; text-decoration: none;"><?php putGS('Home'); ?></A>
			</td>
			
			<td style="padding-left: 10px;">
				<A HREF="" ONCLICK="window.open('/<?php p($ADMIN); ?>/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;"><img src="/<?php p($ADMIN); ?>/img/icon/quick_menu.gif" border="0" alt="<?php putGS('Quick Menu'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px;" nowrap>
				<A HREF="" ONCLICK="window.open('/<?php p($ADMIN); ?>/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" style="color: black; text-decoration: none;"><?php putGS('Quick Menu'); ?></a>
			</td>
			
			<td style="padding-left: 10px;">
				<A HREF="/<?php p($ADMIN); ?>/logout.php"><img src="/<?php p($ADMIN); ?>/img/icon/logout.gif" border="0" alt="<?php putGS('Logout'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px; padding-right: 10px;">
				<A HREF="/<?php p($ADMIN); ?>/logout.php" style="color: black; text-decoration: none;"><?php putGS('Logout'); ?></a>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>