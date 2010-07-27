<?php

if (!isset($editUser) || gettype($editUser) != 'object' || $editUser->getUserName() == '') {
	camp_html_display_error(getGS('No such user account.'),$_SERVER['REQUEST_URI']);
	exit;
}

?>
<table border="0" cellspacing="1" cellpadding="0" width="100%" >
<tr class="table_list_header">
	<td colspan="3">
		<table border="0" cellspacing="0" cellpadding="3" width="100%">
		<tr class="table_list_header">
			<td align="left" style="padding-left: 3px; padding-top: 5px; padding-bottom: 5px; ">
				<?php putGS('User IP access list management'); ?>
			</td>
			<td align="right" nowrap>
				<a href="javascript: void(0);" onclick="ToggleRowVisibility('add_ip_row_id');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" id="my_icon" border="0" align="center"></a>
				<a href="javascript: void(0);" onclick="ToggleRowVisibility('add_ip_row_id');">
					<?php putGS("Add new"); ?>
				</a>
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php

$ipAccessList = IPAccess::GetUserIPAccessList($editUser->getUserId());
if (sizeof($ipAccessList) > 0) {
	$color= 0;
?>
	<tr class="table_list_header">
		<td align="left" valign="top" style="padding-left: 3px; padding-top: 3px; padding-bottom: 3px; "><B><?php putGS("Start IP"); ?></b></td>
		<td align="left" valign="top" style="padding-left: 3px;"><b><?php putGS("Number of addresses"); ?></b></td>
		<td align="left" valign="top" width="1%" style="padding-left: 3px;"><b><?php putGS("Delete"); ?></b></td>
	</tr>
<?php
	foreach ($ipAccessList as $i=>$ipAccess) {
		$startIP = $ipAccess->getStartIPstring();
		$addresses = $ipAccess->getAddresses();
		?>	<tr style="padding-left: 3px;" <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<td style="padding-left: 3px; padding-top: 3px; padding-bottom: 3px; "><?php echo $startIP; ?></td>
		<td style="padding-left: 3px;"><?php p(htmlspecialchars($addresses)); ?></td>
		<td align="center" style="padding-left: 3px;">
			<a href="/<?php echo $ADMIN; ?>/users/do_ipdel.php?User=<?php echo $editUser->getUserId() . '&' . SecurityToken::URLParameter(); ?>&StartIP=<?php  p($startIP); ?>"  onclick="return confirm('<?php putGS('Are you sure you want to delete the IP Group $1:$2?', $startIP, htmlspecialchars($addresses)); ?>');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" border="0" ALT="<?php putGS('Delete'); ?>" title="<?php putGS('Delete'); ?>"></a>
		</td>
	</tr>
<?php
	}
} else {
?><tr class="list_row_odd"><td colspan="3" style="padding-left: 3px;"><?php  putGS('No records.'); ?></td></tr>
<?php } ?>
<tr id="add_ip_row_id" style="display: none;">
	<td colspan="3" align="center" style="padding-top: 3px;">
		<form name="dialog" method="POST" action="do_ipadd.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
		<?php echo SecurityToken::FormParameter(); ?>
		<input type="hidden" name="User" value="<?php echo $editUser->getUserId(); ?>">
		<table border="0" cellspacing="0" cellpadding="0" class="box_table">
			<tr>
				<td align="right" width="1%" nowrap><?php  putGS("Start IP"); ?>:</td>
				<td nowrap>
				<input type="text" class="input_text" name="cStartIP1" size="3" maxlength="3" alt="number|0|0|255" emsg="<?php putGS("You must input a number between 0 and 255 into the Start IP address' $1 field.", 'first'); ?>">.
				<input type="text" class="input_text" name="cStartIP2" size="3" maxlength="3" alt="number|0|0|255" emsg="<?php putGS("You must input a number between 0 and 255 into the Start IP address' $1 field.", 'second'); ?>">.
				<input type="text" class="input_text" name="cStartIP3" size="3" maxlength="3" alt="number|0|0|255" emsg="<?php putGS("You must input a number between 0 and 255 into the Start IP address' $1 field.", 'third'); ?>">.
				<input type="text" class="input_text" name="cStartIP4" size="3" maxlength="3" alt="number|0|0|255" emsg="<?php putGS("You must input a number between 0 and 255 into the Start IP address' $1 field.", 'forth'); ?>">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php  putGS("Number of addresses"); ?>:</TD>
				<td><input type="text" class="input_text" name="cAddresses" size="10" maxlength="10" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "Number of addresses"); ?>"></td>
			</tr>
			<tr>
				<td colspan="2" nowrap>
				<div align="center">
				<input type="submit" class="button" name="Save" value="<?php putGS('Add new'); ?>">
				<input type="submit" class="button" name="cancel" value="<?php putGS('Cancel'); ?>" onclick="ToggleRowVisibility('add_ip_row_id');;">
				</div>
				</td>
			</tr>
		</table>
        </form>
	</td>
</tr>
</table>
<br>
