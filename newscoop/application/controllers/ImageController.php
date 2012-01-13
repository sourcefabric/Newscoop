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
        $this->_helper->service('image')->generateFromSrc($this->_getParam('src'));
        exit;
    }
}
