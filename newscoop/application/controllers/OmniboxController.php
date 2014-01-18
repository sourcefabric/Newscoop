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
        $this->_helper->viewRenderer->setViewSuffix('tpl');
		$this->view->gimme = $this->_getParam('gimme');
    }
	
    public function loginAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('login', 'json')->initContext();

        $translator = Zend_Registry::get('container')->getService('translator');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getParams();

            $adapter = $this->_helper->service('auth.adapter');
            $adapter->setEmail($params['email'])->setPassword($params['password']);
            $result = $this->auth->authenticate($adapter);

            if ($result->getCode() == Zend_Auth_Result::SUCCESS) {
                $user = Zend_Registry::get('container')->getService('user')->getCurrentUser();
                $metaUser = new \MetaUser($user);
                $width = array_key_exists("imageWidth", $params) ? $params['imageWidth'] : 80;
                $height = array_key_exists("imageHeight", $params) ? $params['imageHeight'] : 80;
                $specification = array_key_exists("imageSpecification", $params) ? $params['imageSpecification'] : 'fit';

                $this->view->userData = array(
                    'realName' => $user->getRealName(),
                    'username' => $user->getUsername(),
                    'avatar' => $metaUser->image($width, $height, $specification)
                );

                $this->view->response = 'OK';
            } else {
                $this->view->response = $translator->trans('Login failed.');
            }
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
