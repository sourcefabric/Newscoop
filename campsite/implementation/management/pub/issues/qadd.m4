B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new issue*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
?>dnl
B_HEADER(<*Add new issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'publ');
    if ($NUM_ROWS) { 
	fetchRow($publ);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? getHVar($publ,'Name'); ?></B>*>)
E_CURRENT

<P>
B_HOME_MENU(<*99%*>)
	B_HOME_MENU_HEADER
		X_HOME_MENU_TH(<*<? putGS('Use the structure of the previous issue'); ?>*>, <*add_prev.php?Pub=<? pencURL($Pub); ?>*>)
	E_HOME_MENU_HEADER
	B_HOME_MENU_BODY
		B_HOME_MENU_TD
			X_HOME_MENU_ITEM(<*<LI><? putGS('Copy the entire structure in all languages from the previous issue except for content.'); ?><LI><? putGS('You may modify it later if you wish.'); ?></LI>*>)
		E_HOME_MENU_TD
	B_HOME_MENU_BODY
	B_HOME_MENU_HEADER
		X_HOME_MENU_TH(<*<? putGS('Create a new structure'); ?>*>, <*add_new.php?Pub=<? pencURL($Pub); ?>*>)
	E_HOME_MENU_HEADER
	B_HOME_MENU_BODY
		B_HOME_MENU_TD
			X_HOME_MENU_ITEM(<*<LI><? putGS('Create a complete new structure.'); ?><LI><? putGS('You must define an issue type for each language and then sections for them.'); ?></LI>*>)
		E_HOME_MENU_TD
	B_HOME_MENU_BODY
E_HOME_MENU
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
