INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Add new alias*>)
<?php if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<?php } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todefnum('Pub');
?>
B_HEADER(<*Add new alias*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<TABLE>
<TR>
	<TD>X_BACK_BUTTON(<*Back to publication*>, <*edit.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
	<TD>X_BACK_BUTTON(<*Back to aliases*>, <*aliases.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
</TR>
</TABLE>

<P>
B_DIALOG(<*Add new alias*>, <*POST*>, <*do_add_alias.php*>)
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<?php  pencHTML($Pub); ?>">
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/aliases.php?Pub=<?php  pencURL($Pub); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
        <LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
