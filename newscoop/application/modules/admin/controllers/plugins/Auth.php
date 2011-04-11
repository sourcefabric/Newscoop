<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Auth controller plugin
 */
class Admin_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    private $ignored = array(
        'auth',
    );

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (in_array($request->getControllerName(), $this->ignored)) {
            return;
        }

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { // logged in
            return;
        }

        if (empty($_POST['_next'])) { // after login
            $_POST['_next'] = $request->isPost() ? 'post' : 'get';
        }

        // show login
        $request->setModuleName('admin')
            ->setControllerName('auth')
            ->setActionName('login')
            ->setDispatched(false);
    }
}
