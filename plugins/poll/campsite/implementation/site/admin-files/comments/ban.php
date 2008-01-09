<?php
camp_load_translation_strings("comments");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbReplication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_ban_item.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

if (!$g_user->hasPermission('CommentModerate')) {
	camp_html_display_error(getGS("You do not have the right to moderate comments." ));
	exit;
}

$f_comment_id = Input::Get('f_comment_id', 'int');

// Check input
if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
    exit;
}

if (SystemPref::Get("UseDBReplication") == 'Y') {
    $dbReplicationObj = new DbReplication();
    $connectedToOnlineServer = $dbReplicationObj->connect();
    if ($connectedToOnlineServer == false) {
        camp_html_add_msg(getGS("No connected to Online server"));
    }
}

if (!isset($connectedToOnlineServer)
        || $connectedToOnlineServer == true) {
	// load the comment
	$comment = new Phorum_message($f_comment_id);
	$bans = Phorum_ban_item::IsPostBanned($comment);
}
?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Comment ban settings"); ?></title>
</head>
<body>

<center>
<?php
if (isset($connectedToOnlineServer)
    && $connectedToOnlineServer == false) {
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" align="center" style="margin-top: 15px;">
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
</center>
</BODY>
</HTML>
<?php
    exit;
}
?>
<form action="/<?php p($ADMIN); ?>/comments/do_ban.php" method="GET">
<INPUT type="hidden" name="f_comment_id" value="<?php p($f_comment_id); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" align="center" style="margin-top: 15px;">
<TR>
	<TD >
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr><td style="border-bottom: 1px solid black; padding-bottom: 3px;" align="left">
			<B><?php putGS("Comment ban settings"); ?> </B>
		</td></tr>
		</table>
	</TD>
</TR>
<TR>
	<TD align="center">
		<table border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td align="center"><b><?php putGS("Ban?"); ?></b></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
		    <td align="center" style="padding-left: 10px;"><input type="checkbox" name="f_ban_name" class="input_checkbox" <?php if (isset($bans[PHORUM_BAD_NAMES])) { ?>checked<?php } ?> align="middle"></td>
		    <td><?php putGS("Name:"); ?> <?php p($comment->getAuthor()); ?></td>
		</tr>
		<tr>
		    <td align="center" style="padding-left: 10px;"><input type="checkbox" name="f_ban_email"  class="input_checkbox" <?php if (isset($bans[PHORUM_BAD_EMAILS])) { ?>checked<?php } if ($comment->getEmail() == '') { ?>disabled<?php } ?>></td>
		    <td><?php putGS("Email:"); ?> <?php p($comment->getEmail()); ?></td>
		</tr>
		<tr>
		    <td align="center" style="padding-left: 10px;"><input type="checkbox" name="f_ban_ip"  class="input_checkbox" <?php if (isset($bans[PHORUM_BAD_IPS])) { ?>checked<?php } ?>></td>
		    <td><?php putGS("IP address:"); ?> <?php p($comment->getIpAddress()); ?></td>
		</tr>
		</table>
	</TD>
</TR>
<TR>
	<TD align="center" style="padding-top: 5px;">
		<table>
		<tr>
			<td>
			<INPUT TYPE="submit" NAME="save" VALUE="<?php putGS('Save'); ?>" class="button">
			</TD>
			<TD style="padding-left: 15px;">
				<INPUT TYPE="button" NAME="close" VALUE="<?php putGS('Close'); ?>" class="button" onclick="window.close();">
			</TD>
		</tr>
		</table>
	</td>
</TR>
</TABLE>
</form>
</center>

</BODY>
</HTML>
