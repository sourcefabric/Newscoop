<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Acl controller plugin
 */
class Application_Plugin_Acl extends Zend_Controller_Plugin_Abstract
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
        if (!in_array($request->getModuleName(), $this->modules)) {
            return;
        }

        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return;
        }

        $resource = $request->getControllerName();
        $action = $request->getActionName();

        $user= Zend_Registry::get('container')->getService('user')->getCurrentUser();
        $blogService = Zend_Registry::get('container')->getService('blog');
        if ($blogService->isBlogger($user)) {
            // f_article_edit condition should be removed once file upload is moved to a final place where bloggers can access.
            if (!$blogService->isAllowed($request, $user) && !isset($_REQUEST['f_article_edit'])) {
                $request->setModuleName('admin')
                    ->setControllerName('blog')
                    ->setActionName('index')
                    ->setDispatched(false);
                return;
            }
        }

        if (in_array($resource, $this->ignore)) {
            return; // ignore
        }

        $hasPriv = Saas::singleton()->hasPrivilege($resource, $action);
        if (!$hasPriv) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            /* @var $redirector Zend_Controller_Action_Helper_Redirector */
            $redirector->direct("index", "index", "admin");
        }

        $acl = Zend_Registry::get('acl');
        list($resource, $action) = $acl->getAccess($request->getControllerName(), $request->getActionName());

        if (empty($resource) || empty($action)) {
            return; // ignored by annotation
        }
        
        if ($acl->isAllowed(Zend_Registry::get('user'), $resource, $action)) {
            return; // passed
        }

        // display not allowed
        $request->setModuleName('admin')
            ->setControllerName('error')
            ->setActionName('deny')
            ->setParam('message', getGS('You are not allowed to $1 $2.',
                    $action ? $action : getGS('handle'),
                    $resource ? $resource : getGS('any resource')))
            ->setDispatched(false);
    }
}
