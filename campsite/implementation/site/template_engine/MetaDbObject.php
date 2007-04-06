<?php

/**
 * @package Campsite
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/Exceptions.php');


define('INVALID_PROPERTY_STRING', 'invalid property');
define('OF_OBJECT_STRING', 'of object');


/**
 * @package Campsite
 */
class MetaDbObject {
    //
    protected $m_dbObject = null;

    protected $m_properties = null;

    protected $m_customProperties = null;


    public function __get($p_property)
    {
        if (!$this->defined()) {
            return false;
        }

        try {
        	return $this->m_dbObject->getProperty($this->translateProperty($p_property));
        } catch (InvalidPropertyException $e) {
        	try {
		        return $this->getCustomProperty($p_property);
        	} catch (InvalidPropertyException $e) {
        		$this->trigger_invalid_property_error($p_property);
        		return null;
        	}
        }
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
        return is_object($this->m_dbObject) && $this->m_dbObject->exists();
    } // fn defined


    final public function translateProperty($p_property)
    {
        if (is_array($this->m_properties)) {
        	$property = strtolower($p_property);
        	if (!isset($this->m_properties[$property])) {
        		throw new InvalidPropertyException(get_class($this->m_dbObject), $p_property);
        	}
        	return $this->m_properties[$property];
        }
    	return $p_property;
    }


    protected function getCustomProperty($p_property)
    {
    	if (!is_array($this->m_customProperties)
    			|| !array_key_exists($p_property, $this->m_customProperties)) {
	    	throw new InvalidPropertyException(get_class($this->m_dbObject), $p_property);
    	}
    	if (!method_exists($this, $this->m_customProperties[$p_property])) {
    		throw new InvalidPropertyHandlerException(get_class($this->m_dbObject), $p_property);
    	}
    	$methodName = $this->m_customProperties[$p_property];
    	return $this->$methodName();
    }


    final protected function trigger_invalid_property_error($p_property)
    {
		CampTemplate::singleton()->trigger_error(INVALID_PROPERTY_STRING . " $p_property "
        										 . OF_OBJECT_STRING . ' ' . get_class($this->m_dbObject));

    }
}

?>
