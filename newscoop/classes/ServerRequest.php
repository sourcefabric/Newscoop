<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/User.php';
require_once dirname(__FILE__) . '/SecurityToken.php';

/**
 * Server request class
 */
class ServerRequest
{
    const ERROR_NOT_CALLABLE = 1;
    // note that the value of the ERROR_SECURITY_TOKEN constant is used at the /js/admin.js file too!
    const ERROR_SECURITY_TOKEN = 2;
    const ERROR_PERMISSION = 3;

    /** @var callback */
    private $callback = NULL;

    /** @var string */
    private $callable_name = '';

    /** @var array */
    private $args = array();

    /** @var array */
    private $rules = array();

    /**
     * @param callback $callback
     * @param array $args
     */
    public function __construct($callback, $args = array())
    {
        $this->callback = $callback;
        $this->args = (array) $args;

        // check if callable
        if (!is_callable($this->callback, false, $this->callable_name)) {
            throw new InvalidArgumentException(
                "Callback $1 is not callable.", array('$1' => $this->callable_name),
                self::ERROR_NOT_CALLABLE
            );
        }
    }

    /**
     * Allow certain callback
     * @param string $callable_name
     * @param string $permission_required
     * @return ServerRequest
     */
    public function allow($callable_name, $permission_required = '')
    {
        $this->rules[(string) $callable_name] = (string) $permission_required;
        return $this;
    }

    /**
     * Execute callback
     * @return mixed
     */
    public function execute()
    {
        // token check
        if (!$this->checkToken()) {
            throw new InvalidArgumentException(
                'Invalid security token.',
                self::ERROR_SECURITY_TOKEN
            );
        }

        // authorisation check
        if (!$this->checkPermission()) {
            throw new LogicException(
                'Access denied.',
                self::ERROR_PERMISSION
            );
        }

        // function
        if (function_exists($this->callable_name)) {
            return call_user_func_array($this->callable_name, $this->args);
        }

        list($class, $method) = explode('::', $this->callable_name);
        $methodRef = new ReflectionMethod($class, $method);

        // static method
        if ($methodRef->isStatic()) {
            return call_user_func_array($this->callable_name, $this->args);
        }

        // object method - create object instance
        $classRef = new ReflectionClass($class);
        $cargsNum = $classRef->getConstructor()
            ->getNumberOfParameters();
        $cargs = array_slice($this->args, 0, $cargsNum);
        $instance = $classRef->newInstanceArgs($cargs);

        // call instance method
        $args = array_slice($this->args, $cargsNum);
        return $methodRef->invokeArgs($instance, $args);
    }

    /**
     * Check permission
     * @return bool
     */
    public function checkPermission()
    {
        global $g_user;

        if (!isset($this->rules[$this->callable_name])) {
            return FALSE;
        }

        $permission = $this->rules[$this->callable_name];
        if (!empty($permission) && !$g_user->hasPermission($permission)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Check token
     * @return bool
     */
    public function checkToken()
    {
        return SecurityToken::isValid();
    }
}
