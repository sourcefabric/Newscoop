<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticleImage.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Image.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/XR_CcClient.php");

/**** XML RPC WORKING EXAMPLES ****/

$xrc =& XR_CcClient::factory($mdefs);

/**** download audio clip ****/
$sessid = camp_session_get('cc_sessid', '');
$gunid = '1fa9daab2eef333a';
$r = $xrc->xr_downloadRawAudioDataOpen($sessid, $gunid);
echo 'downloadRawAudioDataOpen response<br /><br />';
var_dump($r); echo '<br /><br />';

/**** store audio clip ****/
$metadata = '<?xml version="1.0" encoding="utf-8"?>
<audioClip>
    <metadata
       xmlns="http://mdlf.org/livesupport/elements/1.0/"
       xmlns:ls="http://mdlf.org/livesupport/elements/1.0/"
       xmlns:dc="http://purl.org/dc/elements/1.1/"
       xmlns:dcterms="http://purl.org/dc/terms/"
       xmlns:xml="http://www.w3.org/XML/1998/namespace"
     >
        <dc:title  >Fil Title3 txt</dc:title>
        <dcterms:alternative  >Alternative File Title txt</dcterms:alternative>
        <dcterms:extent  >00:01:00.000000</dcterms:extent>
        <dc:creator>John Y</dc:creator>
    </metadata>
</audioClip>';
$r = $xrc->xr_storeAudioClipOpen($sessid,'',$metadata,'one.mp3','f0ff3ae9c5f62499fc8a9f2a2ca227ec');
echo 'storeAudioClipOpen response<br /><br />';
var_dump($r); echo '<br /><br />';
/**************************/


if (!$g_user->hasPermission("AddAudioclip")) {
	camp_html_display_error(getGS("You do not have the right to add audioclips" ), null, true);
	exit;
}
$maxId = Image::GetMaxId();
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

/*
TODO: check communication with CC
if (!is_writable($Campsite['IMAGE_DIRECTORY'])) {
	camp_html_add_msg(getGS("Unable to add new image."));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['IMAGE_DIRECTORY']));
}
*/

$articleObj =& new Article($f_language_selected, $f_article_number);
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

$ImageTemplateId = ArticleImage::GetUnusedTemplateId($f_article_number);

$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();
?>
<script>
function checkAddForm(form) {
	retval = ((form.f_image_url.value != '') || (form.f_image_file.value != ''));
	if (!retval) {
	    alert('<?php putGS("You must select an audio file to upload."); ?>');
	    return retval;
	}
	retval = retval && <?php camp_html_fvalidate(); ?>;
	return retval;
} // fn checkAddForm
</script>

<P>
<FORM NAME="audioclip_add" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/audioclip/do_add.php" ENCTYPE="multipart/form-data" onsubmit="return checkAddForm(this);">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add New Audioclip"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("Media file"); ?>:</TD>
	<TD>
		<INPUT TYPE="FILE" NAME="f_media_file" SIZE="32" class="input_file" alt="file|jpg,jpeg,jpe,gif,png,tif,tiff|bok" emsg="<?php putGS("You must select an audio file to upload."); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
    <INPUT TYPE="HIDDEN" NAME="BackLink" VALUE="<?php  p($_SERVER['REQUEST_URI']); ?>">
<?php if (is_writable($Campsite['FILE_DIRECTORY'])) { ?>
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
<?php } else { ?>
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" onclick="window.close();">
<?php } ?>
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<script>
document.forms.audioclip_add.f_image_template_id.focus();
</script>

<P>