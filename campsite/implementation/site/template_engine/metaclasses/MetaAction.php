<?php

class MetaAction
{
    protected $m_defined = false;

    protected $m_properties = null;


    public function takeAction()
    {
    }


    public static function CreateAction(array $p_input)
    {
        foreach ($p_input as $parameter=>$value) {
            $parameter = strtolower($parameter);
            $parameter[0] = strtoupper($parameter[0]);
            $className = 'MetaAction'.$parameter;
            $includeFile = $_SERVER['DOCUMENT_ROOT'].'/template_engine/metaclasses/'
                           . $className . '.php';
            if (file_exists($includeFile)) {
                return new $className;
            }
        }
        return new MetaAction();
    }


    public function __get($p_property)
    {
        if (!$this->defined()) {
            return null;
        }

        $p_property = $this->translateProperty($p_property);
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


    final public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    final public function __unset($p_property)
    {
        throw new InvalidFunctionException(get_class($this), '__unset');
    } // fn __set


    final public function defined()
    {
        return $this->m_defined;
    } // fn defined


    final public function translateProperty($p_property)
    {
        return strtolower($p_property);
    }


    final public function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
    	$errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        				. OF_OBJECT_STRING . ' ' . get_class($this->m_dbObject);
		CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }

}

?>