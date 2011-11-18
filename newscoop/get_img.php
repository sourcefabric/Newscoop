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

require_once __DIR__ . '/application.php';
$application->bootstrap('autoloader');

// reads parameters from image link URI
$imageId = (int) CampRequest::GetVar('ImageId', null, 'get');
$articleNr = (int) CampRequest::GetVar('NrArticle', null, 'get');
$imageNr = (int) CampRequest::GetVar('NrImage', null, 'get');
$imageRatio = (int) CampRequest::GetVar('ImageRatio', null, 'get');
$imageResizeWidth = (int) CampRequest::GetVar('ImageWidth', null, 'get');
$imageResizeHeight = (int) CampRequest::GetVar('ImageHeight', null, 'get');
$imageCrop = CampRequest::GetVar('ImageForcecrop', null, 'get');
$resizeCrop = CampRequest::GetVar('ImageCrop', null, 'get');

if (empty($imageId) && !empty($imageNr) && !empty($articleNr)) {
	$articleImage = new ArticleImage($articleNr, null, $imageNr);
	$imageId = $articleImage->getImageId();
}

$showImage = new CampGetImage($imageId, $imageRatio, $imageResizeWidth, $imageResizeHeight, $imageCrop, $resizeCrop);
