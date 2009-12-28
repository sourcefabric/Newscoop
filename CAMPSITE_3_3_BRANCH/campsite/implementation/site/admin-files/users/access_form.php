<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");
require_once('permission_list.php');

$uType = 'Staff';
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canManage) {
	$error = getGS("You do not have the right to change user account permissions.");
	camp_html_display_error($error);
	exit;
}

$userId = Input::Get('User', 'int', 0);
if ($userId > 0) {
	$editUser = new User($userId);
	if ($editUser->getUserName() == '') {
		camp_html_display_error(getGS('No such user account.'));
		exit;
	}
} else {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}

$rights = camp_get_permission_list();
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite-checkbox.js"></script>
<table border="0" cellspacing="0" cellpadding="3" align="left">
<?php
$rightsList = array();
$checkboxPrefix = 'checkbox_';
$counter = 0;
$no_rights = 0;
foreach ($rights as $group_name => $group) {
    $no_rights += sizeof($group);
    foreach ($group as $right_name => $right_text) {
        if ($editUser->hasPermission($right_name)) {
            $rightsList[] = "'" . $checkboxPrefix . $counter . "'";
        }
        $counter++;
    }
}
$jsRightsArray = implode(',', $rightsList);
?>
<tr>
  <td colspan="2" align="center">
    <input type="button" class="button" value="Select All" onclick="checkAll(<?php echo $no_rights; ?>);" />
    <input type="button" class="button" value="Select None" onclick="uncheckAll(<?php echo $no_rights; ?>);" />
    <input type="button" class="button" value="Restore" onclick="var rightsArray = [<?php echo $jsRightsArray; ?>];checkRestore(<?php echo $no_rights; ?>, rightsArray);" />
  </td>
</tr>
<?php
$i = 0;
foreach ($rights as $group_name => $group) {
?>
	<tr>
		<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
			<strong>--- <?php p($group_name); ?> ---</strong>
		</td>
	</tr>
<?php
        $color = 0;
	foreach ($group as $right_name => $right_text) {
            $rowClass = ($color) ? 'list_row_even' : 'list_row_odd';
            $color = !$color;
?>
	<tr id="<?php echo 'row_' . $i; ?>" class="<?php if ($editUser->hasPermission($right_name)) { echo 'list_row_click'; } else { echo $rowClass; } ?>" onmouseover="setPointer(this, <?php echo $i; ?>, 'over');" onmouseout="setPointer(this, <?php echo $i; ?>, 'out');">
                <script>default_class[<?php echo $i; ?>] = "<?php echo $rowClass; ?>";</script>
		<td align="right" style="padding-left: 10px;">
                        <input type="checkbox" value="<?php echo $no_rights . '_' . ($i + 1); ?>" name="<?php echo $right_name; ?>" id="<?php echo 'checkbox_' . $i; ?>" class="input_checkbox" onclick="checkboxClick(this, <?php echo $i++; ?>);" <?php if ($editUser->hasPermission($right_name)) { p("CHECKED"); } ?>>
		</td>
		<td style="padding-right: 10px;">
			<?php p($right_text); ?>
		</td>
	</tr>
<?php
	}
}
?>
</table>
