<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

camp_load_translation_strings("imagearchive");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

foreach ($_POST['image'] as $id => $values) {
    $imageObj = new Image((int) $id);
    $updateArray = array(
        'Description' => $values['f_description'],
        'Photographer' => $values['f_photographer'],
        'Place' => $values['f_place'],
        'Date' => $values['f_date'],
    );
    $imageObj->update($updateArray);
}

camp_html_add_msg(getGS("Images updated."), "ok");
camp_html_goto_page("/$ADMIN/imagearchive/index.php");
?>
