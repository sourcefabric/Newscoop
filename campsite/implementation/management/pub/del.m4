B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeletePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete publication*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete publications.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Pub');
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'p');
    if ($NUM_ROWS) {
	fetchRow($p);
?>dnl
<P>
B_MSGBOX(<*Delete publication*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the publication $1?','<B>'.getHVar($p,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? pencHTML($Pub); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*NO*>, <*No*>, <*X_ROOT/pub/*>)
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
