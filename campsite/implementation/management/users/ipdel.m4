B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete IP Group*>)
<? if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete IP Groups.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete IP Group*>)
B_HEADER_BUTTONS
X_HBUTTON(<*IP Access List*>, <*users/ipaccesslist.php?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? 
    todefnum('User');
    todefnum('StartIP');
    query ("SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, Addresses FROM SubsByIP WHERE IdUser=$User and StartIP=$StartIP", 'u');
    if ($NUM_ROWS) {
	fetchRowNum($u);
    ?>dnl
<P>
B_MSGBOX(<*Delete IP Group*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the IP Group $1?','<B>'.getNumVar($u,0).'.'.getNumVar($u,1).'.'.getNumVar($u,2).'.'.getNumVar($u,3).':'.getNumVar($u,4).'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="GET" ACTION="do_ipdel.php">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<? p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="StartIP" VALUE="<? p($StartIP); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/users/ipaccesslist.php?User=<? p($User); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such IP Group.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
