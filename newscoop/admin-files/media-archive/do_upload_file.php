<?php
/**
 * @package Newscoop
 *
 * @author Mihai Nistor <mihai.nistor@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

use Symfony\Component\HttpFoundation\File\UploadedFile;

require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');

$container = \Zend_Registry::get('container');
$request = $container->get('request');
$translator = \Zend_Registry::get('container')->getService('translator');
$params = $request->request->all();

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('AddFile')) {
    camp_html_display_error($translator->trans("You do not have the right to add files.", array(), 'media_archive'));
    exit;
}

$nrOfFiles = isset($params['uploader_count']) ? $params['uploader_count'] : 0;

$user = $container->get('security.context')->getToken()->getUser();
$em = $container->get('em');
$language = $em->getRepository('Newscoop\Entity\Language')->findOneByCode($request->getLocale());
$attachmentService = $container->get('attachment');

// process uploaded files
for ($i = 0; $i < $nrOfFiles; $i++) {
    $tmpnameIdx = 'uploader_' . $i . '_tmpname';
    $nameIdx = 'uploader_' . $i . '_name';
    $statusIdx = 'uploader_' . $i . '_status';
    if ($params[$statusIdx] == 'done') {
        $fileLocation = $attachmentService->getStorageLocation(new \Newscoop\Entity\Attachment()).'/'.$params[$tmpnameIdx];
        $file = new UploadedFile($fileLocation, $params[$nameIdx], mime_content_type($fileLocation), filesize($fileLocation), null, true);
        $result = $attachmentService->upload($file, '', $language, array('user' => $user));
    }
}

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('attachment');

if ($result) {
    camp_html_add_msg($translator->trans('$1 files uploaded.', array('$1' => $nrOfFiles), 'media_archive'), "ok");
    camp_html_goto_page("/$ADMIN/media-archive/multiedit_file.php");
} else {
    camp_html_add_msg($f_path . DIR_SEP . basename($newFilePath));
    camp_html_goto_page($backLink);
}

?>
