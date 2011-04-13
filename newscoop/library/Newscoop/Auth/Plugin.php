<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Auth;

use Zend_Auth,
    Zend_Controller_Plugin_Abstract,
    Zend_Controller_Request_Abstract;

/**
 * Auth controller plugin
 */
class Plugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($request->getControllerName() == 'auth') { // loggin in/out
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
