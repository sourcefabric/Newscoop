B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Menu*>)
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*mpa*>, <*ManagePub*>)
SET_ACCESS(<*muta*>, <*ManageUserTypes*>)
SET_ACCESS(<*mda*>, <*ManageDictionary*>)
SET_ACCESS(<*mca*>, <*ManageClasses*>)
SET_ACCESS(<*mcoa*>, <*ManageCountries*>)
SET_ACCESS(<*mata*>, <*ManageArticleTypes*>)
SET_ACCESS(<*mua*>, <*ManageUsers*>)
SET_ACCESS(<*mla*>, <*ManageLanguages*>)
SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*vla*>, <*ViewLogs*>)
SET_ACCESS(<*mlza*>, <*ManageLocalizer*>)
SET_ACCESS(<*mia*>, <*ManageIndexer*>)
SET_ACCESS(<*mcta*>, <*ManageTopics*>)

?>dnl

B_STYLE
E_STYLE

B_MBODY

B_MENU
    X_MENU_ITEM(<*Home*>, <*home.php*>)
    X_MENU_ITEM(<*Quick Menu*>, <**>, <*window.open('X_ROOT/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;*>)
    X_MENU_BAR
    X_MENU_ITEM(<*Publications*>, <*pub/*>)
<?php  if ($mta) { ?>dnl
    X_MENU_ITEM(<*Templates*>, <*/look/*>)
<?php  } ?>dnl
<?php  if ($mcta) { ?>dnl
    X_MENU_ITEM(<*Topics*>, <*topics/*>)
<?php  } ?>dnl
<?php  if ($mua) { ?>dnl
    X_MENU_ITEM(<*Users*>, <*users/*>)
<?php  } ?>dnl
<?php  if ($muta) { ?>dnl
    X_MENU_ITEM(<*User Types*>, <*u_types/*>)
<?php  } ?>dnl
<?php  if ($mata) { ?>dnl
    X_MENU_ITEM(<*Article Types*>, <*a_types/*>)
<?php  } ?>dnl
<?php  if ($mcoa) { ?>dnl
    X_MENU_ITEM(<*Countries*>, <*country/*>)
<?php  } ?>dnl
<?php  if ($mla) { ?>dnl
    X_MENU_ITEM(<*Languages*>, <*languages/*>)
<?php  } ?>dnl
<?php  if ($mda) { ?>dnl
    X_MENU_ITEM(<*Glossary*>, <*glossary/*>)
<?php  } ?>dnl
<?php  if ($mca) { ?>dnl
    X_MENU_ITEM(<*Infotype*>, <*infotype/*>)
<?php  } ?>dnl
<?php  if ($vla) { ?>dnl
    X_MENU_ITEM(<*Logs*>, <*logs/*>)
<?php  } ?>dnl
<?php  if ($mlza) { ?>dnl
    X_MENU_BAR
    X_MENU_ITEM(<*Localizer*>, <*localizer/*>)
<?php  } ?>dnl
    X_MENU_BAR
<?php 
## added by sebastian
if (function_exists ("incModFile"))
	incModFile ($TOL_UserId);
?>
    X_MENU_ITEM(<*Logout*>, <*logout.php*>)
E_MENU

E_MBODY
<?php  } ?>dnl

E_DATABASE
E_HTML
