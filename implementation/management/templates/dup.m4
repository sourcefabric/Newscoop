INCLUDE_PHP_LIB(<*$ADMIN_DIR/templates*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_TITLE(<*Duplicate template*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create templates.*>)
<?php  } ?>dnl
E_HEAD

<?php
if ($access) {
	todef('Path');
	todef('Name');
?>

B_HEADER(<*Duplicate template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates?Path=<?php  pencURL(decS($Path)); ?>*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<?php  pencHTML(decURL($Path)); ?>*>)
X_CURRENT(<*Template*>, <*<?php  pencHTML(decURL($Name)); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Duplicate template*>, <*POST*>, <*do_dup.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cPath" VALUE="<?php  pencHTML(decS($Path)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<?php  pencHTML(decS($Name)); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*/<?php echo $ADMIN; ?>/templates?Path=<?php  pencURL(decS($Path)); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
