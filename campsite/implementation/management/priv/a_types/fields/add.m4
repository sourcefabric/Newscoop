INCLUDE_PHP_LIB(<*article_type_fields*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_TITLE(<*Add new field*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add article type fields.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todef('AType'); ?>dnl
B_HEADER(<*Add new field*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Fields*>, <*a_types/fields/?AType=<?php  print encHTML($AType); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<?php  print encHTML($AType); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new field*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
		<SELECT NAME="cType" class="input_select">
			<OPTION VALUE="1"><?php  putGS('Text'); ?>
			<OPTION VALUE="2"><?php  putGS('Date'); ?>
			<OPTION VALUE="3"><?php  putGS('Article body'); ?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<?php  print encHTML($AType); ?>">
		SUBMIT(<*OK*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/a_types/fields/?AType=<?php  print encURL($AType); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
