B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteArticleTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting field*>)
	<? if ($access == 0) { ?>
		X_AD(<*You do not have the right to delete article type fields.*>)
	<? } ?>
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<? todef('AType');
todef('Field');
    ?>dnl

B_HEADER(<*Deleting field*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Fields*>, <*a_types/fields/?AType=<? print encURL($AType); ?>*>)
X_HBUTTON(<*Article Types*>, <*a_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<B><? print encHTML($AType); ?></B>*>)
E_CURRENT

<P>
<?
    query ("SHOW COLUMNS FROM X$AType LIKE 'F$Field'", 'c');
    if ($NUM_ROWS)
	query ("ALTER TABLE X$AType DROP COLUMN F$Field");
?>dnl
B_MSGBOX(<*Deleting field*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('The field $1 has been deleted.','<B>'.encHTML($Field).'</B>' ); ?></LI>*>)
X_AUDIT(<*72*>, <*getGS('Article type field $1 deleted',encHTML($Field))*>)
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/a_types/fields/?AType=<? print encURL($AType); ?>*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
