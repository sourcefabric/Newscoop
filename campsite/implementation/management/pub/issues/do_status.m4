B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)
CHECK_XACCESS(<*Publish*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing issue status*>)
<? if ($access == 0 || $xaccess == 0) { ?>dnl
	X_XAD(<*You do not have the right to change issues.*>, <*pub/issues/?Pub=<? p($Pub); ?>&Language=<? p($Language); ?> ?>*>)
<? } ?>dnl
E_HEAD

<? if ($access && $xaccess) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl
B_HEADER(<*Changing issue status*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?
    query ("SELECT Number, Name, Published FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing issue status*>)
<?

	$AFFECTED_ROWS= 0;
	query ("UPDATE Issues SET PublicationDate=IF(Published = 'N', NOW(), PublicationDate), Published=IF(Published = 'N', 'Y', 'N') WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
	if ($AFFECTED_ROWS > 0) {
		if (getVar($q_iss,'Published') == "Y") {
			$t2='Published';
			$t3='Not published';
		}
		else {
			$t2='Not published';
			$t3='Published';
		} ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Status of the issue $1 has been changed from $2 to $3','<B>'.getHVar($q_iss,'Number').'. '.getHVar($q_iss,'Name').' ('.getHVar($q_lang,'Name').')</B>',"<B>$t2</B>","<B>$t3</B>"); ?></LI>*>)

X_AUDIT(<*14*>, <*getGS('Issue $1 Published: $2  changed status',getVar($q_iss,'Number').'. '.getVar($q_iss,'Name').' ('.getVar($q_lang,'Name').')',getVar($q_iss,'Published'))*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Status of the issue $1 could not be changed.','<B>'.getVar($q_iss,'Number').'. '.getVar($q_iss,'Name').' ('.getVar($q_lang,'Name').')</B>'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<? 
    if ($AFFECTED_ROWS > 0) { ?>dnl
		<A HREF="X_ROOT/pub/issues/?Pub=<? pencURL($Pub); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/pub/issues/status.php?Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

