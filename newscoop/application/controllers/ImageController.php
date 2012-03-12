<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class ImageController extends Zend_Controller_Action
{
    const DATE_FORMAT = 'D, d M Y H:i:s \G\M\T';

    public function cacheAction()
    {
        $this->getResponse()->clearHeaders();
        $this->getResponse()->setHeader('Cache-Control', sprintf('public, max-age=%d', 3600 * 24 * 30), true);
        $this->getResponse()->setHeader('Pragma', 'cache', true);
        $this->getResponse()->setHeader('Expires', gmdate(self::DATE_FORMAT, date_create('+30 days')->getTimestamp()), true);

        try {
            $image = $this->_helper->service('image')->generateFromSrc($this->_getParam('src'));
            $this->getResponse()->setBody($image->toString());
            $this->getResponse()->setHeader('Content-Type', image_type_to_mime_type($image->getFormatFromString($this->getResponse()->getBody())), true);
            $this->getResponse()->sendHeaders();
        } catch (\Exception $e) {
            $this->getResponse()->clearHeaders();
            $this->getResponse()->setHttpResponseCode(404);
        }

        $this->getResponse()->sendResponse();
        exit;
    }
}
