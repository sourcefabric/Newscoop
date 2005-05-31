<?php

check_basic_access($_REQUEST);
if (!isset($editUser) || gettype($editUser) != 'object' || $editUser->getUserName() == '') {
	CampsiteInterface::DisplayError('No such user account.',$_SERVER['REQUEST_URI']);
	exit;
}

?>
<table border="0" cellspacing="1" cellpadding="3" width="100%" class="table_list">
<tr class="table_list_header">
	<td colspan="5" align="left">
		<table width="100%"><tr class="table_list_header">
			<td align="left"><?php putGS("Subscriptions"); ?></td>
			<td align="right" nowrap>
				<?php $addURI = "/$ADMIN/users/subscriptions/add.php?User=".$editUser->getId(); ?>
				<A HREF="<?php echo $addURI; ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A>&nbsp;
				<A HREF="<?php echo $addURI; ?>"><B><?php putGS("Add new"); ?></B></A>
			</td>
		</tr></table>
	</td>
</tr>
<?php

query ("SELECT * FROM Subscriptions WHERE IdUser=" . $editUser->getId() . " ORDER BY Id DESC", 'q_subs');
if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$color=0;
	?>
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Publication"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Left to pay"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Type"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Active"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?php 
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_subs);
?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD >
<?php 
		query ("SELECT Name FROM Publications WHERE Id=".getSVar($q_subs,'IdPublication'), 'q_pub');
		fetchRow($q_pub);
?>			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/sections/?Subs=<?php pgetUVar($q_subs,'Id'); ?>&Pub=<?php pgetUVar($q_subs,'IdPublication'); ?>&User=<?php  echo $editUser->getId(); ?>"><?php pgetHVar($q_pub,'Name'); ?></A>&nbsp;
		</TD>
		<TD >
			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/topay.php?User=<?php echo $editUser->getId(); ?>&Subs=<?php pgetUVar($q_subs,'Id'); ?>">
			<?php  pgetHVar($q_subs,'ToPay').' '.pgetHVar($q_subs,'Currency'); ?></A>
		</TD>
		<TD >
			<?php  
			$sType = getHVar($q_subs,'Type');
			if ($sType == 'T')
				putGS("Trial");
			else
				putGS("Paid");
			?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/status.php?User=<?php echo $editUser->getId(); ?>&Subs=<?php pgetUVar($q_subs,'Id'); ?>"><?php if (getVar($q_subs,'Active') == "Y") { ?>Yes<?php  } else { ?>No<?php  } ?></A>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/del.php?User=<?php echo $editUser->getId(); ?>&Subs=<?php pgetUVar($q_subs,'Id'); ?>">
			<IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0" ALT="<?php putGS('Delete subscriptions to $1',getHVar($q_pub,'Name') ); ?>" TITLE="<?php  putGS('Delete subscriptions to $1',getHVar($q_pub,'Name') ); ?>"></A>
		</TD>
	</TR>
<?php 
}
?>
<?php  } else { ?>
<tr class="list_row_odd"><td colspan="5"><?php  putGS('No subscriptions.'); ?></td></tr>
<?php  } ?>
</TABLE>
<br>
