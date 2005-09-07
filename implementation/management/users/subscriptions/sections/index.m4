B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Subscribed sections*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT * FROM SubsSections WHERE 1=0", 'q_ssect');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<?php 
    todefnum('Pub');
    todefnum('User');
    todefnum('Subs');
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
	    $UName = getHVar($q_usr,'UName');
?>dnl

B_HEADER(<*Subscribed sections*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*User account*>, <*users/edit.php?User=<?php echo $User; ?>&uType=Subscribers*>, <**>, <*'$UName'*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P><table><tr><td valign=top>X_NEW_BUTTON(<*Add new section to subscription*>, <*add.php?Subs=<?php  p($Subs); ?>&Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>*>)</td>

<P><?php 
    todefnum('SSectOffs');
    if ($SSectOffs < 0) $SSectOffs= 0;
    $lpp=20;
    query ("SELECT DISTINCT Sub.*, Sec.Name, Scr.Type FROM SubsSections as Sub, Sections as Sec, Subscriptions as Scr WHERE Sub.IdSubscription=$Subs AND Scr.Id = $Subs AND Scr.IdPublication = Sec.IdPublication AND Sub.SectionNumber = Sec.Number ORDER BY SectionNumber LIMIT $SSectOffs, ".($lpp+1), 'q_ssect');
    if ($NUM_ROWS) {
?>
<td valign="top">
	X_TOL_BUTTON(<*Change all sections*>, <*change.php?Subs=<?php  p($Subs); ?>&Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>*>)
</td>
</tr>
</table>
<?php 
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
	<?php  if ($isPaid) { ?>
		X_LIST_TH(<*Paid Days*>)
	<?php  } ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/users/subscriptions/sections/change.php?User=<?php  p($User); ?>&Pub=<?php  p($Pub); ?>&Subs=<?php  p($Subs); ?>&Sect=<?php pgetUVar($q_ssect,'SectionNumber'); ?>"><?php p(getHVar($q_ssect,'Name')); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($q_ssect,'StartDate'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($q_ssect,'Days'); ?>
		E_LIST_ITEM
	<?php  if ($isPaid) { ?>
		B_LIST_ITEM
			<?php  pgetHVar($q_ssect,'PaidDays'); ?>
		E_LIST_ITEM
	<?php  } ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete subscription to section $1?',getHVar($q_ssect,'SectionNumber')); ?>*>, <*icon/delete.png*>, <*users/subscriptions/sections/do_del.php?User=<?php  p($User); ?>&Pub=<?php  p($Pub); ?>&Subs=<?php  p($Subs); ?>&Sect=<?php  pgetUVar($q_ssect,'SectionNumber'); ?>*>, <*onclick="return confirm('<?php putGS('Are you sure you want to delete the subscription to the section $1?', getHVar($q_ssect,'Name')); ?>');"*>)
		E_LIST_ITEM
	E_LIST_TR
<?php 
	$i--;
	if ($i)
		fetchRow($q_ssect);
    }
}
?>dnl
	B_LIST_FOOTER
<?php  if ($SSectOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?Subs=<?php  p($Subs); ?>&Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>&SSectOffs=<?php  p($SSectOffs - $lpp); ?>*>)
<?php  }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?Subs=<?php  p($Subs); ?>&Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>&SSectOffs=<?php  p($SSectOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
</tr></table>
<BLOCKQUOTE>
	<LI><?php  putGS('No sections in the current subscription.'); ?></LI>
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

