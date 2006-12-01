<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SystemPref.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");

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

$sessid = camp_session_get('cc_sessid', '');
if (empty($sessid)) {
	camp_html_add_msg(getGS("Unable to add new audioclip. Unable to reach the Campcaster server."));
}

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

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

<P>
<FORM name="audioclip_add" method="POST" action="/<?php echo $ADMIN; ?>/articles/audioclips/edit.php" enctype="multipart/form-data" onsubmit="return checkAddForm(this);">
<TABLE border="0" cellspacing="0" cellpadding="6" class="table_input">
<TR>
	<TD colspan="2">
		<B><?php  putGS("Add New Audioclip"); ?></B>
		<HR noshade size="1" color="BLACK">
	</TD>
</TR>
<TR>
	<TD align="right" ><?php putGS("Media file"); ?>:</TD>
	<TD>
        <INPUT type="hidden" name="MAX_FILE_SIZE" value="<?php p(intval(camp_convert_bytes(SystemPref::Get('MaxUploadFileSize')))); ?>" />
		<INPUT type="file" name="f_media_file" size="32" class="input_file" alt="file|mp3,ogg,wav|bok" emsg="<?php putGS("You must select an audio file to upload."); ?>">
	</TD>
</TR>
<TR>
	<TD align="left" colspan="2" style="padding-left: 15px;"><?php putGS("Should this file only be available for this translation of the article, or for all translations?"); ?></TD>
</TR>
<TR>
	<TD colspan="2" class="indent"  style="padding-left: 30px;">
	<INPUT type="radio" name="f_language_specific" value="yes"><?php putGS("Only this translation"); ?><br>
	<INPUT type="radio" name="f_language_specific" value="no" checked><?php putGS("All translations"); ?>
	</TD>
</TR>
<TR>
	<TD colspan="2">
	<DIV align="center">
    <INPUT type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>">
    <INPUT type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>">
    <INPUT type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>">
    <INPUT type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
    <INPUT type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
    <INPUT type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
    <INPUT type="hidden" name="BackLink" value="<?php p($_SERVER['REQUEST_URI']); ?>">
    <INPUT type="hidden" name="f_action" value="add">
    <?php
    if (!empty($sessid)) {
    ?>
	<INPUT type="submit" name="Save" value="<?php putGS('Save'); ?>" class="button">
    <?php
    }
    ?>
	<INPUT type="button" name="Cancel" value="<?php putGS('Cancel'); ?>" class="button" onclick="window.close();">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>

<P>
