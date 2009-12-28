<?php
camp_load_translation_strings("article_audioclips");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");

if (SystemPref::Get("UseCampcasterAudioclips") != 'Y') {
    camp_html_display_error(getGS("Campcaster integration is disabled"), null, true);
    exit;
}

if (!$g_user->hasPermission("AddAudioclip")) {
	camp_html_display_error(getGS("You do not have the right to add audioclips" ), null, true);
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

// we check for the Campcaster session and show
// the login form if necessary
$isCcOnline = true;
$sessid = null;
$sessid = camp_session_get('cc_sessid', '');
if (empty($sessid)) {
    camp_html_goto_page('campcaster_login.php');
}

// ... is something wrong with either the sessid
// or the communication to Campcaster
$xrc =& XR_CcClient::Factory($mdefs);
$resp = $xrc->ping($sessid);
if (PEAR::isError($resp)) {
    switch ($resp->getCode()) {
        case '805':
            camp_html_goto_page('campcaster_login.php');
            break;
        case '804':
        default:
            camp_html_add_msg(getGS("Unable to reach the Campcaster server."));
            camp_html_add_msg(getGS("Try again later."));
            $isCcOnline = false;
            break;
    }
}

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();
?>
<script>
function checkAddForm(form) {
	retval = (form.f_media_file.value != '');
	if (!retval) {
	    alert('<?php putGS("You must select an audio file to upload."); ?>');
	    return retval;
	}
	retval = retval && <?php camp_html_fvalidate(); ?>;
	return retval;
} // fn checkAddForm
</script>

<p>
<form name="audioclip_add" method="POST" action="/<?php echo $ADMIN; ?>/articles/audioclips/edit.php" enctype="multipart/form-data" onsubmit="return checkAddForm(this);">
<table border="0" cellspacing="0" cellpadding="6" class="table_input">
<tr>
	<td colspan="2">
		<B><?php  putGS("Add New Audioclip"); ?></B>
		<HR noshade size="1" color="BLACK">
	</td>
</tr>
<tr>
	<td align="right" ><?php putGS("Media file"); ?>:</td>
	<td>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php p(intval(camp_convert_bytes(SystemPref::Get('MaxUploadFileSize')))); ?>" />
		<input type="file" name="f_media_file" size="32" class="input_file" alt="file|mp3,ogg,wav|bok" emsg="<?php putGS("You must select an audio file to upload."); ?>">
	</td>
</tr>
<tr>
	<td align="left" colspan="2" style="padding-left: 15px;"><?php putGS("Should this file only be available for this translation of the article, or for all translations?"); ?></td>
</tr>
<tr>
	<td colspan="2" class="indent"  style="padding-left: 30px;">
	<input type="radio" name="f_language_specific" value="yes"><?php putGS("Only this translation"); ?><br>
	<input type="radio" name="f_language_specific" value="no" checked><?php putGS("All translations"); ?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div align="center">
    <input type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>">
    <input type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>">
    <input type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>">
    <input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
    <input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
    <input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
    <input type="hidden" name="BackLink" value="<?php p($_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="f_action" value="add">
    <?php
    if ($isCcOnline) {
    ?>
	<input type="submit" name="Save" value="<?php putGS('Save'); ?>" class="button">
    <?php
    }
    ?>
	<input type="button" name="Cancel" value="<?php putGS('Cancel'); ?>" class="button" onclick="window.close();">
	</div>
	</td>
</tr>
</table>
</form>

<p>
