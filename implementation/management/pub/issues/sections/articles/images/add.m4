B_HTML
INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddImage*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new image*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add images*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Article');
    todefnum('Language');
    todefnum('sLanguage');
?>
B_HEADER(<*Add new image*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Images*>, <*pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>*>)
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
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_art,'Name'); ?></B>*>)
E_CURRENT

<?php 
    query ("SELECT MAX(Number) + 1 FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article", 'q_nr');
    fetchRowNum($q_nr);
    if (getNumVar($q_nr,0) == "")
	$nr= 1;
    else
	$nr=getNumVar($q_nr,0);
?>dnl
<P>
B_DIALOG(<*Add new image*>, <*POST*>, <*/cgi-bin/upload_i*>, <*multipart/form-data*>)
	B_DIALOG_INPUT(<*Number*>)
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<?php  p($nr); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Description*>)
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="Image <?php  p($nr); ?>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Photographer*>)
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<?php  pgetHVar($Usr,'Name'); ?>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Place*>)
		<INPUT TYPE="TEXT" NAME="cPlace" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Date*>)
<?php  
    query ("SELECT LEFT(NOW(), 10)", 'q_now');
    fetchRowNum($q_now);
?>dnl
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php  pgetNumVar($q_now,0); ?>" SIZE="10" MAXLENGTH="10"> <?php  putGS('YYYY-MM-DD'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Image*>)
		<INPUT TYPE="FILE" NAME="cImage" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
	    <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	    <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

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
