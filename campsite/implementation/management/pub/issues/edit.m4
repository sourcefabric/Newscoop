B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
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
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
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
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($publ,'Number'); ?>. <?php  pgetHVar($publ,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change issue details*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64" value="<?php  pgetHVar($publ,'Name'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
	    <SELECT NAME="cLang">
<?php 
    query ("SELECT Id, Name FROM Languages", 'q_lang');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_lang);
	pcomboVar(getVar($q_lang,'Id'),getVar($publ,'IdLanguage'),getVar($q_lang,'Name'));
    }
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Publication date<BR><SMALL>(yyyy-mm-dd)</SMALL>*>)
		<INPUT TYPE="TEXT" NAME="cPublicationDate" SIZE="10" MAXLENGTH="10" value="<?php  pgetHVar($publ,'PublicationDate'); ?>">
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
        <LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl    

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
