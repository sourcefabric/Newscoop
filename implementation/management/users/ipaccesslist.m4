B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*User management*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }

    query ("SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, StartIP, Addresses FROM SubsByIP WHERE 1=0", 'IPs');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*User IP access list management*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('User');
    query ("SELECT Name FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) { 
	fetchRow($users);
    ?>dnl
B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($users,'Name'); ?></B>*>)
E_CURRENT
<P>
<? } ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD>X_NEW_BUTTON(<*Add new IP address group*>, <*ipadd.php?User=<? p($User); ?>*>)</TD>
	<TD ALIGN="RIGHT">
	</TD>
</TABLE>

<P><?
    todefnum('IPOffs');
    if ($IPOffs < 0)
	$IPOffs= 0;
    
    query ("SELECT (StartIP & 0xff000000) >> 24 as ip0, (StartIP & 0x00ff0000) >> 16 as ip1, (StartIP & 0x0000ff00) >> 8 as ip2, StartIP & 0x000000ff as ip3, StartIP, Addresses FROM SubsByIP WHERE IdUser = $User LIMIT $IPOffs, 11", 'IPs');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= 10;
	$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Start IP*>)
		X_LIST_TH(<*Number of addresses*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($IPs);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? p(getHVar($IPs,'ip0').'.'.getHVar($IPs,'ip1').'.'.getHVar($IPs,'ip2').'.'.getHVar($IPs,'ip3') ); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($IPs,'Addresses'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete IP Group $1',getHVar($IPs,'StartIP') ); ?>*>, <*icon/x.gif*>, <*users/ipdel.php?User=<? p($User); ?>&StartIP=<? pgetVar($IPs,'StartIP'); ?>*>)
		E_LIST_ITEM
	E_LIST_TR
<? 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<? if ($IPOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*ipaccesslist.php?User=<? p($User); ?>&IPOffs=<? p($IPOffs - 10); ?>*>)
<? } ?>dnl
<? if ($nr < 11) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*ipaccesslist.php?User=<? p($User); ?>&IPOffs=<? p($IPOffs + 10); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No records.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

