<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__)) . '/library',
    realpath(dirname(__FILE__) . '/../include'),
    get_include_path(),
)));
if (!is_file('Zend/Application.php')) {
	// include libzend if we dont have zend_application
	set_include_path(implode(PATH_SEPARATOR, array(
		'/usr/share/php/libzend-framework-php',
		get_include_path(),
	)));
}
require_once 'Zend/Application.php';

/**
 * Includes
 */
$GLOBALS['g_campsiteDir'] = dirname(__FILE__);
require_once($GLOBALS['g_campsiteDir'].'/include/campsite_init.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampRequest.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampGetImage.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');

// reads parameters from image link URI
$imageId = (int) CampRequest::GetVar('ImageId', null, 'get');
$articleNr = (int) CampRequest::GetVar('NrArticle', null, 'get');
$imageNr = (int) CampRequest::GetVar('NrImage', null, 'get');
$imageRatio = (int) CampRequest::GetVar('ImageRatio', null, 'get');
$imageResizeWidth = (int) CampRequest::GetVar('ImageWidth', null, 'get');
$imageResizeHeight = (int) CampRequest::GetVar('ImageHeight', null, 'get');

if (empty($imageId) && !empty($imageNr) && !empty($articleNr)) {
	$articleImage = new ArticleImage($articleNr, null, $imageNr);
	$imageId = $articleImage->getImageId();
}

$showImage = new CampGetImage($imageId, $imageRatio, $imageResizeWidth, $imageResizeHeight);

?>
