B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*User types*>)
<? if ($access ==0) { ?>dnl
	X_LOGOUT
<? query ("SELECT * FROM UserTypes WHERE 1=0", 'UTypes'); 
 } ?>dnl
E_HEAD

<? if ($access) {
SET_ACCESS(<*muta*>, <*ManageUserTypes*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*User types*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? if ($muta != 0) { ?>
<P>X_NEW_BUTTON(<*Add new user type*>, <*add.php?Back=<? print encURL($REQUEST_URI); ?>*>)
<? } ?>

<P><? 
    todefnum('UTOffs');
    if ($UTOffs < 0) $UTOffs= 0;
    todefnum('lpp', 20);
    query ("SELECT * FROM UserTypes ORDER BY Name LIMIT $UTOffs, ".($lpp+1), 'UTypes');
    if ($NUM_ROWS) {
	$nr=$NUM_ROWS;
	$i=$lpp;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Type*>)
		X_LIST_TH(<*Reader*>, <*1%*>)
	<? if ($muta != 0) { ?> 
		X_LIST_TH(<*Access*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
	<?
	    for ($loop=0;$loop<$nr;$loop++) {
		fetchRow($UTypes);
		if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? pgetHVar($UTypes, 'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<?
			    if (getVar($UTypes,'Reader') == "Y")
				putGS("Yes");
			    else
				putGS("No");
			?>
		E_LIST_ITEM
	<? if ($muta !=0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="access.php?UType=<? pgetUVar($UTypes,'Name'); ?>"><? putGS('Change'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Delete user type <? pgetHVar($UTypes,'Name'); ?>*>, <*icon/x.gif*>, <*u_types/del.php?UType=<? pgetUVar($UTypes,'Name'); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<?
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<? if ($UTOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?UTOffs=<? print ($UTOffs - $lpp); ?>*>)
<? } 
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?UTOffs=<? print ($UTOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No user types.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

