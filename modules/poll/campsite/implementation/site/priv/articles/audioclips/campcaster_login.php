<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/XR_CcClient.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/LoginAttempts.php');

// Load the language files.
camp_load_translation_strings("globals");
camp_load_translation_strings("home");
camp_load_translation_strings("article_audioclips");

if (SystemPref::Get("UseCampcasterAudioclips") != 'Y') {
    camp_html_display_error(getGS("Campcaster integration is disabled"), null, true);
    exit;
}

$isCcOnline = true;
// ... is something wrong with either the sessid
// or the communication to Campcaster
$xrc =& XR_CcClient::Factory($mdefs);
$resp = $xrc->ping();
if (PEAR::isError($resp) && $resp->getCode() != 805) {
    camp_html_add_msg(getGS("Unable to reach the Campcaster server."));
    camp_html_add_msg(getGS("Try again later."));
    $isCcOnline = false;
}
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css" />
    <?php include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php"); ?>
    <title><?php putGS("Campcaster Login"); ?></title>
</head>
<body>
<?php camp_html_display_msgs(); ?>
<table border="0" cellspacing="0" cellpadding="1" width="100%" align="center" >
<tr>
    <td align="center" style="padding-top: 50px;">
        <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/campcaster_logo.png" border="0" />
    </td>
</tr>
</table>

<table width="400px" border="0" cellspacing="0" cellpadding="6" align="center" style="margin-top: 20px; background-color: #d5e2ee; border: 1px solid #8baed1;">
<form action="do_campcaster_login.php" name="campcaster_login" method="post" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<tr>
    <td colspan="2">
        <b><?php  putGS("Login"); ?></b>
        <hr noshade size="1"  color="black" />
    </td>
</tr>
<tr>
    <td colspan="2">
        <?php putGS('There is no valid Campcaster session'); ?>
        <br />
        <?php putGS('Please enter your user name and password'); ?>
    </td>
</tr>
<tr>
    <td align="right" ><?php putGS("Account name"); ?>:</td>
    <td>
        <?php p($g_user->getUserName()); ?>
        <input type="hidden" name="f_cc_username" value="<?php p($g_user->getUserName()); ?>" />
    </td>
</tr>
<tr>
    <td align="right" ><?php putGS("Password"); ?>:</td>
    <td>
        <input type="password" name="f_cc_password" size="32" class="input_text" alt="blank" emsg="<?php putGS("Please enter your password."); ?>" />
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
    <?php
    if ($isCcOnline == true) {
    ?>
        <input type="submit" class="button" name="Login" value="<?php putGS('Login'); ?>" />
    <?php
    } else {
    ?>
         <input type="submit" class="button" name="Close" value="<?php putGS('Close'); ?>" onclick="javascript:window.close();" />
    <?php
    }
    ?>
    </td>
</tr>
<input type="hidden" name="f_backlink" value="<?php p($BackLink); ?>" />
</form>
</table>
<script>
    document.forms.campcaster_login.f_cc_password.focus();
</script>
</body>
</html>
