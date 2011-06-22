<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

use Newscoop\Entity\User\Staff;

/**
 * Auth controller plugin
 */
class Application_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    public function __construct($namespace)
    {
        $auth = Zend_Auth::getInstance();
        $storage = new Zend_Auth_Storage_Session('Zend_Auth_'.ucfirst($namespace));
        $auth->setStorage($storage);

        $seconds = (int) SystemPref::Get('SiteSessionLifeTime');
        if ($seconds <= 0) { // must be positive number
            return;
        }

        $session = new Zend_Session_Namespace($storage->getNamespace());
        $session->setExpirationSeconds($seconds);
    }

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
        global $ADMIN, $g_user;

        // filter logged
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $doctrine = $this->getResource('doctrine');
            $user = $doctrine->getEntityManager()->find( 'Newscoop\Entity\User\Staff', $auth->getIdentity() );

            /* @var $user Newscoop\Entity\User\Staff */

            // set user for application
            $g_user = $user;
            Zend_Registry::set('user', $user);

            // set view user
            $view = $this->getResource('view');
            $view->user = $user;

            // set view navigation acl
            $acl = $this->getResource('acl')->getAcl($user);
            /* @var $acl Zend_Acl */

            $view->navigation()->setAcl($acl);
            $view->navigation()->setRole($user);

			if( !\SaaS::singleton()->hasPrivilege( $request->getControllerName(), $request->getActionName() ) )
			{
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
				/* @var $redirector Zend_Controller_Action_Helper_Redirector */
				$redirector->direct( "index", "index", "admin" );
			}
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

    /**
     * Get resource
     *
     * @param string $name
     * @return mixed
     */
    private function getResource($name)
    {
        return Zend_Registry::get($name);
    }

}
