<HTML>
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
        X_COOKIE(<*TOL_Access=all*>)
	X_COOKIE(<*TOL_Preview=on*>)
	X_TITLE(<*Preview article*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  
    if ($access) {

        todefnum('Pub');
        todefnum('Issue');
        todefnum('Section');
        todefnum('Language');
        todefnum('sLanguage');
        todefnum('Article');


    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		fetchRow($q_art);
    		fetchRow($q_sect);
		fetchRow($q_pub);
		query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
		fetchRow($q_iss);
	        if (($NUM_ROWS !=0)&&(getVar($q_iss,'SingleArticle') != "")) {
		    ?>dnl
<FRAMESET ROWS="60%,*" BORDER="2">
<FRAME SRC="<?php  pgetVar($q_iss,'SingleArticle'); ?>?IdPublication=<?php  p($Pub); ?>&NrIssue=<?php  p($Issue); ?>&NrSection=<?php  p($Section); ?>&NrArticle=<?php  p($Article); ?>&IdLanguage=<?php  p($sLanguage); ?>" NAME="body" FRAMEBORDER="1">
<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1">
</FRAMESET>
<?php  } else { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Preview article*>)
X_HEADER_NO_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    query ("SELECT Name FROM Languages WHERE Id=$sLanguage", 'q_slang');
    fetchRow($q_lang);
    fetchRow($q_slang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_art,'Name'); ?> (<?php  pgetHVar($q_slang,'Name'); ?>)</B>*>)
E_CURRENT

<BLOCKQUOTE>
	<LI><?php  putGS('This article cannot be previewed. Please make sure it has a <B><I>single article</I></B> template selected.'); ?></LI>
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
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

<?php  } ?>dnl

E_DATABASE
E_HTML
