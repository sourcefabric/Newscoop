<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

use Symfony\Component\HttpFoundation\File\UploadedFile;

$translator = \Zend_Registry::get('container')->getService('translator');
$container = \Zend_Registry::get('container');
$request = $container->get('request');
$params = $request->request->all();
$f_image_url = Input::Get('f_image_url', 'string', '', true);
$nrOfFiles = isset($params['uploader_count']) ? $params['uploader_count'] : 0;
$f_article_edit = isset($params['f_article_edit']) ? $params['f_article_edit'] : null;
$f_language_id = isset($params['f_language_id']) ? $params['f_language_id'] : null;
$f_article_number = isset($params['f_article_number']) ? $params['f_article_number'] : null;

if (!SecurityToken::isValid() && !isset($f_article_edit)) {
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

$user = $container->get('security.context')->getToken()->getUser();
$em = $container->get('em');
$language = $em->getRepository('Newscoop\Entity\Language')->findOneByCode($request->getLocale());
$imageService = $container->get('image');

// load image from url
if (!empty($f_image_url)) {
    $newFileName = md5(uniqid()).'__'.basename($f_image_url);
    $fileLocation = $imageService->getImagePath() . $newFileName;
    file_put_contents($fileLocation, file_get_contents($f_image_url));

    // Check if it is really an image file
    $imageInfo = getimagesize($fileLocation);
    if ($imageInfo === false) {
        unlink($fileLocation);
        return new PEAR_Error($translator->trans('URL $1 is not an image.', array('$1' => $f_image_url), 'api'));
    }

    $mime = getimagesize($fileLocation);
    $file = new UploadedFile($fileLocation, $newFileName, $mime['mime'], filesize($fileLocation), null, true);
    $result = $imageService->upload($file, array('user' => $user));
    $result->setDate('0000-00-00');
    $images[] = $result;
}

// process uploaded images
for ($i = 0; $i < $nrOfFiles; $i++) {
    $tmpnameIdx = 'uploader_' . $i . '_tmpname';
    $nameIdx = 'uploader_' . $i . '_name';
    $statusIdx = 'uploader_' . $i . '_status';
    if ($params[$statusIdx] == 'done') {
        $fileLocation = $imageService->getImagePath() . $params[$tmpnameIdx];
        $mime = getimagesize($fileLocation);
        $file = new UploadedFile($fileLocation, $params[$nameIdx], $mime['mime'], filesize($fileLocation), null, true);
        $result = $imageService->upload($file, array('user' => $user));
        $result->setDate('0000-00-00');
        $images[] = $result;
    }
}

$em->flush();

if (!empty($images)) {
    camp_html_add_msg($translator->trans('$1 files uploaded.', array('$1' => count($images)), 'media_archive', "ok"));
    if ($f_article_edit) {
        require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');

        foreach ($images as $image) {
            $ImageTemplateId = ArticleImage::GetUnusedTemplateId($f_article_number);
            ArticleImage::AddImageToArticle($image->getId(), $f_article_number, $ImageTemplateId);
        }
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
