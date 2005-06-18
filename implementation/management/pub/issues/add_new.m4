INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Add new issue*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todefnum('Pub'); ?>dnl
B_HEADER(<*Add new issue*>)
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
	query("SELECT IdDefaultLanguage as IdLang FROM Publications WHERE Id=$Pub", 'q_lang');
	fetchRow($q_lang);
	$IdLang = getVar($q_lang,'IdLang');

	query ("SELECT Id, OrigName FROM Languages ORDER BY Name", 'q_lang');
	$rownr=$NUM_ROWS;
	query ("SELECT MAX(Number) + 1 FROM Issues WHERE IdPublication=$Pub", 'q_nr');
	fetchRowNum($q_nr);
	if (getNumVar($q_nr,0) == "")
		$nr= 1;
	else
		$nr=getNumVar($q_nr,0);
?>dnl
<P>
B_DIALOG(<*Add new issue*>, <*POST*>, <*do_add_new.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLang" class="input_select"><?php 
    for($loop=0;$loop<$rownr;$loop++) {
	fetchRow($q_lang);
	pcomboVar(getHVar($q_lang,'Id'), $IdLang, getHVar($q_lang,'OrigName'));
    }
?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Number*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cNumber" VALUE="<?php  pencHTML($nr); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*URL Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32" value="<?php  pgetHVar($publ,'ShortName'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<?php  pencHTML($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
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
