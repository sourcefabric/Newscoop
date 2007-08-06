<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Subscription.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Publication.php");

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
verify_user_type();
compute_user_rights($g_user, $canManage, $canDelete);

$typeParam = 'uType=' . urlencode($uType);

$userId = Input::Get('User', 'int', 0);
if ($userId > 0) {
    $editUser = new User($userId);
    if ($editUser->getUserName() == '') {
        camp_html_display_error(getGS('No such user account.'), "/$ADMIN/users/?".get_user_urlparams());
        exit;
    }
    $isNewUser = false;
} else {
    $editUser = new User();
    $isNewUser = true;
}

$crumbs = array();
$crumbs[] = array(getGS("Users"), "");
$crumbs[] = array(getGS($uType), "/$ADMIN/users/?".get_user_urlparams());
if ($userId > 0) {
    $crumbs[] = array(getGS("Change user account information"), "");
} else {
    if ($uType == "Staff") {
        $crumbs[] = array(getGS("Add new staff member"), "");
    } else {
        $crumbs[] = array(getGS("Add new subscriber"), "");
    }
}
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");
?>
<p>
<?php
if ($canManage && ($userId > 0)) {
    $addLink = "edit.php?" . get_user_urlparams(0, true, true);
?>
<table class="action_buttons">
<tr>
    <td><a href="<?php echo $addLink; ?>">
        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0">
<?php
    if ($uType == "Staff") {
        echo "<b>" . getGS("Add new staff member") . "</b></a></td>";
    } else {
        echo "<b>" . getGS("Add new subscriber") . "</b></a></td>";
    }
    ?>
    </tr>
    </table>
    <p></p>
    <?php
}
?>

<?php camp_html_display_msgs(); ?>

<table border="0">
<tr>
    <td rowspan="2" valign="top" >
        <?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/info.php"); ?>
    </td>
<?php if ($userId > 0) { ?>
    <td valign="top" >
        <table cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top">
                <?php
                   if (($uType == 'Subscribers') && ($g_user->hasPermission("ManageSubscriptions"))) {
                    require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/subscriptions.php");
                }
                ?>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <?php
            if ($uType == 'Subscribers') {
                    require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/ipaccesslist.php");
                }
            ?>
            </td>
        </tr>
        </table>
    </td>
<?php } ?>
</tr>
</table>
<?php camp_html_copyright_notice(); ?>
