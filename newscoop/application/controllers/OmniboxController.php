<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Omnibox controller
 */
class OmniboxController extends Zend_Controller_Action
{
    /** @var Zend_Auth */
    private $auth;

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->auth = Zend_Auth::getInstance();
    }

    public function indexAction()
    {
		$this->view->gimme = $this->_getParam('gimme');
	}
	
	public function loginAction()
	{
		$this->getHelper('contextSwitch')->addActionContext('login', 'json')->initContext();
		
		$parameters = $this->getRequest()->getParams();
		
		$adapter = $this->_helper->service('auth.adapter');
		$adapter->setUsername($parameters['username'])->setPassword($parameters['password']);
		$result = $this->auth->authenticate($adapter);

		if ($result->getCode() == Zend_Auth_Result::SUCCESS) {
			$this->view->response = 'OK';
		}
		else {
			$this->view->response = getGS('Login failed.');
		}
	}
	public function logoutAction()
	{
		$this->getHelper('contextSwitch')->addActionContext('logout', 'json')->initContext();
		
		if ($this->auth->hasIdentity()) {
            $this->auth->clearIdentity();
        }
        $this->view->response = 'OK';
	}
}
