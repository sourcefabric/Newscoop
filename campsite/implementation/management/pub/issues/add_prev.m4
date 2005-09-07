INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Copy previous issue*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todefnum('Pub'); ?>dnl
B_HEADER(<*Copy previous issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'publ');
    if ($NUM_ROWS) {
	fetchRow($publ);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($publ,'Name'); ?>*>)
E_CURRENT

<?php 
    query ("SELECT MAX(Number) FROM Issues WHERE IdPublication=$Pub", 'q_nr');
    fetchRowNum($q_nr);
    if (getNumVar($q_nr,0) == "") { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No previous issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } else { ?>dnl
<P>
B_DIALOG(<*Copy previous issue*>, <*POST*>, <*do_add_prev.php*>)
	X_DIALOG_TEXT(<*<?php  putGS('Copy structure from issue nr $1','<B>'.getNumVar($q_nr,0).'</B>'); ?>*>)
	B_DIALOG_INPUT(<*Number*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cNumber" VALUE="<?php  print (getNumVar($q_nr,0) + 1); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cOldNumber" VALUE="<?php  pgetNumVar($q_nr,0); ?>">
		<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<?php  pencHTML($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } ?>dnl

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
