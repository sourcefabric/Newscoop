B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Updating issue*>)
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
    todefnum('cLang');
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl

B_HEADER(<*Changing issue's details*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($cPub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'publ');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Id, Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($publ);
	    fetchRow($q_pub);
	    fetchRow($q_lang);
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($publ,'Number'); ?>. <? pgetHVar($publ,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
E_CURRENT   

<?
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Changing issue's details*>)
	X_MSGBOX_TEXT(<*

<?
    $cName=trim($cName);
	///!sql query "SELECT TRIM('?cName')" q_x
    
    if ($cLang == 0) {
	$correct= 0; ?>dnl
		<LI><? putGS('You must select a language.'); ?></LI>
    <? }
    
    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <? }
    
    if ($correct) {
	
	query ("UPDATE Issues SET Name='$cName', IdLanguage=$cLang WHERE IdPublication=$Pub AND Number=$Issue");
	$created= ($AFFECTED_ROWS > 0);
    }
    
    if ($created) { ?>dnl
		<LI><? putGS('The issue $1 has been successfuly changed.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*11*>, <*getGS('Issue $1 updated in publication $2',$cName,getVar($publ,'Name'))*>)
<? } else {
    
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The issue could not be changed.'); ?></LI>
<!--LI>Please check if another issue with the same number/language does not already exist.</LI-->
<? }
}
?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/?Pub=<? pencURL($Pub); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/edit.php?Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
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
