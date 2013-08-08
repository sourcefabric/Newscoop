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

            if (!$user->isAdmin()) { // can't go into admin
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                $redirector->direct('index', 'index', 'default');
            }

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
    }

    /**
     * Set session lifetime
     *
     * @return void
     */
    private function setSessionLifetime()
    {
        $auth = Zend_Auth::getInstance();
        $session = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
        $seconds = SystemPref::Get('SiteSessionLifeTime');

        $gc_works = ini_get('session.gc_probability');
        if (!empty($gc_works)) {
            $max_seconds = 0 + ini_get('session.gc_maxlifetime');
            if (!empty($max_seconds)) {
                if ($seconds > $max_seconds) {
                    $seconds = $max_seconds;
                }
            }
        }

        if ($seconds > 0) {
            $session->setExpirationSeconds($seconds);
        }
    }
}
