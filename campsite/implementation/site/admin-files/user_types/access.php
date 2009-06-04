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

$uTypeId = Input::Get('UType', 'string', '');
if (is_numeric($uTypeId) && $uTypeId > 0) {
	$userType = new UserType($uTypeId);
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

camp_html_display_msgs("0.25em", "0.25em");
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite-checkbox.js"></script>
<table border="0" cellspacing="1" cellpadding="1" width="100%" class="current_location_table">
	<tr>
		<td align="right" width="1%" nowrap valign="top" class="current_location_title">
			&nbsp;<?php putGS("User type"); ?>:
		</td>
		<td valign="top" class="current_location_content">
			<?php echo $userType->getName(); ?>
		</td>
	</tr>
</table>
<?php
$rightsList = array();
$checkboxPrefix = 'checkbox_';
$counter = 0;
$no_rights = 0;
foreach ($rights as $group_name => $group) {
    $no_rights += sizeof($group);
    foreach ($group as $right_name => $right_text) {
       if ($userType->hasPermission($right_name)) {
           $rightsList[] = "'" . $checkboxPrefix . $counter . "'";
       }
       $counter++;
    }
}
$jsRightsArray = implode(',', $rightsList);
?>
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
<tr>
	<td colspan="2">
		<hr noshade size="1" color="black">
	</td>
</tr>
<tr>
 	<td colspan="2" style="padding-top: 5px; padding-bottom: 0px;" align="center">
            <input type="button" class="button" value="Select All" onclick="checkAll(<?php echo $no_rights; ?>);" />
            <input type="button" class="button" value="Select None" onclick="uncheckAll(<?php echo $no_rights; ?>);" />
            <input type="button" class="button" value="Restore" onclick="var rightsArray = [<?php echo $jsRightsArray; ?>];checkRestore(<?php echo $no_rights; ?>, rightsArray);" />
	</td>
</tr>
<?php
$i = 0;
foreach ($rights as $group_name=>$group) {
?>
<tr>
	<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
		<strong>--- <?php p($group_name); ?> ---</strong>
	</td>
</tr>

<?php
$color = 0;
foreach ($group as $right_name=>$right_text) {
        $rowClass = ($color) ? 'list_row_even' : 'list_row_odd';
        $color = !$color;
?>
<tr id="<?php echo 'row_' . $i; ?>" class="<?php if ($userType->hasPermission($right_name)) { echo 'list_row_click'; } else { echo $rowClass; } ?>" onmouseover="setPointer(this, <?php echo $i; ?>, 'over');" onmouseout="setPointer(this, <?php echo $i; ?>, 'out');">
        <script>default_class[<?php echo $i; ?>] = "<?php echo $rowClass; ?>";</script>
	<td align="right" style="padding-left: 10px;">
                <input type="checkbox" value="<?php echo $no_rights . '_' . ($i + 1); ?>" name="<?php echo $right_name; ?>" id="<?php echo 'checkbox_' . $i; ?>" class="input_checkbox" onclick="checkboxClick(this, <?php echo $i++; ?>);" <?php  if ($userType->hasPermission($right_name)) { p("CHECKED"); } ?> />
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
	<td colspan="2" style="padding-top: 5px; padding-bottom: 10px;" align="center">
	<input type="hidden" name="UType" value="<?php p($uTypeId); ?>">
	<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
</table>
</form>
<?php camp_html_copyright_notice(); ?>
