B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting issue*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete issues.*>)
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
?>dnl
B_HEADER(<*Deleting issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    fetchRow($q_iss);
	    fetchRow($q_pub);
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Deleting issue*>)
	X_MSGBOX_TEXT(<*
<?
    todefnum('del', 1);
    $NUM_ROWS = 0;
    $AFFECTED_ROWS = 0;
    query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 article(s) left.',getNumVar($q_art,0)); ?></LI>
    <? }
    
	if ($del){
		query ("SELECT IdPublication FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language LIMIT 1", 'q_sect');
		if ($NUM_ROWS) {
			query ("DELETE FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_sect');
	    	    	if ($AFFECTED_ROWS > 0) {?>
				<LI><? putGS('All sections from Issue $1 from publication $2 deleted','<B>'.getHVar($q_iss,'Name').'</B>', '<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
					X_AUDIT(<*12*>, <*getGS('All sections from Issue $1 from publication $2 deleted',getHVar($q_iss,'Name'),getHVar($q_pub,'Name'))*>)
			<? } else { ?>dnl
				<LI><? putGS('The issue $1 could not be deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
				<? $del = 0;
			}
		}
	}

	if ($del){
		query ("DELETE FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
		if ($AFFECTED_ROWS > 0) { ?>
			<LI><? putGS('The issue $1 has ben deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
			X_AUDIT(<*12*>, <*getGS('Issue $1 from publication $2 deleted',getHVar($q_iss,'Name'),getHVar($q_pub,'Name'))*>)
		<? } else { ?>dnl
			<LI><? putGS('The issue $1 could not be deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
		<? }
	} ?>dnl
*>)
	
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/?Pub=<? pencURL($Pub); ?>*>)
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

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
