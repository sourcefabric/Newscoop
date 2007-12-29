<?php

define('ACTION_OK', 0);


class MetaAction
{
	/**
	 * True if an action was set up; this member is set to false by the
	 * base class. The specialized class must set it to true.
	 *
	 * @var bool
	 */
    protected $m_defined = false;

    
    /**
     * Action properties
     *
     * @var array
     */
    protected $m_properties = null;
    
    
    /**
     * Stores the error data
     *
     * @var PEAR_Error
     */
    protected $m_error = null;


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        return false;
    }


    /**
     * Returns the error code of the action. Returns 0 on success,
     * PEAR_Error object on error.
     *
     * @return mixed
     */
    public function getError()
    {
    	return $this->m_error;
    }


    /**
     * Factory method; creates an object specialized from MetaAction
     * based on the given input.
     *
     * @param array $p_input
     * @return MetaAction
     */
    public static function CreateAction(array $p_input)
    {
        foreach ($p_input as $parameter=>$value) {
            $parameter = strtolower($parameter);
            $parameter[0] = strtoupper($parameter[0]);
            $className = 'MetaAction'.$parameter;
            $includeFile = $_SERVER['DOCUMENT_ROOT'].'/template_engine/metaclasses/'
                           . $className . '.php';
            if (file_exists($includeFile)) {
                return new $className($p_input);
            }
        }
        return new MetaAction();
    }


    /**
     * Returns the value of the given property; throws
     * InvalidPropertyHandlerException if the property didn't exist.
     *
     * @param string $p_property
     * @return mixed
     */
    public function __get($p_property)
    {
        if (!$this->defined()) {
            return null;
        }

        $p_property = MetaAction::TranslateProperty($p_property);
    	if (!is_array($this->m_properties)
    			|| !array_key_exists($p_property, $this->m_properties)) {
            $this->trigger_invalid_property_error($p_property);
    	}
    	if (!method_exists($this, $this->m_properties[$p_property])) {
    		throw new InvalidPropertyHandlerException(get_class($this), $p_property);
    	}
    	$methodName = $this->m_properties[$p_property];
    	return $this->$methodName();
    } // fn __get


    /**
     * Throws InvalidFunctionException; action properties can not be modified.
     *
     * @param string $p_property
     * @param mixed $p_value
     */
    final public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    /**
     * Throws InvalidFunctionException; action properties can not be modified.
     *
     * @param string $p_property
     */
    final public function __unset($p_property)
    {
        throw new InvalidFunctionException(get_class($this), '__unset');
    } // fn __set


    /**
     * Returns true if an action was set up.
     *
     * @return bool
     */
    final public function defined()
    {
        return $this->m_defined;
    } // fn defined


    /**
     * Converts the property name to the standard way of naming properties.
     *
     * @param string $p_property
     * @return string
     */
    public static function TranslateProperty($p_property)
    {
        return strtolower($p_property);
    }


    /**
     * Registers an error message in the CampTemplate singleton object.
     *
     * @param string $p_property
     * @param mixed $p_smarty
     */
    final public function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
    	$errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        				. OF_OBJECT_STRING . ' ' . get_class($this->m_dbObject);
		CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }

}

?>