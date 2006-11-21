<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAudioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Translation.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

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
    $f_xrParams = array();
    $f_xrParams['sessid'] = $_SESSION['cc_sessid'];
    /*************************/
    /* This code below will be a class method */
    $f_xrParams['mdata'] = '<?xml version="1.0" encoding="utf-8"?>
    <audioClip>
        <metadata
            xmlns="http://mdlf.org/campcaster/elements/1.0/"
            xmlns:ls="http://mdlf.org/campcaster/elements/1.0/"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:dcterms="http://purl.org/dc/terms/"
            xmlns:xml="http://www.w3.org/XML/1998/namespace"
        >';

    foreach($mask['pages'] as $key => $val) {
        foreach($mask['pages'][$key] as $k => $v) {
            $element_encode = str_replace(':','_',$v['element']);
            $formData['f_'.$key.'_'.$element_encode] ? $mData[$v['element']] = $formData['f_'.$key.'_'.$element_encode] : NULL;
        }
    }
    foreach($mData as $key => $val) {
        $f_xrParams['mdata'] .= '<'.$key.'>'.$val.'</'.$key.'>';
    }
    $f_xrParams['mdata'] .= '</metadata>
    </audioClip>';
    /*************************/
    $f_xrParams['fname'] = basename($f_audiofile);
    $f_xrParams['chsum'] = md5_file($f_audiofile);
    $aClipObj =& new Audioclip();
    $aClipGunid = $aClipObj->storeAudioclip($f_audiofile, $f_xrParams);
    // Check if audioclip was added successfully to
    // Campcaster storage server
    if (PEAR::isError($aClipGunid)) {
        // Error
        camp_html_goto_page($BackLink);
    }
    $aClipObj->OnFileStore($f_audiofile);
} else {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'audioclips/popup.php'));
}

// link the audioclip to the current article
$p_gunid = hexdec($aClipGunid);
ArticleAudioclip::AddAudioclipToArticle($p_gunid, $articleObj->getArticleNumber());

?>
<script>
window.opener.document.forms.article_edit.f_message.value = "<?php putGS("Audioclip '$1' added.", basename($f_audiofile)); ?>";
window.opener.document.forms.article_edit.onsubmit();
window.opener.document.forms.article_edit.submit();
window.close();
</script>
