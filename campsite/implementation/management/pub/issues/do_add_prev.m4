B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Copying previous issue*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<? }
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
    query ("SELECT * FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('cOldNumber');
    todefnum('cNumber');
    todefnum('cPub');
?>dnl
B_HEADER(<*Copying previous issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($cPub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$cPub", 'publ');
    if ($NUM_ROWS) {
	fetchRow($publ);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($publ,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Copying previous issue*>)
	X_MSGBOX_TEXT(<*
<?
    query ("SELECT * FROM Issues WHERE IdPublication=$cPub AND Number=$cOldNumber", 'q_iss');
    	//copy the whole structure; translated issues may exists
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_iss);
	$idlang=getVar($q_iss,'IdLanguage');

	query ("INSERT IGNORE INTO Issues SET IdPublication=$cPub, Number=$cNumber, IdLanguage=$idlang, Name='".getSVar($q_iss,'Name')."', FrontPage='".getSVar($q_iss,'FrontPage')."', SingleArticle='".getSVar($q_iss,'SingleArticle')."'");
	query ("SELECT * FROM Sections WHERE IdPublication=$cPub AND NrIssue=$cOldNumber AND IdLanguage=$idlang", 'q_sect');
	$nr2=$NUM_ROWS;
	for($loop2=0;$loop2<$nr2;$loop2++) {
	    fetchRow($q_sect);
	    query ("INSERT IGNORE INTO Sections SET IdPublication=$cPub, NrIssue=$cNumber, IdLanguage=$idlang, Number=".getSVar($q_sect,'Number').", Name='".getSVar($q_sect,'Name')."'");
	}
    }
?>dnl
X_AUDIT(<*11*>, <*getGS('New issue $1 from $2 in publication $3',$cNumber,$cOldNumber,getSVar($publ,'Name'))*>)
	<LI><? putGS('Copying done.'); ?></LI>
	*>)
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/?Pub=<? pencURL($cPub); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
