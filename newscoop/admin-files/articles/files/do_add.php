<?php
camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

/**
 * Set message
 * @param string $message
 * @return void
 */
function setMessage($message, $isError = FALSE)
{
    if (empty($_REQUEST['archive'])) { // fancybox
        echo '<script type="text/javascript">';
        echo 'try {';

        if (!$isError) {
            echo 'parent.$.fancybox.reload = true;';
            echo 'parent.$.fancybox.message = "', $message, '";';
        } else {
            echo 'parent.$.fancybox.error = "', $message, '";';
        }

        echo 'parent.$.fancybox.close();';
        echo '} catch (e) {}';
        echo '</script>';
        exit;
    }

    if ($isError) {
	    camp_html_display_error($message, null, true);
        exit;
    }

    camp_html_add_msg($message);
}

if (empty($_POST)) {
    setMessage(getGS('The file exceeds the allowed max file size.'), TRUE);
}

if (!SecurityToken::isValid()) {
    setMessage(SecurityToken::GetToken(), TRUE);
    setMessage(getGS('Invalid security token!'), TRUE);
}

if (!$g_user->hasPermission('AddFile')) {
    setMessage(getGS('You do not have the right to add files.'), TRUE);
}

// We set to unlimit the maximum time to execution whether
// safe_mode is disabled. Upload is still under control of
// max upload size.
if (!ini_get('safe_mode')) {
	set_time_limit(0);
}

$inArchive = !empty($_REQUEST['archive']);

if (!$inArchive) {
    $f_language_id = Input::Get('f_language_id', 'int', 0);
    $f_language_selected = Input::Get('f_language_selected', 'int', 0);
    $f_article_number = Input::Get('f_article_number', 'int', 0);

    $articleObj = new Article($f_language_selected, $f_article_number);
    if (!$articleObj->exists()) {
        setMessage(getGS("Article does not exist."), TRUE);
    }
}

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
            setMessage(getGS("The file exceeds the allowed max file size."), TRUE);
			break;

		case 3: // UPLOAD_ERR_PARTIAL
			setMessage(getGS("The uploaded file was only partially uploaded. This is common when the maximum time to upload a file is low in contrast with the file size you are trying to input. The maximum input time is specified in 'php.ini'"), TRUE);
			break;

		case 4: // UPLOAD_ERR_NO_FILE
			setMessage(getGS("You must select a file to upload."), TRUE);
			break;

		case 6: // UPLOAD_ERR_NO_TMP_DIR
		case 7: // UPLOAD_ERR_CANT_WRITE
			setMessage(getGS("There was a problem uploading the file."), TRUE);
			break;
    }
} else {
	setMessage(getGS("The file exceeds the allowed max file size."), TRUE);
}

if (!Input::IsValid()) {
	setMessage(getGS('Invalid input: $1', Input::GetErrorString()), TRUE);
}

$description = new Translation((int) $f_language_selected);
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
    setMessage($file->getMessage());
	camp_html_goto_page($BackLink);
}

if (!$inArchive) {
    ArticleAttachment::AddFileToArticle($file->getAttachmentId(), $articleObj->getArticleNumber());

    $logtext = getGS('File #$1 "$2" attached to article',
        $file->getAttachmentId(), $file->getFileName());
    Log::ArticleMessage($articleObj, $logtext, null, 38, TRUE);

    setMessage(getGS('File attached.'));
} else { ?>
<script type="text/javascript"><!--
    if (opener && !opener.closed && opener.onUpload) {
        opener.onUpload();
        opener.focus();
        window.close();
    }
//--></script>
<?php } ?>
