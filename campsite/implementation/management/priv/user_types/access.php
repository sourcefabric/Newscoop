<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/permission_list.php");

list($access, $User) = check_basic_access($_REQUEST);
$canManage = $User->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$uType = Input::Get('UType', 'string', '');
if ($uType != '') {
	$userType =& new UserType($uType);
	if ($userType->getName() == '') {
		camp_html_display_error(getGS('No such user type.'));
		exit;
	}
} else {
	camp_html_display_error(getGS('No such user type.'));
	exit;
}

$rights = camp_get_permission_list();

$crumbs = array();
$crumbs[] = array(getGS("Staff User Types"), "/$ADMIN/user_types/");
$crumbs[] = array(getGS("Change user type permissions"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<table border="0" cellspacing="1" cellpadding="1" width="100%" class="current_location_table">
	<tr>
		<td align="right" width="1%" nowrap valign="top" class="current_location_title">
			&nbsp;<?php putGS("User type"); ?>:
		</td>
		<td valign="top" class="current_location_content">
			<?php echo $uType; ?>
		</td>
	</tr>
</table>

<form name="dialog" method="post" action="do_access.php" >
<p>
<table border="0" cellspacing="0" cellpadding="1" class="table_input">
<tr>
	<td colspan="2" style="padding-top: 5px; padding-left: 10px;">
		<b><?php  putGS("Change user type permissions"); ?></b>
	</td>
</tr>
<tr>
	<td colspan="2">
		<hr noshade size="1" color="black">
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-top: 5px; padding-bottom: 0px;" align="center">
	<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
<?php
foreach ($rights as $group_name=>$group) {
?>
<tr>
	<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
		--- <?php p($group_name); ?> ---
	</td>
</tr>

<?php
foreach ($group as $right_name=>$right_text) {
?>
<tr>
	<td align="right" style="padding-left: 10px;">
		<input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox" <?php  if ($userType->hasPermission($right_name)) { p("CHECKED"); } ?>>
	</td>
	<td style="padding-right: 10px;">
		<?php p($right_text); ?>
	</td>
</tr>
<?php
}
}
?>
<tr>
	<td>
	<?php
	if (function_exists ("incModFile"))
		incModFile ($User);
	?>
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-top: 5px; padding-bottom: 10px;" align="center">
	<input type="hidden" name="UType" value="<?php p(htmlspecialchars($uType)); ?>">
	<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
</table></p>
</form>
<?php camp_html_copyright_notice(); ?>
