B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/templates*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Create new folder*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create folders.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todef('Path'); ?>dnl
B_HEADER(<*Create new folder*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<?php  pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><?php  pencHTML(decURL($Path)); ?></B>*>)
E_CURRENT

<P>

B_DIALOG(<*Create new folder*>, <*POST*>, <*do_new_dir.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cPath" VALUE="<?php  pencHTML(decS($Path)); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  pencHTML(decS($Path)); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
