<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Auth controller plugin
 */
class Application_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /** @var array */
    private $modules = array();

    /** @var array */
    private $ignore = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->modules = $config['modules'];
        $this->ignore = $config['ignore'];
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->setSessionLifeTime();

        if (!in_array($request->getModuleName(), $this->modules)) {
            return;
        }

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $user = Zend_Registry::get('container')->getService('user')->getCurrentUser();

            // set user for application
            $GLOBALS['g_user'] = $user;
            Zend_Registry::set('user', $user);

            // set view user
            $view = Zend_Registry::get('view');
            $view->currentUser = $user;

            // set view navigation acl
            $acl = Zend_Registry::get('acl')->getAcl($user);
            /* @var $acl Zend_Acl */

            $view->navigation()->setAcl($acl);
            $view->navigation()->setRole($user);
            return;
        }

        if (in_array($request->getControllerName(), $this->ignore)) {
            return;
        }

        if (empty($_POST['_next'])) { // action after login
            $_POST['_next'] = $request->isPost() ? 'post' : 'get';
        }

        if($this->_request->isXmlHttpRequest()) {
            $this->_response->setHeader('not-logged-in', true);
        }

        // use old login
        $_SERVER['REQUEST_URI'] = "/$GLOBALS[ADMIN]/login.php";
        $request
            ->setModuleName('admin')
            ->setControllerName('legacy')
            ->setActionName('index')
            ->setDispatched(false);
    }

    /**
     * Set session lifetime
     *
     * @return void
     */
    private function setSessionLifetime()
    {
        $storage = new Zend_Auth_Storage_Session('Zend_Auth_Storage');
        Zend_Auth::getInstance()->setStorage($storage);

        $session = new Zend_Session_Namespace($storage->getNamespace());
        $seconds = SystemPref::Get('SiteSessionLifeTime');
        if ($seconds > 0) {
            $session->setExpirationSeconds($seconds);
        }
    }
}
