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
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Ban user from comments"); ?></title>
</head>
<body>
<?php
if (isset($connectedToOnlineServer)
        && $connectedToOnlineServer == false) {
?>
<center>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
        <TD >
<?php
        camp_html_display_msgs("0.25em", "0.25em");
?>
        </TD>
</TR>
<TR>
        <TD style="padding-left: 15px;">
                <INPUT TYPE="button" NAME="close" VALUE="<?php putGS('Close'); ?>" class="button" onclick="window.close();">
        </TD>
</TR>
</TABLE>
</center>
</BODY>
</HTML>
<?php
}
?>
<script type="text/javascript">
window.opener.location.reload();
</script>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD align="center">
		<b>
		<?PHP
		if ($banned) {
			putGS("The user has been banned.  Any future attempts by the user to post comments will not be allowed.");
		} else {
			putGS("The user is now allowed to post comments.");
		}
		?>
		</b>
	</TD>
</TR>
<tr>
	<td align="center">
		<INPUT type="button" name="close" value="<?php putGS("Close"); ?>" class="button" onclick="window.close();">
	</td>
</tr>
</table>
