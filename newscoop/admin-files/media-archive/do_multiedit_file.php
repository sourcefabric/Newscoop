<?php
/**
 * @package Newscoop
 *
 * @author Mihai Nistor <mihai.nistor@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');

$translator = \Zend_Registry::get('container')->getService('translator');
$em = \Zend_Registry::get('container')->getService('em');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);

foreach ($_POST['file'] as $id => $values) {
    $attachment = $em->getRepository('Newscoop\Entity\Attachment')->findOneById($id);
    $description = $em->getRepository('Newscoop\Entity\Translation')->findOneById($attachment->getDescription()->getId());

    if ($f_language_selected > 0) {
        $description->setLanguage($f_language_selected);
    }

    $description->setTranslationText($values['f_description']);
    $attachment->setDescription($description);
    $attachment->setUpdated(new \DateTime());
    $attachment->setContentDisposition($values['f_content_disposition']);
}

$em->flush();
camp_html_add_msg($translator->trans("Images updated.", array(), 'media_archive'), "ok");
camp_html_goto_page("/$ADMIN/media-archive/index.php#files");
?>
