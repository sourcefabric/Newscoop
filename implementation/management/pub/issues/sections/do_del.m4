B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteSection*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting section*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add sections.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
    todefnum('Section');
?>dnl
B_HEADER(<*Deleting section*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) { 
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang'); 
		fetchRow($q_sect);
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Deleting section*>)
	X_MSGBOX_TEXT(<*
<?
    $del= 1;
    query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND IdLanguage=$Language", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 article(s) left.',getNumVar($q_art,0) ); ?></LI>
    <? }
    
    if ($del)
	query ("DELETE FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section");
    
    if ($AFFECTED_ROWS > 0) { ?>dnl
	<LI><? putGS('The section $1 has been deleted.','<B>'.gethVar($q_sect,'Name').'</B>'); ?></LI>
X_AUDIT(<*22*>, <*getGS('Section $1 deleted from issue $2. $3 $4 of $5',getHVar($q_sect,'Name'),getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_lang,'Name'),getHVar($q_pub,'Name')  )*>)
<? } else { ?>
	<LI><? putGS('The section $1 could not be deleted.','<B>'.getHVar($q_sect,'Name').'</B>'); ?></LI>
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		<A HREF="X_ROOT/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
