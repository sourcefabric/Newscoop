B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteCountries*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting country*>)
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
B_HEADER(<*Deleting country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Countries WHERE Code='$Code' AND IdLanguage=$Language", 'q_ctr');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
?>dnl

<P>
B_MSGBOX(<*Deleting country*>)
	X_MSGBOX_TEXT(<*
<?
    query ("DELETE FROM Countries WHERE Code='$Code' AND IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { 
	fetchRow($q_ctr);
	fetchRow($q_lang);
	$del=1;
    ?>dnl
		<LI><? putGS('The country $1 has been deleted.' ,'<B>'.getHVar($q_ctr,'Name').'('.getHVar($q_lang,'Name').')</B>'); ?></LI>
X_AUDIT(<*134*>, <*getGS('Country $1 deleted',getSVar($q_ctr,'Name').' ('.getSVar($q_lang,'Name').')' )*>)
<? } else { ?>dnl
		<LI><? putGS('The country $1 could not be deleted.' ,'<B>'.getHVar($q_ctr,'Name').'('.getHVar($q_lang,'Name').')</B>'); ?></LI>
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? if ($del) { ?>dnl
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
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

