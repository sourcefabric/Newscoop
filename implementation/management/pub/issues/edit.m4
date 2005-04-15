INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Change issue details*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change issue details.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl

B_HEADER(<*Change issue details*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'publ');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Id, Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($q_pub);
	    fetchRow($q_lang);
	    fetchRow($publ);
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Issue*>, <*<?php  pgetHVar($publ,'Number'); ?>. <?php  pgetHVar($publ,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)*>)
E_CURRENT

<P>
B_DIALOG(<*Change issue details*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64" value="<?php  pgetHVar($publ,'Name'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
	    <SELECT NAME="cLang" class="input_select">
<?php 
    query ("SELECT Id, OrigName FROM Languages", 'q_lang');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_lang);
	pcomboVar(getVar($q_lang,'Id'),getVar($publ,'IdLanguage'),getVar($q_lang,'OrigName'));
    }
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
<?php
	if (getVar($publ, 'Published') == 'Y') {
?>
	B_DIALOG_INPUT(<*Publication date<BR><SMALL>(yyyy-mm-dd)</SMALL>*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cPublicationDate" SIZE="10" MAXLENGTH="10" value="<?php  pgetHVar($publ,'PublicationDate'); ?>">
	E_DIALOG_INPUT
<?php
	}
?>
	B_DIALOG_INPUT(<*Front Page Template*>)
		<SELECT NAME="cIssueTplId" class="input_select">
			<OPTION VALUE="0">---</OPTION>
<?php 
	query ("SELECT Id, Name FROM Templates ORDER BY Level ASC, Name ASC", 'q_iss_tpl');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_iss_tpl);
		pcomboVar(getVar($q_iss_tpl,'Id'),getVar($publ,'IssueTplId'),getVar($q_iss_tpl,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Section Template*>)
		<SELECT NAME="cSectionTplId" class="input_select">
			<OPTION VALUE="0">---</OPTION>
<?php 
	query ("SELECT Id, Name FROM Templates ORDER BY Level ASC, Name ASC", 'q_sect_tpl');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_sect_tpl);
		pcomboVar(getVar($q_sect_tpl,'Id'),getVar($publ,'SectionTplId'),getVar($q_sect_tpl,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Article Template*>)
		<SELECT NAME="cArticleTplId" class="input_select">
			<OPTION VALUE="0">---</OPTION>
<?php 
	query ("SELECT Id, Name FROM Templates ORDER BY Level ASC, Name ASC", 'q_art_tpl');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_art_tpl);
		pcomboVar(getVar($q_art_tpl,'Id'),getVar($publ,'ArticleTplId'),getVar($q_art_tpl,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Short Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32" value="<?php  pgetHVar($publ,'ShortName'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  pencHTML($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

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
