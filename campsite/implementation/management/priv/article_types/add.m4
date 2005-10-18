INCLUDE_PHP_LIB(<*article_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_TITLE(<*Add new article type*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add article types.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new article type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new article type*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="15" MAXLENGTH="15">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Ok*>, <*Save changes*>)
<?php  todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php  print encHTML($Back); ?>">
<?php  if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  print ($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/a_types/*>)
<?php  } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
