B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new issue*>)
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
    todefnum('cPub');
?>dnl
B_HEADER(<*Adding new issue*>)
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

<?
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new issue*>)
	X_MSGBOX_TEXT(<*
<?
    $cName=trim($cName);
    $cNumber=trim($cNumber);
    
    if ($cLang == 0) {
	$correct= 0; ?>dnl
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
    }
    
    if ($created) { ?>dnl
		<LI><? putGS('The issue $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*11*>, <*getGS('Issue $1 added in publication $2',$cName,getVar($publ,'Name'))*>)
<? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The issue could not be added.'); ?></LI><LI><? putGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
<? }
}
?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*Another*>, <*Add another*>, <*X_ROOT/pub/issues/add_new.php?Pub=<? pencURL($cPub); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/?Pub=<? pencURL($cPub); ?>*>)
	E_MSGBOX_BUTTONS
<? } else { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/add_new.php?Pub=<? pencURL($cPub); ?>*>)
	E_MSGBOX_BUTTONS
<? } ?>dnl
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
