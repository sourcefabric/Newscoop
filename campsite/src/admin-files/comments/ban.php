<?php
camp_load_translation_strings("comments");
require_once($GLOBALS['g_campsiteDir']."/include/phorum_load.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_message.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_ban_item.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

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
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Expires" content="now" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<title><?php putGS("Comment ban settings"); ?></title>
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
<?php
    exit;
}
?>
<center>
<form action="/<?php p($ADMIN); ?>/comments/do_ban.php" method="GET">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_comment_id" value="<?php p($f_comment_id); ?>" />
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td>
    <table cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td style="border-bottom: 1px solid black; padding-bottom: 3px;" align="left">
        <b><?php putGS("Comment ban settings"); ?> </b>
      </td>
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td align="center">
    <table border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td align="center"><b><?php putGS("Ban?"); ?></b></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center" style="padding-left: 10px;"><input type="checkbox" name="f_ban_name" class="input_checkbox" <?php if (isset($bans[PHORUM_BAD_NAMES])) { ?>checked<?php } ?> align="middle"></td>
      <td><?php putGS("Name"); ?>: <?php p($comment->getAuthor()); ?></td>
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
  </td>
</tr>
<tr>
  <td align="center" style="padding-top: 5px;">
    <table>
    <tr>
      <td>
        <input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" />
      </td>
      <td style="padding-left: 15px;">
        <input type="button" name="close" value="<?php putGS('Close'); ?>" class="button" onclick="window.close();" />
      </td>
    </tr>
    </table>
  </td>
</tr>
</table>
</form>
</center>
