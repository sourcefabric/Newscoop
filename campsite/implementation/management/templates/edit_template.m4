B_HTML
INCLUDE_PHP_LIB(<*..*>)dnl
B_DATABASE

<?php  todefnum('What'); ?>dnl

CHECK_BASIC_ACCESS
<?php  if ($What != 0) { ?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<?php  } ?>dnl

B_HEAD
	X_EXPIRES
	X_TITLE(<*Templates management*>)

<?php 
    if ($access == 0) {
	if ($What) { ?>dnl
	X_AD(<*You do not have the right to change default templates.*>)
<?php  } else { ?>dnl
	X_LOGOUT
<?php  }
    }
?>dnl
E_HEAD

<?php  if ($access) { 

SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todef('Path'); ?>dnl
<?php  todef('Name'); ?>dnl

B_HEADER(<*Edit template*>)
B_HEADER_BUTTONS

X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><A HREF="<?php  pencHTML(decURL($Path)); ?>"><?php  pencHTML(decURL($Path)); ?></A></B>*>)
X_CURRENT(<*Template*>, <*<B><?php  pencHTML(decURL($Name)); ?></B>*>)
E_CURRENT


B_DIALOG(<*Edit template*>, <*POST*>, <*do_edit.php*>)

	B_DIALOG_BUTTONS
<?php  if ($dta != 0) { ?>
	SUBMIT(<*Save*>, <*Save changes*>)
	REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  pencHTML(decS($Path)); ?>*>)
<?php  } else { ?>
	REDIRECT(<*Done*>, <*Done*>, <*<?php  pencHTML(decS($Path)); ?>*>)
<?php  } ?>
	E_DIALOG_BUTTONS

<?php 
	$filename = "$DOCUMENT_ROOT".decURL($Path)."$Name";
	$fd = fopen ($filename, "r");
	$contents = fread ($fd, filesize ($filename));
	fclose ($fd);
?>

	<TR><TD><TEXTAREA ROWS="28" COLS="90" NAME="cField" WRAP="NO"><?php  p(decS($contents)) ?></TEXTAREA></TD></TR>
	<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<?php  p($Path); ?>">
	<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<?php  p($Name); ?>">

	B_DIALOG_BUTTONS
<?php  if ($dta != 0) { ?>
	SUBMIT(<*Save*>, <*Save changes*>)
	REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  pencHTML(decS($Path)); ?>*>)
<?php  } else { ?>
	REDIRECT(<*Done*>, <*Done*>, <*<?php  pencHTML(decS($Path)); ?>*>)
<?php  } ?>
	E_DIALOG_BUTTONS
E_DIALOG



X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


