B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing your password*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Changing your password*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? $ok= 1; ?>dnl
<P>
B_MSGBOX(<*Changing your password*>)
	X_MSGBOX_TEXT(<*
<?
    query ("SELECT COUNT(*) FROM Users WHERE Id=".getSVar($Usr,'Id')." AND Password=password('$cOldPass')", 'urec');
    fetchRowNum($urec);
    if (getNumVar($urec,0) == 0) { ?>dnl
	<LI><? putGS('The password you typed is incorrect.'); ?></LI>
<?
    $ok= 0;
    }

    if ($ok) {
	if ((strlen(decS($cNewPass1))<6)||($cNewPass1!=$cNewPass2)) { ?>dnl
	    <LI><? putGS('The password must be at least 6 characters long and both passwords should match.'); ?></LI>
<?
    $ok= 0;
    }
    }

    if ($ok) {
	query ("UPDATE Users SET Password=password('$cNewPass1') WHERE Id=".getSVar($Usr,'Id'));
	if ($AFFECTED_ROWS <= 0) { ?>dnl
	<LI><? putGS('The password could not be changed.'); ?></LI>
<?
    $ok= 0;
    } else { ?>dnl
X_AUDIT(<*53*>, <*getGS('User $1 changed his password',getHVar($Usr,'UName'))*>)
	<LI><? putGS('The password has been changed.'); ?></LI>
<? } ?>dnl
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? if ($ok) { ?>dnl
		<A HREF="X_ROOT/home.php"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/users/chpwd.php"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
