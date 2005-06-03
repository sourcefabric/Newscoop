B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*User management*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  query ("SELECT * FROM Users WHERE 1=0", 'Users');

} ?>dnl
E_HEAD

<?php  if ($access) { 
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

<?php 
    todef('sUname', '');
    if (!isset($sUname))
    	$sUname = '';
    todef('sType', '');
    if (!isset($sType))
    	$sType = '';
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<?php  if ($mua != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new user account*>, <*add.php?Back=<?php  pencURL($REQUEST_URI); ?>*>)</TD>
	<?php  } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><?php  putGS('User name'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" NAME="sUname" VALUE="<?php  pencHTML($sUname); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><SELECT NAME="sType"><OPTION><OPTION VALUE="Y" <?php  if ($sType == "Y") { ?>SELECTED<?php  } ?>><?php  putGS('Reader'); ?><OPTION VALUE="N" <?php  if ($sType == "N") { ?>SELECTED<?php  } ?>><?php  putGS('Staff'); ?></SELECT></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<P><?php 
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
	<?php  if ($mua != 0) { ?>
		X_LIST_TH(<*IP Access*>, <*1%*>, <*nowrap*>)
		X_LIST_TH(<*Password*>, <*1%*>)
		X_LIST_TH(<*Reader*>, <*1%*>)
		X_LIST_TH(<*Info*>, <*1%*>)
		X_LIST_TH(<*Rights*>, <*1%*>)
	<?php  } else { ?>
		X_LIST_TH(<*Reader*>, <*1%*>)
	<?php  }
	    if ($dua != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($Users);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<?php  pgetHVar($Users,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($Users,'UName'); ?>&nbsp;
		E_LIST_ITEM
	<?php  if ($mua != 0) {
		query ("SELECT COUNT(*) FROM SubsByIP WHERE IdUser=".getSVar($Users,'Id'), 'bip');
		fetchRowNum($bip);
		?>
		B_LIST_ITEM(<*CENTER*>)
                        <A HREF="X_ROOT/users/ipaccesslist.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  if (getNumVar($bip,0)) putGS('Update'); else putGS('Set'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
        		<A HREF="X_ROOT/users/passwd.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Password'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<?php  if (getVar($Users,'Reader') == "Y") putGS('Yes'); else putGS('No'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/users/info.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Change'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<?php  
    if (getVar($Users,'Reader') == "Y") {
		if ($msa != 0) { ?>
			<A HREF="X_ROOT/users/subscriptions/?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Subscriptions'); ?></A>
		<?php  } else { ?>
			&nbsp;
		<?php  }
    } else { ?>
			<A HREF="X_ROOT/users/access.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Rights'); ?></A>
<?php  } ?>dnl
		E_LIST_ITEM
	<?php  } else { ?>
		B_LIST_ITEM(<*CENTER*>)
			<?php  if (getVar($Users,'Reader') == "Y") putGS('Yes'); else putGS('No'); ?>
                E_LIST_ITEM  
	<?php  }
	
	if ($dua != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete user $1',getHVar($Users,'Name')); ?>*>, <*icon/x.gif*>, <*users/del.php?User=<?php  pgetVar($Users,'Id'); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
	E_LIST_TR
<?php 
    $i--;
    }
    }
?>dnl
	B_LIST_FOOTER
<?php  if ($UserOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?sUname=<?php  pencURL($sUname); ?>&sType=<?php  pencURL($sType); ?>&UserOffs=<?php  p($UserOffs - $lpp); ?>*>)
<?php  }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?sUname=<?php  pencURL($sUname); ?>&sType=<?php  pencURL($sType); ?>&UserOffs=<?php  p($UserOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

