B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteArticleTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete field*>)
	<? if ($access == 0) { ?>
		X_AD(<*You do not have the right to delete article type fields.*>)
	<? } ?>
E_HEAD

<? if ($access) { ?>
B_STYLE
E_STYLE

B_BODY

<? todef('AType');
todef('Field');?>dnl

B_HEADER(<*Delete field*>)
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
B_MSGBOX(<*Delete field*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the field $1?','<B>'.encHTML($Field).'</B>'); ?></LI>
		<LI><? putGS('You will also delete all fields with this name from all articles of this type from all publications.'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<? print encHTML($AType); ?>">
		<INPUT TYPE="HIDDEN" NAME="Field" VALUE="<? print encHTML($Field); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/a_types/fields/?AType=<? print encURL($AType); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>

E_DATABASE
E_HTML
