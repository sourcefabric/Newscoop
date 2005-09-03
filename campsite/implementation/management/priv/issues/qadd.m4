INCLUDE_PHP_LIB(<*$ADMIN_DIR/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Add new issue*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
?>dnl
B_HEADER(<*Add new issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'publ');
    if ($NUM_ROWS) { 
	fetchRow($publ);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  getHVar($publ,'Name'); ?>*>)
E_CURRENT

<P>
B_HOME_MENU(<*99%*>)
	B_HOME_MENU_HEADER
		X_HOME_MENU_TH(<*<?php  putGS('Use the structure of the previous issue'); ?>*>, <*add_prev.php?Pub=<?php  pencURL($Pub); ?>*>)
	E_HOME_MENU_HEADER
	B_HOME_MENU_BODY
		B_HOME_MENU_TD
			X_HOME_MENU_ITEM(<*<LI><?php  putGS('Copy the entire structure in all languages from the previous issue except for content.'); ?><LI><?php  putGS('You may modify it later if you wish.'); ?></LI>*>)
		E_HOME_MENU_TD
	B_HOME_MENU_BODY
	B_HOME_MENU_HEADER
		X_HOME_MENU_TH(<*<?php  putGS('Create a new structure'); ?>*>, <*add_new.php?Pub=<?php  pencURL($Pub); ?>*>)
	E_HOME_MENU_HEADER
	B_HOME_MENU_BODY
		B_HOME_MENU_TD
			X_HOME_MENU_ITEM(<*<LI><?php  putGS('Create a complete new structure.'); ?><LI><?php  putGS('You must define an issue type for each language and then sections for them.'); ?></LI>*>)
		E_HOME_MENU_TD
	B_HOME_MENU_BODY
E_HOME_MENU
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
