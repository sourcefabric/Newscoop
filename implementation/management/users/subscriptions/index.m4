B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Subscriptions*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT * FROM Subscriptions WHERE 1=0", 'q_subs');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todefnum('User'); ?>dnl
B_HEADER(<*Subscriptions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	fetchRow($q_usr);
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<?php  pgetHVar($q_usr,'UName'); ?>*>)
E_CURRENT

<P>X_NEW_BUTTON(<*Add new subscription*>, <*add.php?User=<?php  p($User); ?>*>)
<P><?php 
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
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_subs);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
<?php 
    query ("SELECT Name FROM Publications WHERE Id=".getSVar($q_subs,'IdPublication'), 'q_pub');
    fetchRow($q_pub);
?>dnl
			<A HREF="X_ROOT/users/subscriptions/sections/?Subs=<?php  pgetUVar($q_subs,'Id'); ?>&Pub=<?php  pgetUVar($q_subs,'IdPublication'); ?>&User=<?php  p($User); ?>"><?php  pgetHVar($q_pub,'Name'); ?></A>dnl
&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/users/subscriptions/topay.php?User=<?php  p($User); ?>&Subs=<?php  pgetUVar($q_subs,'Id'); ?>">dnl
			<?php  pgetHVar($q_subs,'ToPay').' '.pgetHVar($q_subs,'Currency'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  
			$sType = getHVar($q_subs,'Type');
			if ($sType == 'T')
				putGS("Trial subscription");
			else
				putGS("Paid subscription");
			?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/users/subscriptions/status.php?User=<?php  p($User); ?>&Subs=<?php  pgetUVar($q_subs,'Id'); ?>">dnl
<?php  if (getVar($q_subs,'Active') == "Y") { ?>Yes<?php  } else { ?>No<?php  } ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete subscriptions to $1',getHVar($q_pub,'Name') ); ?>*>, <*icon/delete.png*>, <*users/subscriptions/del.php?User=<?php  p($User); ?>&Subs=<?php  pgetUVar($q_subs,'Id'); ?>*>)
		E_LIST_ITEM
	E_LIST_TR
<?php 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?php  if ($SubsOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?User=<?php  p($User); ?>&SubsOffs=<?php  p($SubsOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?User=<?php  p($User); ?>&SubsOffs=<?php  p($SubsOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No subscriptions.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

