<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class DashboardController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\UserService */
    private $service;

    /** @var Newscoop\Entity\User */
    private $user;

    public function init()
    {
        $GLOBALS['controller'] = $this;
        $this->_helper->layout->disableLayout();

        $this->service = $this->_helper->service('user');
        $this->user = $this->service->getCurrentUser();
    }

    public function preDispatch()
    {
        if (empty($this->user)) {
            $this->_helper->redirector('index', 'auth');
        }
    }

    public function indexAction()
    {
        $form = new Application_Form_Profile();
        $form->setMethod('POST');

        $form->setDefaultsFromEntity($this->user);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->service->update($this->user, $form->getValues());
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
        $this->view->user = new MetaUser($this->user);
    }
}
