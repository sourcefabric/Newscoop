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

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

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

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap('autoloader');

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
