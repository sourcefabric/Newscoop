<?php
/**
 * @package Resource
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

use Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\Parser,
    Doctrine\Common\Cache\ArrayCache,
    Resource\Acl\StorageInterface,
    Resource\Acl\RuleInterface;

/**
 * Acl Zend application resource
 */
class Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    const ANNOTATION = 'Resource\Acl\Annotation\Acl';

    const PARENTS_METHOD = 'getGroups';

    const CACHE_NAMESPACE = '_resource_acl';

    const CACHE_TTL = 300;

    /** @var Doctrine\Common\Annotations\AnnotationReader */
    private $reader;

    /** @var Resource\Acl\StorageInterface */
    private $storage;

    /** @var array */
    private $resources;

    /** @var array */
    private $access;

    /**
     * Init acl
     */
    public function init()
    {
        Zend_Registry::set('acl', $this);
        return $this;
    }

    /**
     * Check access
     *
     * @param Zend_Acl_Role_Interface $role
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function isAllowed(Zend_Acl_Role_Interface $role, $resource, $action)
    {
        $acl = $this->getAcl($role);
        try {
            return $acl->isAllowed($role, $resource, $action);
        } catch (Zend_Acl_Exception $e) { // resource not found
            return true;
        }
    }

    /**
     * Get acl for role
     *
     * @param Zend_Acl_Role_Interface $role
     * @return Zend_Acl
     */
    public function getAcl(Zend_Acl_Role_Interface $role)
    {
        $acl = new Zend_Acl;

        // set resources
        $resources = $this->getResources();
        foreach (array_keys($resources) as $resource) {
            $acl->addResource($resource);
        }

        // get role parents if possible
        $method = self::PARENTS_METHOD;
        $parents = NULL;
        if (method_exists($role, $method)) {
            foreach ($role->$method() as $parent) {
                $parents[] = $parent;
                $acl->addRole($parent);
                $this->addRules($acl, $parent);
            }
        }

        // set role
        $acl->addRole($role, $parents);
        $this->addRules($acl, $role);

        return $acl;
    }

    /**
     * Add role rules
     * 
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $role
     * @return void
     */
    private function addRules(Zend_Acl $acl, Zend_Acl_Role_Interface $role)
    {
        foreach ($this->getStorage()->getRules($role) as $rule) {
            if (!$rule instanceof Resource\Acl\RuleInterface) {
                throw new InvalidArgumentException;
            }

            $type = $rule->getType();
            $acl->$type($role, $rule->getResource(), $rule->getAction());
        }
    }

    /**
     * Get application resources
     *
     * @return array
     */
    public function getResources()
    {
        $this->scan();

        return $this->resources;
    }

    /**
     * Scan for resources/actions
     *
     * @return void
     */
    private function scan()
    {
        $options = $this->getOptions();

        $cache = false;
        if (!empty($options['cache'])) { // use cache
            $cache = new $options['cache'];

            if ($cache->contains(self::CACHE_NAMESPACE)) {
                list($this->resources, $this->access) = json_decode($cache->fetch(self::CACHE_NAMESPACE), TRUE);
                return;
            }
        }

        $front = Zend_Controller_Front::getInstance();
        $paths = $front->getControllerDirectory();
        $path = $paths['admin'];

        $resources = $access = array();

        // load resources from file if any
        $file = APPLICATION_PATH . '/configs/resources.ini';
        if (file_exists($file)) {
            $config = new Zend_Config_Ini($file);
            $resources = $config->toArray();
        }

        // scan controllers
        $reader = $this->getAnnotationReader();
        foreach (glob("$path/*Controller.php") as $controllerFile) {
            require_once($controllerFile);
            $controller = 'Admin_' . current(explode('.', basename($controllerFile)));
            $reflection = new ReflectionClass($controller);

            $resource = $this->formatName($controller);
            $defaultAction = NULL;

            $controllerKey = $resource;
            $access[$controllerKey] = array();

            // process annotations
            $annotation = $reader->getClassAnnotation($reflection, self::ANNOTATION);
            if ($annotation !== NULL) {
                if (!empty($annotation->ignore)) { // ignored class
                    continue;
                }

                if (!empty($annotation->resource)) {
                    $resource = $this->formatName($annotation->resource);
                }

                if (!empty($annotation->action)) {
                    $defaultAction = $this->formatName($annotation->action);
                }
            }

            // get actions
            $actions = array();
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (!preg_match('/Action$/', $method->getName())) {
                    continue;
                }

                $action = $this->formatName($method->getName());
                $target = $resource;

                $methodKey = $action;
                $access[$controllerKey][$methodKey] = array();

                // process annotations
                $annotation = $reader->getMethodAnnotation($method, self::ANNOTATION);
                if ($annotation !== NULL) {
                    if (!empty($annotation->ignore)) {
                        continue;
                    }

                    if (!empty($annotation->resource)) {
                        $target = $this->formatName($annotation->resource);
                    }

                    if (!empty($annotation->action)) {
                        $action = $this->formatName($annotation->action);
                    }
                } elseif ($defaultAction !== NULL) {
                    $action = $defaultAction;
                }

                // add action to target resource
                if (!isset($resources[$target])) {
                    $resources[$target] = array($action);
                } elseif (!in_array($action, $resources[$target])) {
                    $resources[$target][] = $action;
                }

                if ($target) {
                    $access[$controllerKey][$methodKey] = array($target, $action);
                }
            }
        }

        $this->resources = $resources;
        $this->access = $access;

        if ($cache) {
            $cache->save(self::CACHE_NAMESPACE, json_encode(array($resources, $access)), self::CACHE_TTL);
        }
    }

    /**
     * Get access
     *
     * @param string $controller
     * @param string $action
     * @return array|NULL
     */
    public function getAccess($controller, $action)
    {
        $this->scan();

        if (isset($this->access[$controller][$action])) {
            return $this->access[$controller][$action];
        }

        return NULL;
    }

    /**
     * Get resource actions
     *
     * @param string $resource
     * @return array
     */
    public function getActions($resource = '')
    {
        $resources = $this->getResources();

        $actions = array();
        if (!empty($resource)) { // resource specific
            $actions = isset($resources[$resource]) ? $resources[$resource] : array();
        } else {
            foreach ($resources as $resource => $resourceActions) {
                $actions = array_merge($actions, $resourceActions);
            }
        }

        $actions = array_unique($actions);
        sort($actions);
        return $actions;
    }

    /**
     * Get annotation reader
     *
     * @return Doctrine\Common\Annotations\AnnotationReader
     */
    private function getAnnotationReader()
    {
        if ($this->reader === NULL) {
            $namespaces = explode('\\', self::ANNOTATION);
            array_pop($namespaces);
            $namespace = implode('\\', $namespaces) . '\\';

            $this->reader = new AnnotationReader;
            $this->reader->setAutoloadAnnotations(true);
            $this->reader->setDefaultAnnotationNamespace($namespace);
        }

        return $this->reader;
    }

    /**
     * Set acl storage
     *
     * @param Resource\Acl\StorageInterface
     * @return Resource_Acl
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Get acl storage
     *
     * @return Resource\Acl\StorageInterface
     */
    private function getStorage()
    {
        return $this->storage;
    }

    /**
     * Format name for resource/action
     *
     * @param string $name
     * @return string
     */
    private function formatName($name)
    {
        $name = str_replace(array(
            'Admin_',
            'Controller',
            'Action',
        ), array(
            '',
        ), $name);

        $parts = array();
        foreach (preg_split('/([A-Z][a-z]+)/', $name, 0, PREG_SPLIT_DELIM_CAPTURE) as $part) {
            if (empty($part)) {
                continue;
            }

            $parts[] = strtolower($part);
        }

        return implode('-', $parts);
    }
}
