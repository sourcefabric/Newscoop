<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Image\Rendition,
    Newscoop\Package\PackageService;

/**
 * @Acl(ignore=True)
 */
class Admin_SlideshowRestController extends Zend_Rest_Controller
{

    public function headAction()
    {
    }

    public function indexAction()
    {
        $limit = 25;
        $this->_helper->json($this->view->slideshowsJson($this->_helper->service('package')->findBy(array(), array('id' => 'desc'), $limit, ($this->_getParam('page', 1) - 1) * $limit)));
    }

    public function getAction()
    {
    }

    public function postAction()
    {
    }

    public function putAction()
    {
    }

    public function deleteAction()
    {
        try {
            $this->_helper->service('package')->delete($this->_getParam('id'));
            $this->_helper->json(array());
        } catch (Exception $e) {
            $this->_helper->json(array(
                'exception' => array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ),
            ));
        }
    }
}
