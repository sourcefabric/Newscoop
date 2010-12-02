<?php
camp_load_translation_strings("comments");
require_once($GLOBALS['g_campsiteDir']."/include/phorum_load.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_message.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_ban_item.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

if (!SecurityToken::isValid()) {
    camp_html_add_msg(getGS('Invalid security token!'));
?>
<script type="text/javascript">
window.close();
window.opener.location.reload();
</script>
<?php
    exit;
}

if (!$g_user->hasPermission('CommentModerate')) {
	camp_html_add_msg(getGS("You do not have the right to moderate comments." ));
?>
<script type="text/javascript">
window.close();
window.opener.location.reload();
</script>
<?php
	exit;
}

if (SystemPref::Get("UseDBReplication") == 'Y') {
    $dbReplicationObj = new DbReplication();
    $connectedToOnlineServer = $dbReplicationObj->connect();
    if ($connectedToOnlineServer == false) {
        camp_html_add_msg(getGS("Comments Disabled: you are either offline or not able to reach the Online server"));
?>
<script type="text/javascript">
window.close();
window.opener.location.reload();
</script>
<?php
		exit;
    }
}

if (!isset($connectedToOnlineServer)
        || $connectedToOnlineServer == true) {
	$f_comment_id = Input::Get("f_comment_id", "int");

	$banned = false;
	$comment = new Phorum_message($f_comment_id);
	if ($comment->exists()) {
		$banIp = Input::Get("f_ban_ip", 'checkbox');
		if ($banIp) {
			$banItem = new Phorum_ban_item();
			$banItem->create(PHORUM_BAD_IPS, false, $comment->getIpAddress());
			$banned = true;
		} else {
			Phorum_ban_item::DeleteMatching(PHORUM_BAD_IPS, false, $comment->getIpAddress());
		}
		$banEmail = Input::Get("f_ban_email", 'checkbox');
		if ($banEmail) {
			$banItem = new Phorum_ban_item();
			$banItem->create(PHORUM_BAD_EMAILS, false, $comment->getEmail());
			$banned = true;
		} else {
			Phorum_ban_item::DeleteMatching(PHORUM_BAD_EMAILS, false, $comment->getEmail());
		}
		$banName = Input::Get("f_ban_name", 'checkbox');
		if ($banName) {
			$banItem = new Phorum_ban_item();
			$banItem->create(PHORUM_BAD_NAMES, false, $comment->getAuthor());
			$banned = true;
		} else {
			Phorum_ban_item::DeleteMatching(PHORUM_BAD_NAMES, false, $comment->getAuthor());
		}
	}
}

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
  <title><?php putGS("Ban user from comments"); ?></title>
</head>
<body>
<?php
if (isset($connectedToOnlineServer)
        && $connectedToOnlineServer == false) {
?>
<center>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td>
<?php
  camp_html_display_msgs("0.25em", "0.25em");
?>
  </td>
</tr>
<tr>
  <td style="padding-left: 15px;">
    <input type="button" name="close" value="<?php putGS('Close'); ?>" class="button" onclick="window.close();" />
  </td>
</tr>
</table>
</center>
</body>
</html>
<?php
}
?>
<script type="text/javascript">
window.opener.location.reload();
</script>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td align="center">
    <b>
    <?php
    if ($banned) {
        putGS("The user has been banned.  Any future attempts by the user to post comments will not be allowed.");
    } else {
        putGS("The user is now allowed to post comments.");
    }
    ?>
    </b>
  </td>
</tr>
<tr>
  <td align="center">
    <input type="button" name="close" value="<?php putGS("Close"); ?>" class="button" onclick="window.close();" />
  </td>
</tr>
</table>
