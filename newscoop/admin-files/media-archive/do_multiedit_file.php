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

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);

foreach ($_POST['file'] as $id => $values) {
    $description = new Translation((int) $f_language_selected);
    $description->create($values['f_description']); //$values

    $updateArray = array();
    $updateArray['fk_description_id'] = $description->getPhraseId();
/*    if ($values['f_language_specific'] == "yes") {
        $updateArray['fk_language_id'] = $f_language_selected;
    }
*/
    if ($values['f_content_disposition'] == "attachment") {
        $updateArray['content_disposition'] = "attachment";
    }
    $fileObj = new Attachment((int) $id);
    $fileObj->update($updateArray);
}

camp_html_add_msg($translator->trans("Images updated.", array(), 'media_archive'), "ok");
camp_html_goto_page("/$ADMIN/media-archive/index.php#files");
?>
