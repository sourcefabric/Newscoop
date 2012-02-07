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
    public function cacheAction()
    {
        header('Cache-Control: public, max-age=3600');
        header('Pragma: cache');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true);

        $this->_helper->service('image')->generateFromSrc($this->_getParam('src'));
        exit;
    }
}
