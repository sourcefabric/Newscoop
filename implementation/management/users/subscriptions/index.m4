B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Subscriptions*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT * FROM Subscriptions WHERE 1=0", 'q_subs');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todefnum('User'); ?>dnl
B_HEADER(<*Subscriptions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	fetchRow($q_usr);
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($q_usr,'UName'); ?></B>*>)
E_CURRENT

<P>X_NEW_BUTTON(<*Add new subscription*>, <*add.php?User=<? p($User); ?>*>)
<P><?
    todefnum('SubsOffs');
    if ($SubsOffs < 0) $SubsOffs= 0;
    todefnum('lpp', 20);
    
    query ("SELECT * FROM Subscriptions WHERE IdUser=$User ORDER BY Id DESC LIMIT $SubsOffs, ".($lpp+1), 'q_subs');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color=0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Publication<BR><SMALL>(click to see sections)</SMALL>*>)
		X_LIST_TH(<*Left to pay*>)
		X_LIST_TH(<*Type*>)
		X_LIST_TH(<*Active*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_subs);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
<?
    query ("SELECT Name FROM Publications WHERE Id=".getSVar($q_subs,'IdPublication'), 'q_pub');
    fetchRow($q_pub);
?>dnl
			<A HREF="X_ROOT/users/subscriptions/sections/?Subs=<? pgetUVar($q_subs,'Id'); ?>&Pub=<? pgetUVar($q_subs,'IdPublication'); ?>&User=<? p($User); ?>"><? pgetHVar($q_pub,'Name'); ?></A>dnl
&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/users/subscriptions/topay.php?User=<? p($User); ?>&Subs=<? pgetUVar($q_subs,'Id'); ?>">dnl
			<? pgetHVar($q_subs,'ToPay').' '.pgetHVar($q_subs,'Currency'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? 
			$sType = getHVar($q_subs,'Type');
			if ($sType == 'T')
				putGS("Trial subscription");
			else
				putGS("Paid subscription");
			?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/users/subscriptions/status.php?User=<? p($User); ?>&Subs=<? pgetUVar($q_subs,'Id'); ?>">dnl
<? if (getVar($q_subs,'Active') == "Y") { ?>Yes<? } else { ?>No<? } ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete subscriptions to $1',getHVar($q_pub,'Name') ); ?>*>, <*icon/x.gif*>, <*users/subscriptions/del.php?User=<? p($User); ?>&Subs=<? pgetUVar($q_subs,'Id'); ?>*>)
		E_LIST_ITEM
	E_LIST_TR
<?
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<? if ($SubsOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?User=<? p($User); ?>&SubsOffs=<? p($SubsOffs - $lpp); ?>*>)
<? } ?>dnl
<? if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?User=<? p($User); ?>&SubsOffs=<? p($SubsOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No subscriptions.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

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

