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
        $src = $this->_getParam('src');
        if (substr_count($src, '/') > 2) {
            $srcAry = explode('/', $src, 3);
            $srcAry[2] = rawurlencode(rawurlencode($srcAry[2]));
            $src = implode('/', $srcAry);
        }

        $this->_helper->service('image')->generateFromSrc($src);
        exit;
    }
}
