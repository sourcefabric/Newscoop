B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new translation*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todef('cName');
    todefnum('cNumber');
    todefnum('cLang');
    todefnum('Language');
    todefnum('cPub');
?>
B_HEADER(<*Adding new translation*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($cPub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? 
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new translation*>)
	X_MSGBOX_TEXT(<*
<?
    $cName=trim($cName);
    $cNumber=trim($cNumber);
    
    if ($cLang == 0) {
	$correct= 0;
	?>dnl
		<LI><? putGS('You must select a language.'); ?></LI>
    <? }
    
    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <? }
    
    if ($cNumber == "" || $cNumber == " ") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); ?></LI>
    <? }
    
    if ($correct) {
	query ("INSERT IGNORE INTO Issues SET Name='$cName', IdPublication=$cPub, IdLanguage=$cLang, Number=$cNumber");
	$created= ($AFFECTED_ROWS > 0);
	if($created){
		query ("SELECT * FROM Sections WHERE IdPublication=$cPub AND NrIssue=$cNumber AND IdLanguage=$Language", 'q_sect');
		$nr2=$NUM_ROWS;
		for($loop2=0;$loop2<$nr2;$loop2++) {
			fetchRow($q_sect);
			query ("INSERT IGNORE INTO Sections SET IdPublication=$cPub, NrIssue=$cNumber, IdLanguage=$cLang, Number=".getSVar($q_sect,'Number').", Name='".getSVar($q_sect,'Name')."'");
		}
	}
    }

    if ($created) { ?>dnl
		<LI><? putGS('The issue $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>' ); ?></LI>
X_AUDIT(<*11*>, <*getGS('Issue $1 added',$cName)*>)
<? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The issue could not be added.'); ?></LI><LI><? putGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
<? }
} ?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/translate.php?Pub=<? pencURL($cPub); ?>&Issue=<? pencURL($cNumber); ?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another issue"></A>
		<A HREF="X_ROOT/pub/issues/?Pub=<? pencURL($cPub); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/translate.php?Pub=<? pencURL($cPub); ?>&Issue=<? pencURL($cNumber); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
