B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting user type*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete user types.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting user type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*User Types*>, <*u_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
<? query ("DELETE FROM UserTypes WHERE Name='$UType'"); ?>dnl
B_MSGBOX(<*Deleting user type*>)
<? if ($AFFECTED_ROWS > 0) { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('The user type has been deleted.'); ?></LI>*>)
X_AUDIT(<*122*>, <*getGS('User type $1 deleted',encHTML($UType))*>)
<? } else { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('The user type could not be deleted.'); ?></LI>*>)
<? } ?>
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

