B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete subscription default time*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete subscription default time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Pub');
    todefnum('Language');
    todef('CountryCode');

    query ("SELECT * FROM Publications WHERE Id=$Pub", 'p');
    if ($NUM_ROWS) { 
	fetchRow($p);
?>dnl
<P>
B_MSGBOX(<*Delete subscription default time*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the subscription default time for $1?','<B>'.getHVar($p,'Name').':'.encHTML($CountryCode).'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_deldeftime.php">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? pencHTML($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? pencHTML($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="CountryCode" VALUE="<? pencHTML($CountryCode); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)
		</FORM>
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
