B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Subscribed sections*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT * FROM SubsSections WHERE 1=0", 'q_ssect');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<?
    todefnum('Pub');
    todefnum('User');
    todefnum('Subs');
?>dnl
B_HEADER(<*Subscribed sections*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
	query("SELECT  Id as IdLang FROM Languages WHERE code='$TOL_Language'", 'q_lang');
	if($NUM_ROWS == 0){
		query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=$Pub", 'q_lang');
	}
	fetchRow($q_lang);
	$IdLang = getVar($q_lang,'IdLang');

    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    fetchRow($q_usr);
	    fetchRow($q_pub);
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($q_usr,'UName'); ?></B>*>)
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P><table><tr><td valign=top>X_NEW_BUTTON(<*Add new section to subscription*>, <*add.php?Subs=<? p($Subs); ?>&Pub=<? p($Pub); ?>&User=<? p($User); ?>*>)</td>

<P><?
    todefnum('SSectOffs');
    if ($SSectOffs < 0) $SSectOffs= 0;
    $lpp=20;
    query ("SELECT DISTINCT Sub.*, Sec.Name, Scr.Type FROM SubsSections as Sub, Sections as Sec, Subscriptions as Scr WHERE Sub.IdSubscription=$Subs AND Scr.Id = $Subs AND Scr.IdPublication = Sec.IdPublication AND Sub.SectionNumber = Sec.Number ORDER BY SectionNumber LIMIT $SSectOffs, ".($lpp+1), 'q_ssect');
    if ($NUM_ROWS) {
?>
<td valign=top>X_NEW_BUTTON(<*Change all sections*>, <*change.php?Subs=<? p($Subs); ?>&Pub=<? p($Pub); ?>&User=<? p($User); ?>*>)</td></tr></table>
<?
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
	fetchRow($q_ssect);
	$isPaid = 0;
	$sType = getHVar($q_ssect, 'Type');
	if ($sType == 'P')
	    $isPaid = 1;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Section*>)
		X_LIST_TH(<*Start Date<BR><SMALL>(yyyy-mm-dd)</SMALL>*>)
		X_LIST_TH(<*Days*>)
	<? if ($isPaid) { ?>
		X_LIST_TH(<*Paid Days*>)
	<? } ?>
		X_LIST_TH(<*Change*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? p(getHVar($q_ssect,'Name')); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($q_ssect,'StartDate'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($q_ssect,'Days'); ?>
		E_LIST_ITEM
	<? if ($isPaid) { ?>
		B_LIST_ITEM
			<? pgetHVar($q_ssect,'PaidDays'); ?>
		E_LIST_ITEM
	<? } ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/users/subscriptions/sections/change.php?User=<? p($User); ?>&Pub=<? p($Pub); ?>&Subs=<? p($Subs); ?>&Sect=<? pgetUVar($q_ssect,'SectionNumber'); ?>"><? putGS('Change'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete subscription to section $1?',getHVar($q_ssect,'SectionNumber')); ?>*>, <*icon/x.gif*>, <*users/subscriptions/sections/del.php?User=<? p($User); ?>&Pub=<? p($Pub); ?>&Subs=<? p($Subs); ?>&Sect=<? pgetUVar($q_ssect,'SectionNumber'); ?>*>)
		E_LIST_ITEM
	E_LIST_TR
<?
	$i--;
	if ($i)
		fetchRow($q_ssect);
    }
}
?>dnl
	B_LIST_FOOTER
<? if ($SSectOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Subs=<? p($Subs); ?>&Pub=<? p($Pub); ?>&User=<? p($User); ?>&SSectOffs=<? p($SSectOffs - $lpp); ?>*>)
<? }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Subs=<? p($Subs); ?>&Pub=<? p($Pub); ?>&User=<? p($User); ?>&SSectOffs=<? p($SSectOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
</tr></table>
<BLOCKQUOTE>
	<LI><? putGS('No sections in the current subscription.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
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

