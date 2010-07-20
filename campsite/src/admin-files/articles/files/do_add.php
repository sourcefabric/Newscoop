<?php
camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');
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

if (!$g_user->hasPermission('AddFile')) {
	camp_html_display_error(getGS('You do not have the right to add files.'), null, true);
	exit;
}

// We set to unlimit the maximum time to execution whether
// safe_mode is disabled. Upload is still under control of
// max upload size.
if (!ini_get('safe_mode')) {
	set_time_limit(0);
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_description = Input::Get('f_description');
$f_language_specific = Input::Get('f_language_specific');
$f_content_disposition = Input::Get('f_content_disposition');

$BackLink = Input::Get('BackLink', 'string', null, true);

if (isset($_FILES["f_file"])) {
	switch($_FILES["f_file"]['error']) {
		case 0: // UPLOAD_ERR_OK
			break;
		case 1: // UPLOAD_ERR_INI_SIZE
		case 2: // UPLOAD_ERR_FORM_SIZE
			camp_html_display_error(getGS("The file exceeds the allowed max file size."), null, true);
			break;
		case 3: // UPLOAD_ERR_PARTIAL
			camp_html_display_error(getGS("The uploaded file was only partially uploaded. This is common when the maximum time to upload a file is low in contrast with the file size you are trying to input. The maximum input time is specified in 'php.ini'"), null, true);
			break;
		case 4: // UPLOAD_ERR_NO_FILE
			camp_html_display_error(getGS("You must select a file to upload."), null, true);
			break;
		case 6: // UPLOAD_ERR_NO_TMP_DIR
		case 7: // UPLOAD_ERR_CANT_WRITE
			camp_html_display_error(getGS("There was a problem uploading the file."), null, true);
			break;
	}
} else {
	camp_html_display_error(getGS("The file exceeds the allowed max file size."), null, true);
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

$description = new Translation($f_language_selected);
$description->create($f_description);

$attributes = array();
$attributes['fk_description_id'] = $description->getPhraseId();
$attributes['fk_user_id'] = $g_user->getUserId();
if ($f_language_specific == "yes") {
	$attributes['fk_language_id'] = $f_language_selected;
}
if ($f_content_disposition == "attachment") {
	$attributes['content_disposition'] = "attachment";
}

if (!empty($_FILES['f_file'])) {
	$file = Attachment::OnFileUpload($_FILES['f_file'], $attributes);
} else {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'files/popup.php'));
}

// Check if image was added successfully
if (PEAR::isError($file)) {
	camp_html_add_msg($file->getMessage());
	camp_html_goto_page($BackLink);
}

ArticleAttachment::AddFileToArticle($file->getAttachmentId(), $articleObj->getArticleNumber());

$logtext = getGS('File #$1 "$2" attached to article #$3 "$4"',
		 $file->getAttachmentId(), $file->getFileName(),
		 $articleObj->getArticleNumber(), $articleObj->getName());
Log::Message($logtext, null, 38);
?>
<script>
window.opener.document.forms.article_edit.f_message.value = "<?php putGS("File '$1' added.", $file->getFileName()); ?>";
window.opener.document.forms.article_edit.submit();
window.close();
</script>
