<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/user_types/utypes_common.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/permission_list.php");
camp_load_translation_strings("users");

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$rights = camp_get_permission_list();

$crumbs = array();
$crumbs[] = array(getGS("Users"), "");
$crumbs[] = array(getGS("User types"), "/$ADMIN/user_types");
$crumbs[] = array(getGS("Add new user type"), "");
echo camp_html_breadcrumbs($crumbs);

camp_html_display_msgs("0.25em", "0.25em");
?>

<form name="user_type_add" method="post" action="do_add.php">
<p>
<table border="0" cellspacing="0" cellpadding="1" class="table_input">
<tr>
	<td colspan="2" style="padding-top: 5px; padding-left: 10px;">
		<b><?php  putGS("Add new user type"); ?></b>
	</td>
</tr>
<tr>
	<td colspan="2">
		<hr noshade size="1" color="black">
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-top: 5px; padding-bottom: 10px;" align="center">
		<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
<tr>
	<td colspan="2" align="left" style="padding-left: 10px;">
		<?php putGS('Name'); ?>:
		<input type="text" class="input_text" name="Name" size="32">
	</td>
</tr>
<?php
foreach ($rights as $group_name=>$group) {
?>
<tr>
	<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
		--- <?php putGS($group_name); ?> ---
	</td>
</tr>
<?php
foreach ($group as $right_name=>$right_text) {
?>
<tr>
	<td align="right" style="padding-left: 10px;">
		<input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox" />
	</td>
	<td style="padding-right: 10px;">
		<?php putGS($right_text); ?>
	</td>
</tr>
<?php
}
}
?>
<tr>
	<td colspan="2" style="padding-top: 5px; padding-bottom: 10px;" align="center">
		<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
</table>
</form>
<script>
document.user_type_add.Name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
