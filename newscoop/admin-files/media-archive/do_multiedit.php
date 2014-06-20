<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

foreach ($_POST['image'] as $id => $values) {
    $imageObj = new Image((int) $id);
    $updateArray = array(
        'Description' => $values['f_description'],
        'Photographer' => $values['f_photographer'],
        'Place' => $values['f_place'],
        'Date' => $values['f_date'],
        'photographer_url' => $values['f_photographer_url'],
    );
    $imageObj->update($updateArray);
}

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('image');

camp_html_add_msg($translator->trans("Images updated.", array(), 'media_archive'), "ok");
camp_html_goto_page("/$ADMIN/media-archive/index.php");
?>
