INCLUDE_PHP_LIB(<*$ADMIN_DIR/a_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteArticleTypes*>)

B_HEAD
	X_TITLE(<*Delete article type*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete article types.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete article type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

<?php  todef('AType'); ?>dnl
<P>
B_MSGBOX(<*Delete article type*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the article type $1?','<B>'.encHTML($AType).'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<?php  print encHTML($AType); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/a_types/*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

