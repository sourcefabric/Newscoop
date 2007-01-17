<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAudioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Translation.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

if (SystemPref::Get("UseCampcasterAudioclips") != 'Y') {
    camp_html_display_error(getGS("Campcaster integration is disabled"), null, true);
    exit;
}

if (!$g_user->hasPermission('AddAudioclip')) {
	camp_html_display_error(getGS('You do not have the right to add audioclips.'), null, true);
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_language_specific = Input::Get('f_language_specific', 'string', null, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_audiofile = Input::Get('f_audiofile', 'string', null, true);

$BackLink = Input::Get('BackLink', 'string', null, true);
$formData = $_POST;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj =& new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

if (!empty($f_audiofile)) {
    $sessId = camp_session_get('cc_sessid', '');
    $aClipGunid = Audioclip::StoreAudioclip($sessId, $f_audiofile, $formData);
    if (PEAR::isError($aClipGunid)) {
        camp_html_display_error(getGS('Audio file could not be stored'));
        camp_html_goto_page($BackLink);
    }
    Audioclip::OnFileStore($f_audiofile);
} else {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'audioclips/popup.php'));
}


// link the audioclip to the current article
$articleAudioclip =& new ArticleAudioclip($articleObj->getArticleNumber(), $aClipGunid);
$attributes = null;
if ($f_language_specific == 'yes') {
    $attributes['fk_language_id'] = $f_language_id;
}
$articleAudioclip->create($attributes);

?>
<script>
window.opener.document.forms.article_edit.f_message.value = "<?php putGS("Audioclip '$1' added.", basename($f_audiofile)); ?>";
window.opener.document.forms.article_edit.onsubmit();
window.opener.document.forms.article_edit.submit();
window.close();
</script>
