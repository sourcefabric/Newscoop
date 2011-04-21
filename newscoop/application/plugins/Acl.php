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
    const ANNOTATION_NS = 'Newscoop\Annotations\\';

    const ANNOTATION_CLASS = 'Newscoop\Annotations\Acl';

    /** @var array */
    private $ignored = array(
        'auth',
        'error',
        'legacy',
        'login.php',
        'password_recovery.php',
        'password_check_token.php',
    );

    /** @var Zend_Acl */
    private $acl;

    /** @var Zend_Acl_Role_Interface */
    private $role;

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return;
        }

        $resource = $request->getControllerName();
        $action = $request->getActionName();

        if (in_array($resource, $this->ignored)) { // ignore
            return;
        }

        // get annotation reader
        $reader = new AnnotationReader;
        $reader->setAutoloadAnnotations(true);
        $reader->setDefaultAnnotationNamespace(self::ANNOTATION_NS);

        // get annotations for class/method
        $annotations = array();
        $reflection = $this->getReflection($resource);
        if ($reflection) {
            $annotations[] = $reader->getClassAnnotation($reflection, self::ANNOTATION_CLASS);
            $method = $this->formatName($action, TRUE) . 'Action';
            if ($reflection->hasMethod($method)) {
                $reflection = $reflection->getMethod($method);
                $annotations[] = $reader->getMethodAnnotation($reflection, self::ANNOTATION_CLASS);
            }
        }

        // override resource/action with annotations
        foreach ($annotations as $annotation) {
            if (isset($annotation->ignore)) { // ignore acl
                return;
            }

            $resource = isset($annotation->resource) ? $annotation->resource : $resource;
            $action = isset($annotation->action) ? $annotation->action : $action;
        }

        if ($resource === 'null') { // set proper type
            $resource = null;
        }

        if ($action === 'null') { // set proper type
            $action = null;
        }
        
        $acl = $this->getAcl();
        $role = $this->getRole();
        try { // check access
            if ($acl->isAllowed($role, $resource, $action)) {
                return;
            }
        } catch (Zend_Acl_Exception $e) { // ignore not found resources - old code
            return;
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

    /**
     * Get Acl object
     *
     * @return Zend_Acl
     */
    private function getAcl()
    {
        if ($this->acl === NULL) {
            $this->acl = Zend_Registry::get('acl')->getAcl();
        }

        return $this->acl;
    }

    /**
     * Get Role object
     *
     * @return Zend_Acl_Role_Interface
     */
    private function getRole()
    {
        if ($this->role === NULL) {
            $this->role = Zend_Registry::get('user')->getRole();
        }

        return $this->role;
    }

    /**
     * Format name
     *
     * @param string $name
     * @param bool $isAction
     * @return string
     */
    private function formatName($name, $isAction = FALSE)
    {
        $name_ary = explode('-', $name);
        $name_ary = array_map('strtolower', $name_ary);
        $name_ary = array_map('ucfirst', $name_ary);
        $name = implode('', $name_ary);
        return $isAction ? lcfirst($name) : $name;
    }

    /**
     * Get reflection
     *
     * @param string $resource
     * @return ReflectionClass|null
     */
    private function getReflection($resource)
    {
        $controller = $this->formatName($resource) . 'Controller';
        $front = Zend_Controller_Front::getInstance();
        foreach ($front->getControllerDirectory() as $dir) {
            if (file_exists("$dir/$controller.php")) {
                require_once "$dir/$controller.php";
                return new ReflectionClass("Admin_$controller");
            }
        }

        return null;
    }
}
