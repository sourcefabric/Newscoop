INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Add new translation*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<?php  }
    query ("SELECT Name FROM Issues WHERE 1=0", 'q_iss');
    
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>    
B_HEADER(<*Add new translation*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
    	query ("SELECT Name FROM Issues WHERE IdPublication=$Pub AND Number=$Issue", 'q_iss');
	if ($NUM_ROWS) {
		$nriss=$NUM_ROWS;
        	fetchRow($q_pub);
//		fetchRow($q_iss);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<?php 
    query ("SELECT Languages.Id, Languages.OrigName FROM Languages LEFT JOIN Issues ON Issues.IdPublication = $Pub AND Issues.Number=$Issue AND Issues.IdLanguage = Languages.Id WHERE Issues.IdPublication IS NULL", 'q_lang');
    if ($NUM_ROWS) { 
        $nrlang=$NUM_ROWS;
?>dnl
<P>
B_DIALOG(<*Add new translation*>, <*POST*>, <*do_translate.php*>)
	B_DIALOG_INPUT(<*Issue*>)
			<?php  $comma= 0;
    for($loop=0;$loop<$nriss;$loop++) {
	fetchRow($q_iss);
	if ($comma)
	    print ', ';
	pgetHVar($q_iss,'Name');
	$comma =1;
    }
?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLang" class="input_select"><?php 
	for($loop2=0;$loop2<$nrlang;$loop2++) { 
		fetchRow($q_lang);
		pcomboVar(getHVar($q_lang,'Id'),'',getHVar($q_lang,'OrigName'));
        }
	    ?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<?php  pencHTML($Pub);?>">
		<INPUT TYPE="HIDDEN" NAME="cNumber" VALUE="<?php  pencHTML($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No more languages.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
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
