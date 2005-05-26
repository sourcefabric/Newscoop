<?php

check_basic_access($_REQUEST);
if (!isset($editUser) || gettype($editUser) != 'object' || $editUser->getUserName() == '') {
	CampsiteInterface::DisplayError('No such user account.',$_SERVER['REQUEST_URI']);
	exit;
}

?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<tr class="table_list_header">
		<td colspan="3" align="left">
			 <?php putGS('User IP access list management'); ?>
		</td>
	</tr>
	<tr class="table_list_header">
		<td align="right" colspan="3" align="right">
			<a href="ipadd.php?User=<?php echo $editUser->getId(); ?>">
			<img src="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></a>&nbsp;
			<a href="ipadd.php?User=<?php echo $editUser->getId(); ?>" ><B><?php putGS("Add new IP address group"); ?></b></a>
		</td>
	</tr>
<?php

query ("SELECT Name FROM Users WHERE Id=" . $editUser->getId(), 'users');
if ($NUM_ROWS)
	fetchRow($users);

$sql = "SELECT (StartIP & 0xff000000) >> 24 as ip0, (StartIP & 0x00ff0000) >> 16 as ip1, (StartIP & 0x0000ff00) >> 8 as ip2, StartIP & 0x000000ff as ip3, StartIP, Addresses FROM SubsByIP WHERE IdUser = ".$editUser->getId();
query($sql, 'IPs');
if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$color= 0;
?>
	<tr class="table_list_header">
		<td align="left" valign="top"><B><?php putGS("Start IP"); ?></b></td>
		<td align="left" valign="top"><b><?php putGS("Number of addresses"); ?></b></td>
		<td align="left" valign="top" width="1%"><b><?php putGS("Delete"); ?></b></td>
	</tr>
<?php
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($IPs);
		$ip = getVar($IPs, 'ip0') . '.' . getVar($IPs , 'ip1') . '.'
			. getVar($IPs, 'ip2') . '.' . getVar($IPs, 'ip3');
		?>	<tr <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<td><?php echo $ip; ?></td>
		<td><?php pgetHVar($IPs,'Addresses'); ?></td>
		<td align="center">
			<a href="/<?php echo $ADMIN; ?>/users/do_ipdel.php?User=<?php echo $editUser->getId(); ?>&StartIP=<?php  pgetVar($IPs,'StartIP'); ?>"  onclick="return confirm('<?php putGS('Are you sure you want to delete the IP Group $1?', $ip); ?>');">
			<img src="/<?php echo $ADMIN; ?>/img/icon/delete.png" border="0" ALT="<?php putGS('Delete'); ?>" title="<?php putGS('Delete'); ?>"></a>
		</td>
	</tr>
<?php  
	}
} else {
?><tr class="list_row_odd"><td colspan="3"><?php  putGS('No records.'); ?></td></tr>
<?php } ?>
</table>
<br>
