B_HTML
INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteImage*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete image*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete images*>)
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
    todefnum('Image');
?>
B_HEADER(<*Delete image*>)
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
query ("SELECT Description, Photographer, Place, Date, ContentType FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article AND Number=$Image", 'q_img');
if ($NUM_ROWS) {
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
		    fetchRow($q_img);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_art,'Name'); ?></B>*>)
X_CURRENT(<*Image*>, <*<B><?php  pgetHVar($q_img,'Description'); ?> (<?php  pgetHVar($q_img,'Photographer'); ?>, <?php  pgetHVar($q_img,'Place'); ?>, <?php  pgetHVar($q_img,'Date'); ?>)</B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Delete image*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the image $1?','<B>'.getHVar($q_img,'Description').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
	    <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	    <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Image" VALUE="<?php  p($Image); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
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

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such image.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
