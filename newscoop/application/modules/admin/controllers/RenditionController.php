<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

/**
 * @Acl(ignore=True)
 */
class Admin_RenditionController extends Zend_Controller_Action
{
    public function init(){}

    public function indexAction()
    {
        $this->view->renditions = $this->_helper->service('image.rendition')->getRenditions();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->_helper->service('image.rendition')->setRenditionsLabels($this->getLabels());
            $this->_helper->service('image.rendition')->setRenditionsOrder($request->getPost('order', array()));
            $this->_helper->json(array());
        }
    }

    private function getLabels()
    {
        $labels = array();
        foreach ($this->getRequest()->getPost('labels', array()) as $label) {
            $labels[$label['name']] = $label['value'];
        }

        return $labels;
    }

    public function reloadRenditionsAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Zend_Controller_Action_Exception('POST required', 405);
        }

        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->_helper->service('image.rendition')->reloadRenditions();
        $this->_helper->flashMessenger($translator->trans('Renditions reloaded', array(), 'article_images'));
        $this->_helper->redirector('index', 'rendition', 'admin');
    }
}
