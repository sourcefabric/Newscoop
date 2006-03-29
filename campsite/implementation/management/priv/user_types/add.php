<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/permission_list.php");
camp_load_language("users");

list($access, $User) = check_basic_access($_REQUEST);
$canManage = $User->hasPermission('ManageUserTypes');
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

?>

<form name="dialog" method="post" action="do_add.php" >
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
		<input type="text" class="input_text" name="Name" size="32" maxlength="32">
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
		<input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox">
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
	<td>
<?php
if (function_exists ("incModFile"))
	incModFile($User);
?>
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-top: 5px; padding-bottom: 10px;" align="center">
		<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
</table>
</p>
</form>
<?php camp_html_copyright_notice(); ?>
