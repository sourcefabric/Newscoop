B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Translate article*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT Name FROM Articles WHERE 1=0", 'q_alist');
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
?>dnl
E_HEAD

B_STYLE
E_STYLE

<?php  if ($access) { ?>dnl
B_BODY

<?php  
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Article');
    todefnum('Language');
?>dnl
B_HEADER(<*Translate article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');

		    fetchRow($q_art);
		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

CHECK_XACCESS(<*ChangeArticle*>)

<?php 
    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
    fetchRowNum($q_xperm);
    if (getNumVar($q_xperm,0)) { ?>dnl
<P>
B_DIALOG(<*Translate article*>, <*POST*>, <*do_translate.php*>)
	B_DIALOG_INPUT(<*Article*>)
<?php 
    query ("SELECT Name FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_alist');
    $comma= 0;
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_alist);
	if ($comma)
	    print ', ';
	else
	    $comma= 1;
	pgetHVar($q_alist,'Name');
    }
?>dnl
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLanguage">
<?php 
    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'q_lang');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_lang);
	query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=".getVar($q_lang,'Id'), 'q_xart');
	fetchRowNum($q_xart);
	if (getNumVar($q_xart,0) == 0)
			pcomboVar(getHVar($q_lang,'Id'),'',getHVar($q_lang,'Name'));
    }
?>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Keywords*>)
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="cType" VALUE="<?php  pgetHVar($q_art,'Type'); ?>">
		<INPUT TYPE="HIDDEN" NAME="cOnFrontPage" VALUE="<?php  pgetHVar($q_art,'OnFrontPage'); ?>">
		<INPUT TYPE="HIDDEN" NAME="cOnSection" VALUE="<?php  pgetHVar($q_art,'OnSection'); ?>">
		<INPUT TYPE="HIDDEN" NAME="cPublic" VALUE="<?php  pgetHVar($q_art,'Public'); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
<?php  todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php  pencHTML($Back); ?>">
<?php  if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  p($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
<?php  } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
    X_XAD(<*You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
