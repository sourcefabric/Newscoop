<?php
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
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
	|| $User->hasPermission("ManageUserTypes")
	|| $User->hasPermission("ManageReaders"));
	
$showObsoleteMenu = ($User->hasPermission("ManageDictionary") 
	|| $User->hasPermission("DeleteDictionary") 
	|| $User->hasPermission("ManageClasses"));

$publications =& Publication::GetAllPublications();
$issues = array();
$sections = array();
foreach ($publications as $publication) {
	$issues[$publication->getPublicationId()] =& 
		Issue::GetIssues($publication->getPublicationId(), null, null, null, 
			array('ORDER BY'=>array('Number'=>'DESC'), 'LIMIT' => '5'));
	foreach ($issues[$publication->getPublicationId()] as $issue) {
		$sections[$issue->getPublicationId()][$issue->getIssueId()][$issue->getLanguageId()] = 
			Section::GetSections($issue->getPublicationId(), 
				$issue->getIssueId(), $issue->getLanguageId());
	}
}
?>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<script language="JavaScript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/JSCookMenu.js" type="text/javascript"></script>
	<LINK REL="stylesheet" HREF="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/ThemeOffice/theme.css" TYPE="text/css">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
<script language="JavaScript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/JSCookMenu/ThemeOffice/theme.js" type="text/javascript"></script>
	<SCRIPT LANGUAGE="JavaScript"><!--
	var myMenu =
	[
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/home.png" align="middle" style="padding-bottom: 3px;" width="22" height="22" />', ' <?php putGS('Home'); ?>', '/<?php p($ADMIN); ?>/home.php' ],
	    _cmSplit,	    
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/content.png" align="middle" style="padding-bottom: 3px;" width="22" height="22" />', ' <?php putGS('Content'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/publication.png"  width="22" height="22"/>', '<?php putGS('Publications'); ?>', '/<?php p($ADMIN); ?>/pub/index.php' ],
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/image_archive.png"  width="22" height="22"/>', '<?php putGS('Image Archive'); ?>', '/<?php p($ADMIN); ?>/imagearchive/index.php' ],
		    _cmSplit,
	    	<?php
	    	foreach ($publications as $publication) {
	    		$pubId = $publication->getPublicationId();
	    		?>
		    	['<img src="/<?php p($ADMIN); ?>/img/tol.gif"/>', '<?php p(htmlspecialchars($publication->getName())); ?>', '/<?php p($ADMIN); ?>/pub/issues/index.php?Pub=<?php p($pubId); ?>', '', '' <?php
	    		if (isset($issues[$pubId])) {
	    			echo ",\n";
	    			foreach ($issues[$pubId] as $issue) {
	    				$issueId = $issue->getIssueId();
	    				$languageId = $issue->getLanguageId();
		    			?>['<img src="/<?php p($ADMIN); ?>/img/tol.gif"/>', '<?php p(htmlspecialchars($issue->getName().' ('.$issue->getLanguageName().')')); ?>', '/<?php p($ADMIN); ?>/pub/issues/sections/index.php?Pub=<?php p($pubId); ?>&Issue=<?php p($issueId); ?>&Language=<?php p($languageId); ?>', '', ''
		    			<?php
		    			if (isset($sections[$pubId][$issueId][$languageId])) {
			    			echo ",\n";
		    				foreach ($sections[$pubId][$issueId][$languageId] as $section) {
		    					?>['<img src="/<?php p($ADMIN); ?>/img/tol.gif"/>', '<?php p(htmlspecialchars($section->getName())); ?>', '/<?php p($ADMIN); ?>/pub/issues/sections/articles/index.php?Pub=<?php p($pubId); ?>&Issue=<?php p($issueId); ?>&Language=<?php p($languageId); ?>&Section=<?php p($section->getSectionId()); ?>' ], 					
		    					<?php
		    				} // foreach ($sections ... )
		    			}
	    				echo "],\n";		    			
		    		} // foreach ($issues ... )
		    		?>
		    		_cmSplit,
		    		['<img src="/<?php p($ADMIN); ?>/img/tol.gif"/>', '<?php putGS('More...'); ?>', '/<?php p($ADMIN); ?>/pub/issues/index.php?Pub=<?php p($pubId); ?>' ],
		    		<?php
	    		}
	    		echo "],\n";
	    	} // foreach ($publications ... )
	    	?>
	    ],
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/actions.png" align="middle"  width="22" height="22"/>', ' <?php putGS('Actions'); ?>', '', '', '',
	    	
	    	<?php if ($User->hasPermission("AddArticle")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_article.png"  width="22" height="22"/>', '<?php putGS('Add new article'); ?>', '/<?php p($ADMIN); ?>/pub/add_article.php'],
	    	<?php } ?>
			
			<?php  if ($User->hasPermission("ManageTempl")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/upload_template.png"  width="22" height="22"/>', '<?php putGS("Upload new template"); ?>', '/<?php p($ADMIN); ?>/templates/upload_templ.php?Path=/look/&Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>'],
			<?php  } ?>
				
	    	<?php  if ($User->hasPermission("ManagePub")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_publication.png"  width="22" height="22"/>', '<?php putGS("Add new publication"); ?>', '/<?php p($ADMIN); ?>/pub/add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>'],
			<?php  } ?>

			<?php  if ($User->hasPermission("ManageUsers")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_user.png"  width="22" height="22"/>', '<?php putGS("Add new user account"); ?>', '/<?php p($ADMIN); ?>/users/add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>'],
			<?php  } ?>
			
			<?php  if ($User->hasPermission("ManageUserTypes")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_user_type.png"  width="22" height="22"/>', '<?php putGS("Add new user type"); ?>', '/<?php p($ADMIN); ?>/u_types/add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>'],
			<?php  } ?>

			<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_article_type.png"  width="22" height="22"/>', '<?php putGS("Add new article type"); ?>', '/<?php p($ADMIN); ?>/a_types/add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>'],
			<?php  } ?>

			<?php  if ($User->hasPermission("ManageCountries")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_country.png"  width="22" height="22"/>', '<?php putGS("Add new country"); ?>', '/<?php p($ADMIN); ?>/country/add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>'],
			<?php  } ?>
		
			<?php  if ($User->hasPermission("ManageLanguages")) { ?>	
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_language.png"  width="22" height="22"/>', '<?php putGS("Add new language"); ?>', '/<?php p($ADMIN); ?>/languages/add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>'],
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
	    	<?php if ($User->hasPermission("ManageUsers") || $User->hasPermission("DeleteUsers")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/users.png" width="22" height="22" />', '<?php putGS('Staff'); ?>', '/<?php p($ADMIN); ?>/users/?uType=Staff' ],
	    	<?php } ?>
	    	<?php if ($User->hasPermission("ManageReaders") || $User->hasPermission("ManageSubscriptions")) { ?>
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/users.png" width="22" height="22" />', '<?php putGS('Readers'); ?>', '/<?php p($ADMIN); ?>/users/?uType=Readers' ],
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

	<TITLE>Campsite <?php p($Campsite['VERSION']); ?></TITLE>
</HEAD>

<BODY>
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 2px solid #D5E2EE;"> 
<tr >
	<td valign="top" align="left" width="70%" style="padding-top: 0px; ">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="padding-left: 3px; padding-right: 10px; padding-top: 0px; padding-bottom: 2px; font-size: 14pt; font-weight: bold; color: black; font-style: Verdana;" align="left" valign="middle" nowrap>
				<a href="/<?php p($ADMIN) ?>/home.php"><IMG SRC="/admin/img/sign_big.gif" BORDER="0" align="middle"></a>
			</td>
			<td style="padding-left: 20px; padding-top: 6px;" valign="top">
			<DIV ID="myMenuID"></DIV>
			<SCRIPT LANGUAGE="JavaScript"><!--
				cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
			--></SCRIPT>
			</td>
		</tr>
		</table>
	</td>
	<td align="right" valign="middle" width="30%">
		<table cellpadding="0" cellspacing="0" width="100%" style="">
		<tr>
			<td align="right" style="padding-top: 2px;">
				<table cellpadding="0" cellspacing="0">
				<TR>
            		<td align="right" style="font-size: 8pt; padding-right: 5px; padding-top: 2px;" colspan="4">
            			<?php putGS("Signed in: $1", "<b>".$User->getName()."</b>"); ?>
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
	</td>
</tr>
</table>