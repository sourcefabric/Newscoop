<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(ignore=True)
 */
class Admin_RenditionController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->renditions = $this->_helper->service('image.rendition')->getRenditions();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->_helper->service('image.rendition')->setRenditionsOrder($request->getPost('order', array()));
            $this->_helper->json(array());
        }
    }
}
