B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding subscription*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add subscriptions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('User');
    todefnum('cPub');
    todef('cActive');
    todef('bAddSect');
    todef('cStartDate');
    todefnum('cDays');
    todefnum('Subs');
    todef('sType');
    if ($cActive === "on")
	$cActive= "Y";
    else
	$cActive= "N";
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
    	fetchRow($q_usr);
    	$UName = getHVar($q_usr,'UName');
?>dnl
B_HEADER(<*Adding subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*User account*>, <*users/edit.php?User=<?php echo $User; ?>&uType=Subscribers*>, <**>, <*'$UName'*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	fetchRow($q_usr);
?>dnl

<P>
B_MSGBOX(<*Adding subscription*>)
<?php 
	$paidDays = 0;
	if ($sType == "PN" || $sType == "T")
		$paidDays = $cDays;
	$subsType = 'T';
	if ($sType != "T")
		$subsType = 'P';
	query ("INSERT IGNORE INTO Subscriptions SET IdUser=$User, IdPublication=$cPub, Active='$cActive', Type='$subsType'");
	$success_subs = 1;
        if ($AFFECTED_ROWS > 0){
        	query ("SELECT LAST_INSERT_ID()", 'lid');
		fetchRowNum($lid);
		$Subs = getNumVar($lid,0);
        }
        else $success_subs= 0;

	if ($success_subs) { ?>dnl
		X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription has been added successfully.'); ?></LI>*>)
	<?php  }
	else { ?>dnl
		X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription could not be added.'); ?></LI><LI><?php  putGS("Please check if there isn't another subscription to the same publication."); ?></LI>*>)
	<?php  }

	if($success_subs && ($bAddSect == 'Y')){
		query ("SELECT DISTINCT Number FROM Sections where IdPublication=$cPub", 'q_sect');
		$nr=$NUM_ROWS;
		$success_sect = 1;
		for($loop=0;$loop<$nr;$loop++) {
			fetchRowNum($q_sect);
			$tval=encS(getNumVar($q_sect,0));
			query ("INSERT IGNORE INTO SubsSections SET IdSubscription=$Subs, SectionNumber='$tval', StartDate='$cStartDate', Days='$cDays', PaidDays='$paidDays'");
			if ($AFFECTED_ROWS == 0)  $success_sect= 0;
		}
		if ($success_sect) { ?>dnl
			X_MSGBOX_TEXT(<*<LI><?php  putGS('The sections were added successfully.'); ?></LI>*>)
		<?php  }
		else { ?>dnl
			X_MSGBOX_TEXT(<*<LI><?php  putGS('The sections could not be added successfully. Some of them were already added !'); ?></LI>*>)
		<?php  }
	} ?>dnl


	B_MSGBOX_BUTTONS
	<?php  if ($success_sect) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/subscriptions/?User=<?php  p($User); ?>*>)
	<?php  } else {
		if($success_subs) {?>dnl
			REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/subscriptions/sections/add.php?Pub=<?php  p($cPub); ?>&User=<?php  p($User); ?>&Subs=<?php  p($Subs); ?>*>)
		<?php  }
		else { ?>
			REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/subscriptions/add.php?User=<?php  p($User); ?>*>)
		<?php  }
	} ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } ?>dnl
<?php  } ?>dnl
X_COPYRIGHT
E_BODY

E_DATABASE
E_HTML
