INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
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
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<TABLE>
<TR>
	<TD>X_NEW_BUTTON(<*Back to publication*>, <*edit.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
	<TD>X_NEW_BUTTON(<*Back to aliases*>, <*aliases.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
</TR>
</TABLE>

<P>
B_DIALOG(<*Add new alias*>, <*POST*>, <*do_add_alias.php*>)
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<?php  pencHTML($Pub); ?>">
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/aliases.php?Pub=<?php  pencURL($Pub); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
        <LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
