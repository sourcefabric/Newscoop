B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*User management*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? query ("SELECT * FROM Users WHERE 1=0", 'Users');

} ?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mua*>, <*ManageUsers*>)
SET_ACCESS(<*dua*>, <*DeleteUsers*>)
SET_ACCESS(<*msa*>, <*ManageSubscriptions*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*User management*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todef('sUname');
    todef('sType');
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<? if ($mua != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new user account*>, <*add.php?Back=<? pencURL($REQUEST_URI); ?>*>)</TD>
	<? } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('User name'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" NAME="sUname" VALUE="<? pencHTML($sUname); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><SELECT NAME="sType"><OPTION><OPTION VALUE="Y" <? if ($sType == "Y") { ?>SELECTED<? } ?>><? putGS('Reader'); ?><OPTION VALUE="N" <? if ($sType == "N") { ?>SELECTED<? } ?>><? putGS('Staff'); ?></SELECT></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<P><?
    todefnum('UserOffs');
    if ($UserOffs < 0) $UserOffs= 0;
    todefnum('lpp', 20);

    query ("SELECT * FROM Users WHERE Name LIKE '%$sUname%' AND Reader LIKE '$sType%' ORDER BY Name ASC LIMIT $UserOffs, ".($lpp+1), 'Users');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Full Name*>)
		X_LIST_TH(<*User Name*>)
	<? if ($mua != 0) { ?>
		X_LIST_TH(<*IP Access*>, <*1%*>, <*nowrap*>)
		X_LIST_TH(<*Password*>, <*1%*>)
		X_LIST_TH(<*Reader*>, <*1%*>)
		X_LIST_TH(<*Info*>, <*1%*>)
		X_LIST_TH(<*Rights*>, <*1%*>)
	<? } else { ?>
		X_LIST_TH(<*Reader*>, <*1%*>)
	<? }
	    if ($dua != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($Users);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? pgetHVar($Users,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($Users,'UName'); ?>&nbsp;
		E_LIST_ITEM
	<? if ($mua != 0) {
		query ("SELECT COUNT(*) FROM SubsByIP WHERE IdUser=".getSVar($Users,'Id'), 'bip');
		fetchRowNum($bip);
		?>
		B_LIST_ITEM(<*CENTER*>)
                        <A HREF="X_ROOT/users/ipaccesslist.php?User=<? pgetUVar($Users,'Id'); ?>"><? if (getNumVar($bip,0)) putGS('Update'); else putGS('Set'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
        		<A HREF="X_ROOT/users/passwd.php?User=<? pgetUVar($Users,'Id'); ?>"><? putGS('Password'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<? if (getVar($Users,'Reader') == "Y") putGS('Yes'); else putGS('No'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/users/info.php?User=<? pgetUVar($Users,'Id'); ?>"><? putGS('Change'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<? 
    if (getVar($Users,'Reader') == "Y") {
		if ($msa != 0) { ?>
			<A HREF="X_ROOT/users/subscriptions/?User=<? pgetUVar($Users,'Id'); ?>"><? putGS('Subscriptions'); ?></A>
		<? } else { ?>
			&nbsp;
		<? }
    } else { ?>
			<A HREF="X_ROOT/users/access.php?User=<? pgetUVar($Users,'Id'); ?>"><? putGS('Rights'); ?></A>
<? } ?>dnl
		E_LIST_ITEM
	<? } else { ?>
		B_LIST_ITEM(<*CENTER*>)
			<? if (getVar($Users,'Reader') == "Y") putGS('Yes'); else putGS('No'); ?>
                E_LIST_ITEM  
	<? }
	
	if ($dua != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete user $1',getHVar($Users,'Name')); ?>*>, <*icon/x.gif*>, <*users/del.php?User=<? pgetVar($Users,'Id'); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<?
    $i--;
    }
    }
?>dnl
	B_LIST_FOOTER
<? if ($UserOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?sUname=<? pencURL($sUname); ?>&sType=<? pencURL($sType); ?>&UserOffs=<? p($UserOffs - $lpp); ?>*>)
<? }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?sUname=<? pencURL($sUname); ?>&sType=<? pencURL($sType); ?>&UserOffs=<? p($UserOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

