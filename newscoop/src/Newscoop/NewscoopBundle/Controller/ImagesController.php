<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ImagesController extends Controller
{
    /**
     * @Route("get_img")
     */
    public function indexAction(Request $request)
    {
        // reads parameters from image link URI
        $imageId = $request->get('ImageId', null);
        $articleNr = $request->get('NrArticle', null);
        $imageNr = $request->get('NrImage', null);
        $imageRatio = $request->get('ImageRatio', null);
        $imageResizeWidth = $request->get('ImageWidth', null);
        $imageResizeHeight = $request->get('ImageHeight', null);
        $imageCrop = $request->get('ImageForcecrop', null);
        $resizeCrop = $request->get('ImageCrop', null);

        if (empty($imageId) && !empty($imageNr) && !empty($articleNr)) {
            $articleImage = new \ArticleImage($articleNr, null, $imageNr);
            $imageId = $articleImage->getImageId();
        }

        $showImage = new \CampGetImage($imageId, $imageRatio, $imageResizeWidth, $imageResizeHeight, $imageCrop, $resizeCrop);
        die();
    }
}