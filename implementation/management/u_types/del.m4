B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/u_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete user type*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete user types.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete user type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*User Types*>, <*u_types/*>)
E_HEADER_BUTTONS
E_HEADER

<?php  todef('UType');
    query ("SELECT * FROM UserTypes WHERE Name='$UType'", 'u');
    fetchRow($u);
    $name=getHVar($u,'Name');
?>dnl
<P>
<?php  if ($NUM_ROWS) { ?>dnl
B_MSGBOX(<*Delete user type*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the user type $1?','<B>'.$name.'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="UType" VALUE="<?php  print $name; ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/u_types/*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<?php  } else { ?>dnl
	<LI><?php  putGS('No such user type.'); ?></LI>
<?php  } ?>dnl
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

