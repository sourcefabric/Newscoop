B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteArticle*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting article*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete articles.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Article');
    todefnum('Language');
    todefnum('sLanguage');
?>dnl
B_HEADER(<*Deleting article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		    query ("SELECT Name FROM Languages WHERE Id=$sLanguage", 'q_slang');

		    fetchRow($q_art);
		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
		    fetchRow($q_slang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Delete article*>)
	X_MSGBOX_TEXT(<*
<?
    $del= 1;
    query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_nr');
    fetchRowNum($q_nr);
    
    if (getNumVar($q_nr,0) == "1") {
	query ("SELECT COUNT(*) FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article", 'q_img');
	fetchRowNum($q_img);
	
	if (getNumVar($q_img,0) != 0) {
	    $del= 0;
	    ?>
	<LI><? putGS('There are $1 image(s) left.',getNumVar($q_img,0) ); ?></LI>
	<? }
    }
    
    $AFFECTED_ROWS=0;
    if ($del)
	query ("DELETE FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage");

    if ($AFFECTED_ROWS > 0) {
	query ("DELETE FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage");
	query ("DELETE FROM ArticleIndex WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article AND IdLanguage=$sLanguage");
	$del= 1;
    } else {
	$del= 0;
    }
    
    if ($del) { ?>dnl
		<LI><? putGS('The article $1 ($2) has been deleted.','<B>'.getHVar($q_art,'Name'),getHVar($q_slang,'Name').'</B>' ); ?></LI>
X_AUDIT(<*32*>, <*getGS('Article $1 ($2) deleted from $3. $4 from $5. $6 ($7) of $8',getSVar($q_art,'Name'),getSVar($q_slang,'Name'),getSVar($q_sect,'Number'),getSVar($q_sect,'Name'),getSVar($q_iss,'Number'),getSVar($q_iss,'Name'),getSVar($q_lang,'Name'),getSVar($q_pub,'Name') )*>)
<? } else { ?>dnl
		<LI><? putGS('The article $1 ($2) could not be deleted.','<B>'.getHVar($q_art,'Name'),getHVar($q_slang,'Name').'</B>' ); ?></LI>
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?
    if ($del) { ?>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI>No such issue.</LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI>No such section.</LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI>No such article.</LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

