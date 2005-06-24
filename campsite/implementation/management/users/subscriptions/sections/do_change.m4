B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Updating subscription*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change subscriptions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Subs');
    todefnum('Sect');
    todefnum('Pub');
    todef('cStartDate');
    todefnum('cDays');
    todefnum('cPaidDays');
    todefnum('User');
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
		fetchRow($q_usr);
	    $UName = getHVar($q_usr,'UName');
?>dnl
B_HEADER(<*Updating subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*users/subscriptions/sections/?User=<?php  p($User); ?>&Pub=<?php  p($Pub); ?>&Subs=<?php  p($Subs); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*User account*>, <*users/edit.php?User=<?php echo $User; ?>&uType=Subscribers*>, <**>, <*'$UName'*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Subscriptions WHERE Id = $Subs", 'q_sub');
	    if ($NUM_ROWS) {
		$sectCond = "";
		if ($Sect > 0)
		    $sectCond = "AND SectionNumber = ".$Sect;
		query ("SELECT * FROM SubsSections WHERE IdSubscription=$Subs $sectCond", 'q_ssub');
		if ($NUM_ROWS) {
		    fetchRow($q_pub);
		    fetchRow($q_sub);
		    fetchRow($q_ssub);
		    $isPaid = 0;
		    if (getHVar($q_sub, 'Type') == 'P')
			$isPaid = 1;
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing subscription*>)
<?php 
    if (!$isPaid)
	$cPaidDays = $cDays;
    query ("UPDATE SubsSections SET StartDate='$cStartDate', Days='$cDays', PaidDays='$cPaidDays' WHERE IdSubscription=$Subs $sectCond");
    if ($AFFECTED_ROWS >= 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription has been updated.'); ?></LI>*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription could not be updated.'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS >= 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/subscriptions/sections/?Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>&Subs=<?php  p($Subs); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/subscriptions/sections/change.php?Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>&Subs=<?php  p($Subs); ?>&Sect=<?php  p($Sect); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
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
