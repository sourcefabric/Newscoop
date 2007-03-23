<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$UTOffs = Input::Get('UTOffs', 'int', 0);
if ($UTOffs < 0) {
	$UTOffs= 0;
}
$lpp = Input::Get('lpp', 'int', 20);

$crumbs = array();
$crumbs[] = array(getGS("Users"), "");
$crumbs[] = array(getGS("User types"), "");
echo camp_html_breadcrumbs($crumbs);
?>

<?php if ($canManage) { ?>
<p>
<table border="0" cellspacing="0" cellpadding="1" class="action_buttons">
<tr>
	<td><a href="add.php?Back=<?php print urlencode($_SERVER['REQUEST_URI']); ?>" ><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0"></a></td>
	<td><a href="/<?php echo $ADMIN; ?>/user_types/add.php?Back=<?php print urlencode($_SERVER['REQUEST_URI']); ?>"><b><?php putGS("Add new user type"); ?></b></a></td>
</tr>
</table>
<?php  } ?>

<P>
<?php
$userTypes = UserType::GetUserTypes();

if (count($userTypes) > 0) {
	$color= 0;
?>
<table border="0" cellspacing="1" cellpadding="3" class="table_list">
<tr class="table_list_header">
	<td align="left" valign="top"><B><?php putGS("Type"); ?></b></td>
<?php if ($canManage) { ?>
	<td align="left" valign="top"><b><?php putGS("Access"); ?></b></td>
	<td align="left" valign="top"><b><?php putGS("Delete"); ?></b></td>
<?php } ?>
</tr>
<?php
foreach ($userTypes as $userType) { ?>
<tr <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<td>
		<?php p(htmlspecialchars($userType->getName())); ?>&nbsp;
	</td>

	<?php if ($canManage) { ?>
	<td align="center">
		<a href="/<?php echo $ADMIN; ?>/user_types/access.php?UType=<?php p(urlencode($userType->getName())); ?>"><?php  putGS('Change'); ?></a>
	</td>

	<td align="center">
		<a href="/<?php echo $ADMIN; ?>/user_types/do_del.php?UType=<?php p(urlencode($userType->getName())); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the user type $1?', $userType->getName()); ?>');">
		<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" border="0" alt="Delete user type <?php p(htmlspecialchars($userType->getName())); ?>" title="Delete user type <?php p(htmlspecialchars($userType->getName())); ?>"></a>
	</td>
<?php  } ?>
</tr>
<?php
} // foreach
?>
</table>
<?php  } else { ?><blockquote>
	<li><?php  putGS('No user types.'); ?></li>
</blockquote>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
