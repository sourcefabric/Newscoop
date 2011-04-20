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
    /** @var array */
    private $ignored = array(
        'auth',
        'error',
        'legacy',
        'login.php',
        'password_recovery.php',
        'password_check_token.php',
    );

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        global $ADMIN;

        // filter logged
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            return;
        }

        // filter ignored
        if (in_array($request->getControllerName(), $this->ignored)) {
            return;
        }

        if (empty($_POST['_next'])) { // action after login
            $_POST['_next'] = $request->isPost() ? 'post' : 'get';
        }

        // use old login
        $_SERVER['REQUEST_URI'] = "/{$ADMIN}/login.php";
        $request
            ->setModuleName('admin')
            ->setControllerName('legacy')
            ->setActionName('index')
            ->setDispatched(false);
    }
}
