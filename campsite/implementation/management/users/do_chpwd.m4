B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing your password*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Changing your password*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php  $ok= 1; ?>dnl
<P>
B_MSGBOX(<*Changing your password*>)
	X_MSGBOX_TEXT(<*
<?php 
    query ("SELECT COUNT(*) FROM Users WHERE Id=".getSVar($Usr,'Id')." AND Password=password('$cOldPass')", 'urec');
    fetchRowNum($urec);
    if (getNumVar($urec,0) == 0) { ?>dnl
	<LI><?php  putGS('The password you typed is incorrect.'); ?></LI>
<?php 
    $ok= 0;
    }

    if ($ok) {
	if ((strlen(decS($cNewPass1))<6)||($cNewPass1!=$cNewPass2)) { ?>dnl
	    <LI><?php  putGS('The password must be at least 6 characters long and both passwords should match.'); ?></LI>
<?php 
    $ok= 0;
    }
    }

    if ($ok) {
	query ("UPDATE Users SET Password=password('$cNewPass1') WHERE Id=".getSVar($Usr,'Id'));
	if ($AFFECTED_ROWS <= 0) { ?>dnl
	<LI><?php  putGS('The password could not be changed.'); ?></LI>
<?php 
    $ok= 0;
    } else { ?>dnl
X_AUDIT(<*53*>, <*getGS('User $1 changed his password',getHVar($Usr,'UName'))*>)
	<LI><?php  putGS('The password has been changed.'); ?></LI>
<?php  } ?>dnl
<?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($ok) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/home.php*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/chpwd.php*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
