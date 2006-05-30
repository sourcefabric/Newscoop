<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("comments");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_ban_item.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('CommentModerate')) {
	camp_html_display_error(getGS("You do not have the right to moderate comments." ));
	exit;
}

$f_comment_id = Input::Get("f_comment_id", "int");

$banned = false;
$comment =& new Phorum_message($f_comment_id);
if ($comment->exists()) {
	$banIp = Input::Get("f_ban_ip", 'checkbox');
	if ($banIp) {
		$banItem =& new Phorum_ban_item();
		$banItem->create(PHORUM_BAD_IPS, false, $comment->getIpAddress());
		$banned = true;
	} else {
		Phorum_ban_item::DeleteMatching(PHORUM_BAD_IPS, false, $comment->getIpAddress());
	}
	$banEmail = Input::Get("f_ban_email", 'checkbox');
	if ($banEmail) {
		$banItem =& new Phorum_ban_item();
		$banItem->create(PHORUM_BAD_EMAILS, false, $comment->getEmail());
		$banned = true;
	} else {
		Phorum_ban_item::DeleteMatching(PHORUM_BAD_EMAILS, false, $comment->getEmail());
	}
	$banName = Input::Get("f_ban_name", 'checkbox');
	if ($banName) {
		$banItem =& new Phorum_ban_item();
		$banItem->create(PHORUM_BAD_NAMES, false, $comment->getAuthor());
		$banned = true;
	} else {
		Phorum_ban_item::DeleteMatching(PHORUM_BAD_NAMES, false, $comment->getAuthor());
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
<center>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" align="center" style="margin-top: 20px;">
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
</body>
</html>