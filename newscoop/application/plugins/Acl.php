<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

use Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\Parser,
    Doctrine\Common\Cache\ArrayCache;

/**
 * Acl controller plugin
 */
class Application_Plugin_Acl extends Zend_Controller_Plugin_Abstract
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
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return;
        }

        $resource = $request->getControllerName();
        $action = $request->getActionName();

        if (in_array($resource, $this->ignored)) {
            return; // ignore
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
