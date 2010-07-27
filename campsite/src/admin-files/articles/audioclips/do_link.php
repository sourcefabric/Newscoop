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

if (!$g_user->hasPermission('AttachAudioclipToArticle')) {
	camp_html_display_error(getGS('You do not have the right to attach audioclips to articles.'), null, true);
	exit;
}

$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_audioclip_codes = Input::Get('f_audioclip_code', 'array', array(), true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

if (sizeof($f_audioclip_codes) == 0) {
    camp_html_add_msg('You must select at least one audioclip to link to the article.');
    exit;
}

foreach ($f_audioclip_codes as $audioclip) {
    $audioclipObj = new Audioclip($audioclip);
    if (!$audioclipObj->exists()) {
        camp_html_display_error(getGS("Audioclip does not exist."), null, true);
        exit;
    }
    // link the audioclip to the current article
    $articleAudioclip = new ArticleAudioclip($articleObj->getArticleNumber(), $audioclipObj->getGunId());
    $articleAudioclip->create();
}

?>
<script>
window.opener.document.forms.article_edit.f_message.value = "<?php putGS("Audioclip(s) attached successfully."); ?>";
window.opener.document.forms.article_edit.submit();
window.close();
</script>
