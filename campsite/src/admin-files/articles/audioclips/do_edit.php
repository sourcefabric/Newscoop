<?php
camp_load_translation_strings("article_images");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Audioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

if (SystemPref::Get("UseCampcasterAudioclips") != 'Y') {
    camp_html_display_error(getGS("Campcaster integration is disabled"), null, true);
    exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_audioclip_id = Input::Get('f_audioclip_id', 'string', null, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$BackLink = Input::Get('BackLink', 'string', null, true);
$formData = $_POST;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);

if (!$g_user->hasPermission('AttachAudioclipToArticle')) {
	camp_html_display_error(getGS("You do not have the right to change audioclip information."), null, true);
    exit;
}

$audioclipObj = new Audioclip($f_audioclip_id);
$res = $audioclipObj->editMetadata($formData);
if (PEAR::isError($res)) {
    camp_html_display_error(getGS("Failed to update audioclip information."), null, true);
    exit;
}

?>
<script>
window.opener.document.forms.article_edit.f_message.value = "<?php putGS("Audioclip '$1' updated.", $audioclipObj->getMetatagValue('title')); ?>";
window.opener.document.forms.article_edit.submit();
window.close();
</script>
