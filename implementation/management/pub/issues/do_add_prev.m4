B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Copying previous issue*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<?php  }
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
    query ("SELECT * FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('cOldNumber');
    todefnum('cNumber');
    todefnum('cPub');
?>dnl
B_HEADER(<*Copying previous issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($cPub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$cPub", 'publ');
    if ($NUM_ROWS) {
	fetchRow($publ);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($publ,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Copying previous issue*>)
	X_MSGBOX_TEXT(<*
<?php 
query ("SELECT * FROM Issues WHERE IdPublication=$cPub AND Number=$cOldNumber", 'q_iss');
//copy the whole structure; translated issues may exists
$nr=$NUM_ROWS;
for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_iss);
	$idlang=getVar($q_iss,'IdLanguage');

	$sql = "INSERT INTO Issues SET IdPublication=$cPub, Number=$cNumber, IdLanguage=$idlang, Name='" . getSVar($q_iss,'Name') . "', IssueTplId=" . getSVar($q_iss,'IssueTplId') . ", SectionTplId=" . getSVar($q_iss,'SectionTplId') . ", ArticleTplId=" . getSVar($q_iss,'ArticleTplId') . ", ShortName = " . $cNumber;
	query($sql);
	query ("SELECT * FROM Sections WHERE IdPublication=$cPub AND NrIssue=$cOldNumber AND IdLanguage=$idlang", 'q_sect');
	$nr2=$NUM_ROWS;
	for($loop2=0;$loop2<$nr2;$loop2++) {
	    fetchRow($q_sect);
	    $sql = "INSERT INTO Sections SET IdPublication=$cPub, NrIssue=$cNumber, IdLanguage=$idlang, Number=".getSVar($q_sect,'Number').", Name='".getSVar($q_sect,'Name')."', ShortName=".getSVar($q_sect,'Number') . ", SectionTplId=" . getSVar($q_iss,'SectionTplId') . ", ArticleTplId=" . getSVar($q_iss,'ArticleTplId');
		query($sql);
	}
}
?>dnl
	X_AUDIT(<*11*>, <*getGS('New issue $1 from $2 in publication $3', $cNumber, $cOldNumber, getSVar($publ,'Name'))*>)
	<LI><?php  putGS('Copying done.'); ?></LI>
	*>)
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($cPub); ?>*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
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
