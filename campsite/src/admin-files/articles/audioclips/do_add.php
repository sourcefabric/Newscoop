<?php
camp_load_translation_strings("article_audioclips");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Audioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAudioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

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
$f_audiofile = Input::Get('f_audiofile', 'string', null);

$BackLink = Input::Get('BackLink', 'string', null, true);
$formData = $_POST;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

if (empty($f_audiofile)) {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'audioclips/popup.php'));
	exit(0);
}

$sessId = camp_session_get(CS_CAMPCASTER_SESSION_VAR_NAME, '');
$metaData = array();
foreach($mask['pages'] as $key => $val) {
	foreach($mask['pages'][$key] as $k => $v) {
		$element_encode = str_replace(':','_',$v['element']);
		$inputValue = Input::Get('f_'.$key.'_'.$element_encode, 'string', null, true);
		if (!is_null($inputValue) && $inputValue != '') {
			$metaData[$v['element']] = $inputValue;
		}
	}
}

$aClipGunid = Audioclip::StoreAudioclip($sessId, $f_audiofile, $metaData);
if (PEAR::isError($aClipGunid)) {
	camp_html_display_error(getGS('There was an error while saving the audioclip: $1',
									$aClipGunid->getMessage()), null, true);
	exit(0);
}

Audioclip::OnFileStore($f_audiofile);

// link the audioclip to the current article
$articleAudioclip = new ArticleAudioclip($articleObj->getArticleNumber(), $aClipGunid);
$attributes = null;
if ($f_language_specific == 'yes') {
    $attributes['fk_language_id'] = $f_language_id;
}
$articleAudioclip->create($attributes);
camp_html_add_msg(getGS("Audioclip '$1' added.", basename($f_audiofile)), 'ok');

?>
<script>
window.opener.document.forms.article_edit.submit();
window.close();
</script>
