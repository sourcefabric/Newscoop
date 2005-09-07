INCLUDE_PHP_LIB(<*$ADMIN_DIR/a_types/fields*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteArticleTypes*>)

B_HEAD
	X_TITLE(<*Delete field*>)
	<?php  if ($access == 0) { ?>
		X_AD(<*You do not have the right to delete article type fields.*>)
	<?php  } ?>
E_HEAD

<?php  if ($access) { ?>
B_STYLE
E_STYLE

B_BODY

<?php  todef('AType');
todef('Field');?>dnl

B_HEADER(<*Delete field*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Fields*>, <*a_types/fields/?AType=<?php  print encURL($AType); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<?php  print encHTML($AType); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Delete field*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the field $1?','<B>'.encHTML($Field).'</B>'); ?></LI>
		<LI><?php  putGS('You will also delete all fields with this name from all articles of this type from all publications.'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<?php  print encHTML($AType); ?>">
		<INPUT TYPE="HIDDEN" NAME="Field" VALUE="<?php  print encHTML($Field); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/a_types/fields/?AType=<?php  print encURL($AType); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>

E_DATABASE
E_HTML
