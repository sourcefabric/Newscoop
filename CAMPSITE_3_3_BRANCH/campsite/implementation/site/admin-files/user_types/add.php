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
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite-checkbox.js"></script>
<form name="user_type_add" method="post" action="do_add.php">
<p>
<?php
$checkboxPrefix = 'checkbox_';
$no_rights = 0;
foreach ($rights as $group_name => $group) {
    $no_rights += sizeof($group);
}
?>
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
<tr>
        <td colspan="2" align="center" style="padding-top:10px; padding-bottom:10px;">
                <input type="button" class="button" value="Select All" onclick="checkAll(<?php echo $no_rights; ?>);" />
                <input type="button" class="button" value="Select None" onclick="uncheckAll(<?php echo $no_rights; ?>);" />
        </td>
</tr>
<?php
$i = 0;
foreach ($rights as $group_name=>$group) {
?>
<tr>
	<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
		<strong>--- <?php putGS($group_name); ?> ---</strong>
	</td>
</tr>
<?php
$color = 0;
foreach ($group as $right_name=>$right_text) {
        $rowClass = ($color) ? 'list_row_even' : 'list_row_odd';
        $color = !$color;
?>
<tr id="<?php echo 'row_' . $i; ?>" class="<?php echo $rowClass; ?>" onmouseover="setPointer(this, <?php echo $i; ?>, 'over');" onmouseout="setPointer(this, <?php echo $i; ?>, 'out');">
        <script>default_class[<?php echo $i; ?>] = "<?php echo $rowClass; ?>";</script>
	<td align="right" style="padding-left: 10px;">
                <input type="checkbox" value="<?php echo $no_rights . '_' . ($i + 1); ?>" name="<?php echo $right_name; ?>" id="<?php echo 'checkbox_' . $i; ?>" class="input_checkbox" onclick="checkboxClick(this, <?php echo $i++; ?>);" />
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
