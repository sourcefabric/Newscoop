<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR");
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	exit;
}
$menu_index = 1;
$max_menu_items = 5;
$counter_reset = false;
$counter_resets = 0;
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
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/content.png" align="middle" style="padding-bottom: 3px;" />', ' <?php putGS('Content'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/publication.png" />', '<?php putGS('Publications'); ?>', '/<?php p($ADMIN); ?>/pub/index.php' ],
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/image_archive.png" />', '<?php putGS('Image archive'); ?>', '/<?php p($ADMIN); ?>/imagearchive/index.php' ]
	    ],
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/actions.png" align="middle" />', ' <?php putGS('Actions'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/add_article.png" />', '<?php putGS('Add new article'); ?>', '/<?php p($ADMIN); ?>/pub/add_article.php'],
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/change_password.png" />', '<?php putGS('Change your password'); ?>', '/<?php p($ADMIN); ?>/users/chpwd.php']
	    ],
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/configure.png" align="middle" />', ' <?php putGS('Configure'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/publishing_environment.png" />', '<?php putGS('Publishing environment'); ?>', '', '', '',
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/templates.png" />', '<?php putGS('Templates');?>', '/<?php p($ADMIN); ?>/templates/' ],
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/article_types.png" />', '<?php putGS('Article Types'); ?>', '/<?php p($ADMIN); ?>/a_types/' ],
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/topics.png" />', '<?php putGS('Topics'); ?>', '/<?php p($ADMIN); ?>/topics/' ],
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/languages.png" />', '<?php putGS('Languages'); ?>', '/<?php p($ADMIN); ?>/languages/' ],
	    		['<img src="/<?php p($ADMIN); ?>/img/icon/countries.png" />', '<?php putGS('Countries'); ?>', '/<?php p($ADMIN); ?>/country/' ],
	    	],
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/localizer.png" />', '<?php putGS('Localizer'); ?>', '/<?php p($ADMIN); ?>/localizer/',  ],
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/logs.png" />', '<?php putGS('Logs'); ?>', 'logs/', ]
	    ],
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/users.png" align="middle" />', ' <?php putGS('Users'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/users.png" />', '<?php putGS('Users'); ?>', '/<?php p($ADMIN); ?>/users/' ], 
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/user_types.png" />', '<?php putGS('User Types'); ?>', '/<?php p($ADMIN); ?>/u_types/' ], 
	    ],
	    _cmSplit,
	    ['<img src="/<?php p($ADMIN); ?>/img/icon/obsolete.png" align="middle" />', ' <?php putGS('Obsolete'); ?>', '', '', '',
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/glossary.png" />', '<?php putGS('Glossary'); ?>', '/<?php p($ADMIN); ?>/glossary/' ], 
	    	['<img src="/<?php p($ADMIN); ?>/img/icon/infotypes.png" />', '<?php putGS('Infotype'); ?>', '/<?php p($ADMIN); ?>/infotype/' ], 
	    ]	    	
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
			<td style="padding-left: 10px; padding-right: 20px; font-size: 14pt; font-weight: bold; color: black; font-style: Verdana;" align="left" valign="middle">
				<IMG SRC="/admin/img/sign_big.gif" BORDER="0">
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
				<A HREF="/<?php p($ADMIN); ?>/home.php"><img src="/<?php p($ADMIN); ?>/img/icon/home.png" border="0" alt="<?php putGS('Home'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px;">
				<A HREF="/<?php p($ADMIN); ?>/home.php" style="color: black; text-decoration: none;"><?php putGS('Home'); ?></A>
			</td>
			
			<td style="padding-left: 10px;">
				<A HREF="" ONCLICK="window.open('/<?php p($ADMIN); ?>/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;"><img src="/<?php p($ADMIN); ?>/img/icon/quick_menu.png" border="0" alt="<?php putGS('Quick Menu'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px;" nowrap>
				<A HREF="" ONCLICK="window.open('/<?php p($ADMIN); ?>/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" style="color: black; text-decoration: none;"><?php putGS('Quick Menu'); ?></a>
			</td>
			
			<td style="padding-left: 10px;">
				<A HREF="/<?php p($ADMIN); ?>/logout.php"><img src="/<?php p($ADMIN); ?>/img/icon/logout.png" border="0" alt="<?php putGS('Logout'); ?>"></a>
			</td>
			<td style="font-weight: bold; padding-left: 2px; padding-right: 10px;">
				<A HREF="/<?php p($ADMIN); ?>/logout.php" style="color: black; text-decoration: none;"><?php putGS('Logout'); ?></a>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>