B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteCountries*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete country*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete countries.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
	todef('Code');
	todefnum('Language');
?>dnl
B_HEADER(<*Delete country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
	query ("SELECT * FROM Countries WHERE Code='$Code' AND IdLanguage=$Language", 'q_ctr');
	fetchRow($q_ctr);
	if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_lang);
?>dnl

<P>
B_MSGBOX(<*Delete country*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the country $1?' ,'<B>'.getHVar($q_ctr,'Name').'('.getHVar($q_lang,'Name').')</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Code" VALUE="<? print encHTML(decS($Code)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? print $Language; ?>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else {?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such country.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


