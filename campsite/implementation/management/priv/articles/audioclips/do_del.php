<?php
camp_load_translation_strings("article_files");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAudioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

if (!$g_user->hasPermission('AttachAudioclipToArticle')) {
	camp_html_display_error(getGS('You do not have the right to delete audioclips.' ), null, true);
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_audioclip_gunid = Input::Get('f_audioclip_gunid', 'string', null, true);

// Check input
if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
    exit;
}

$articleObj =& new Article($f_language_selected, $f_article_number);

if (!$articleObj->exists()) {
    camp_html_display_error(getGS("Article does not exist."), null, true);
    exit;
}

$audioclipObj =& new Audioclip($f_audioclip_gunid);
if (is_null($audioclipObj->getGunId())) {
    camp_html_display_error(getGS('Audioclip does not exist.'), null, true);
    exit;
}

// Deletes the link to the article
$articleAudioclipObj =& new ArticleAudioclip($articleObj->getArticleNumber(), $audioclipObj->getGunId());
$articleAudioclipObj->delete();
// Deletes all the audioclip metadata
//if (!$audioclipObj->hasLock()) {
    $audioclipObj->deleteMetadata();
//}

// Go back to article.
camp_html_add_msg(getGS("Audioclip '$1' deleted.", $audioclipObj->getMetatagValue('title')), "ok");
camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
?>