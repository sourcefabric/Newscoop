<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

$translator = \Zend_Registry::get('container')->getService('translator');
$f_image_url = Input::Get('f_image_url', 'string', '', true);
$nrOfFiles = isset($_POST['uploader_count']) ? $_POST['uploader_count'] : 0;
$f_article_edit = isset($_POST['f_article_edit']) ? $_POST['f_article_edit'] : null;
$f_language_id = isset($_POST['f_language_id']) ? $_POST['f_language_id'] : null;
$f_article_number = isset($_POST['f_article_number']) ? $_POST['f_article_number'] : null;

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('AddImage') && !isset($f_article_edit)) {
	camp_html_display_error($translator->trans("You do not have the right to add images.", array(), 'media_archive'));
	exit;
}

if (empty($f_image_url) && empty($nrOfFiles)) {
	camp_html_add_msg($translator->trans("You must select an image file to upload.", array(), 'media_archive'));
    if ($f_article_edit) {
        camp_html_goto_page('/'.$ADMIN.'/image/article-attach/article_number/'.$f_article_number.'/language_id/'.$f_language_id);
    }
	camp_html_goto_page("/$ADMIN/media-archive/add.php");
}

$images = array();

// process image url
if (!empty($f_image_url)) {
    $attributes = array(
        'Description' => '',
        'Photographer' => '',
        'Place' => '',
        'Date' => '',
    );

	if (camp_is_valid_url($f_image_url)) {
		$result = Image::OnAddRemoteImage($f_image_url, $attributes, $g_user->getUserId());
        $images[] = $result;
	} else {
		camp_html_add_msg($translator->trans("The URL you entered is invalid: $1", array('$1' => htmlspecialchars($f_image_url))), 'media_archive');
	}
}

// process uploaded images
for ($i = 0; $i < $nrOfFiles; $i++) {
    $tmpnameIdx = 'uploader_' . $i . '_tmpname';
    $nameIdx = 'uploader_' . $i . '_name';
    $statusIdx = 'uploader_' . $i . '_status';
    if ($_POST[$statusIdx] == 'done') {
        $result = Image::ProcessFile($_POST[$tmpnameIdx], $_POST[$nameIdx], $g_user->getUserId());
        $images[] = $result;
    }
}

if (!empty($images)) {
    camp_html_add_msg($translator->trans('$1 files uploaded.', array('$1' => count($images)), 'media_archive', "ok"));
    if ($f_article_edit) {
        require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
        
        //$imageIdList = array();
        foreach ($images as $image) {
            $ImageTemplateId = ArticleImage::GetUnusedTemplateId($f_article_number);
            ArticleImage::AddImageToArticle($image->getImageId(), $f_article_number, $ImageTemplateId);
            //$imageIdList[] = $image->getImageId();
        }
        //$imageIdList = implode(',',$imageIdList);
        //camp_html_goto_page('/'.$ADMIN.'/image/edit-image-data/images/'.$imageIdList);
        camp_html_goto_page('/'.$ADMIN.'/image/edit-image-data/article_number/'.$f_article_number.'/language_id/'.$f_language_id);
    }
    else {
        camp_html_goto_page("/$ADMIN/media-archive/multiedit.php");
    }
} else {
    if ($f_article_edit) {
        camp_html_goto_page('/'.$ADMIN.'/image/article-attach/article_number/'.$f_article_number.'/language_id/'.$f_language_id);
    }
    else {
        camp_html_add_msg($f_path . DIR_SEP . basename($newFilePath));
        camp_html_goto_page($backLink);
    }
}

?>
