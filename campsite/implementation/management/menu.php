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
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/content.png" align="middle" style="padding-bottom: 3px;" width="22" height="22" />', ' <?php putGS('Content'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/publication.png"  width="22" height="22"/>', '<?php putGS('Publications'); ?>', '/<?php p($ADMIN); ?>/pub/index.php' ],
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/image_archive.png"  width="22" height="22"/>', '<?php putGS('Image archive'); ?>', '/<?php p($ADMIN); ?>/imagearchive/index.php' ]
	    ],
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/actions.png" align="middle"  width="22" height="22"/>', ' <?php putGS('Actions'); ?>', '', '', '',
	    	
	    	<?php if ($User->hasPermission("AddArticle")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_article.png"  width="22" height="22"/>', '<?php putGS('Add new article'); ?>', '/<?php p($ADMIN); ?>/pub/add_article.php'],
	    	<?php } ?>
			
			<?php  if ($User->hasPermission("ManageTempl")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/upload_template.png"  width="22" height="22"/>', '<?php putGS("Upload new template"); ?>', '/<?php p($ADMIN); ?>/templates/upload_templ.php?Path=/look/&Back=<?php  print encURL ($REQUEST_URI); ?>'],
			<?php  } ?>
				
	    	<?php  if ($User->hasPermission("ManagePub")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_publication.png"  width="22" height="22"/>', '<?php putGS("Add new publication"); ?>', '/<?php p($ADMIN); ?>/pub/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>'],
			<?php  } ?>

			<?php  if ($User->hasPermission("ManageUsers")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_user.png"  width="22" height="22"/>', '<?php putGS("Add new user account"); ?>', '/<?php p($ADMIN); ?>/users/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>'],
			<?php  } ?>
			
			<?php  if ($User->hasPermission("ManageUserTypes")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_user_type.png"  width="22" height="22"/>', '<?php putGS("Add new user type"); ?>', '/<?php p($ADMIN); ?>/u_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>'],
			<?php  } ?>

			<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_article_type.png"  width="22" height="22"/>', '<?php putGS("Add new article type"); ?>', '/<?php p($ADMIN); ?>/a_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>'],
			<?php  } ?>

			<?php  if ($User->hasPermission("ManageCountries")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_country.png"  width="22" height="22"/>', '<?php putGS("Add new country"); ?>', '/<?php p($ADMIN); ?>/country/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>'],
			<?php  } ?>
		
			<?php  if ($User->hasPermission("ManageLanguages")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_language.png"  width="22" height="22"/>', '<?php putGS("Add new language"); ?>', '/<?php p($ADMIN); ?>/languages/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>'],
			<?php  } ?>
			
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/change_password.png"  width="22" height="22"/>', '<?php putGS('Change your password'); ?>', '/<?php p($ADMIN); ?>/users/chpwd.php']
	    ],
	    <?php if ($showConfigureMenu) { ?>
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/configure.png" align="middle"  width="22" height="22"/>', ' <?php putGS('Configure'); ?>', '', '', '',
	    	<?php if ($showPublishingEnvironmentMenu) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/publishing_environment.png"  width="22" height="22"/>', '<?php putGS('Publishing environment'); ?>', '', '', '',
	    		<?php if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/templates.png" width="22" height="22" />', '<?php putGS('Templates');?>', '/<?php p($ADMIN); ?>/templates/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageArticleTypes") || $User->hasPermission("DeleteArticleTypes")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/article_types.png" width="22" height="22" />', '<?php putGS('Article Types'); ?>', '/<?php p($ADMIN); ?>/a_types/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageTopics")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/topics.png" width="22" height="22" />', '<?php putGS('Topics'); ?>', '/<?php p($ADMIN); ?>/topics/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageLanguages") || $User->hasPermission("DeleteLanguages")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/languages.png" width="22" height="22" />', '<?php putGS('Languages'); ?>', '/<?php p($ADMIN); ?>/languages/' ],
	    		<?php } ?>
	    		<?php if ($User->hasPermission("ManageCountries") || $User->hasPermission("DeleteCountries")) { ?>
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/countries.png" width="22" height="22" />', '<?php putGS('Countries'); ?>', '/<?php p($ADMIN); ?>/country/' ],
	    		<?php } ?>
	    	],
	    	<?php } // if ($showPublishingEnvironmentMenu) ?>
	    	<?php if ($User->hasPermission("ManageLocalizer")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/localizer.png" width="22" height="22" />', '<?php putGS('Localizer'); ?>', '/<?php p($ADMIN); ?>/localizer/',  ],
	    	<?php } ?>
	    	<?php if ($User->hasPermission("ViewLogs")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/logs.png" />', '<?php putGS('Logs'); ?>', '/<?php p($ADMIN); ?>/logs/', ]
	    	<?php } ?>
	    ],
	    <?php } // if ($showConfigureMenu) ?>
	    <?php if ($showUserMenu) { ?>
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/users.png" width="22" height="22" align="middle" />', ' <?php putGS('Users'); ?>', '', '', '',
	    	<?php if ($User->hasPermission("ManageUsers") || $User->hasPermission("DeleteUsers") || $User->hasPermission("ManageSubscriptions")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/users.png" width="22" height="22" />', '<?php putGS('Users'); ?>', '/<?php p($ADMIN); ?>/users/' ], 
	    	<?php } ?>
	    	<?php if ($User->hasPermission("ManageUserTypes")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/user_types.png" width="22" height="22" />', '<?php putGS('User Types'); ?>', '/<?php p($ADMIN); ?>/u_types/' ], 
	    	<?php } ?>
	    ],
	    <?php } // if ($showUserMenu) ?>
	    <?php if ($showObsoleteMenu)  { ?>
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/obsolete.png" width="22" height="22" align="middle" />', ' <?php putGS('Obsolete'); ?>', '', '', '',
	    	<?php if ($User->hasPermission("ManageDictionary") || $User->hasPermission("DeleteDictionary")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/glossary.png" width="22" height="22" />', '<?php putGS('Glossary'); ?>', '/<?php p($ADMIN); ?>/glossary/' ], 
	    	<?php } ?>
	    	<?php if ($User->hasPermission("ManageClasses")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/infotypes.png" width="22" height="22" />', '<?php putGS('Infotype'); ?>', '/<?php p($ADMIN); ?>/infotype/' ], 
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
			<td style="padding-left: 5px; padding-right: 10px; padding-top: 2px; padding-bottom: 2px; font-size: 14pt; font-weight: bold; color: black; font-style: Verdana;" align="left" valign="middle">
				<IMG SRC="/admin/img/sign_big.gif" BORDER="0" align="middle">
				Campsite v<?php p($Campsite['version']); ?>
			</td>
			<td style="padding-left: 2px;">
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
				<A HREF="/<?php p($ADMIN); ?>/home.php"><img src="/<?php p($ADMIN); ?>/img/icon/home.png" width="22" height="22" border="0" alt="<?php putGS('Home'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px;">
				<A HREF="/<?php p($ADMIN); ?>/home.php" style="color: black; text-decoration: none;"><?php putGS('Home'); ?></A>
			</td>
			
			<td style="padding-left: 10px;">
				<A HREF="" ONCLICK="window.open('/<?php p($ADMIN); ?>/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;"><img src="/<?php p($ADMIN); ?>/img/icon/quick_menu.png" width="22" height="22" border="0" alt="<?php putGS('Quick Menu'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px;" nowrap>
				<A HREF="" ONCLICK="window.open('/<?php p($ADMIN); ?>/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" style="color: black; text-decoration: none;"><?php putGS('Quick Menu'); ?></a>
			</td>
			
			<td style="padding-left: 10px;">
				<A HREF="/<?php p($ADMIN); ?>/logout.php"><img src="/<?php p($ADMIN); ?>/img/icon/logout.png" width="22" height="22" border="0" alt="<?php putGS('Logout'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px; padding-right: 10px;">
				<A HREF="/<?php p($ADMIN); ?>/logout.php" style="color: black; text-decoration: none;"><?php putGS('Logout'); ?></a>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>