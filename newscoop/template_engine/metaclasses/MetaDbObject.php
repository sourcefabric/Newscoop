<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/Exceptions.php');

/**
 * @package Campsite
 */
class MetaDbObject {
    /**
     * Pointer to the database access class instantiation
     * @var DatabaseObject
     */
    protected $m_dbObject = null;

    /**
     * Array of meta class properties
     * @var array
     */
    protected $m_properties = null;

    /**
     * Array of meta class custom properties
     * @var array
     */
    protected $m_customProperties = null;

    /**
     * The name of the method used to retrieve a property value
     * @var string
     */
    protected $m_getPropertyMethod = 'getProperty';

    /**
     * Array of fields for which the HTML filter is not applied.
     * @var array
     */
    protected $m_skipFilter = array();


    /**
     * Returns true if the current object is the same type as the given
     * object then has the same value.
     * @param mix $p_otherObject
     * @return boolean
     */
    public function same_as($p_otherObject)
    {
    	return get_class($this) == get_class($p_otherObject)
    	&& (is_null($this->m_dbObject) && is_null($p_otherObject->m_dbObject)
    	|| $this->m_dbObject->sameAs($p_otherObject->m_dbObject));
    } // fn same_as


    public function __toString()
    {
    	$className = get_class($this);
    	if (strncasecmp($className, 'Meta', 4) == 0) {
    		$className = strtolower(substr($className, 4));
    	}
    	CampTemplate::trigger_error("Invalid use of object of type '$className'. Use \$campsite->${className}->[property_name] to display a property of this object.");
    	return null;
    }


    static public function htmlFilter($p_text)
    {
    	return str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $p_text);
    }


    public function __get($p_property)
    {
        if (!$this->defined()) {
            return null;
        }
        $property = $this->translateProperty($p_property);

        try {
            if (array_search($property, $this->m_properties)) {
                $methodName = $this->m_getPropertyMethod;
                $propertyValue = $this->m_dbObject->$methodName($property);
            } elseif (array_key_exists($property, $this->m_customProperties)) {
                $propertyValue = $this->getCustomProperty($property);
            } else {
                $this->trigger_invalid_property_error($p_property);
                return null;
            }
            if (empty($propertyValue) || !is_string($propertyValue) || is_numeric($propertyValue)) {
            	return $propertyValue;
            }
            if (count($this->m_skipFilter) == 0 || !in_array(strtolower($p_property), $this->m_skipFilter)) {
            	$propertyValue = self::htmlFilter($propertyValue);
            }
            return $propertyValue;
        } catch (InvalidPropertyException $e) {
            $this->trigger_invalid_property_error($p_property);
        	return null;
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


    /**
     * Returns true if the article has a property with the given name
     *
     * @return bool
     */
    public function has_property($p_property) {
        $p_property = $this->translateProperty($p_property);
        return (is_array($this->m_properties)
        && array_search($p_property, $this->m_properties))
        || (is_array($this->m_customProperties)
        && array_key_exists($p_property, $this->m_customProperties));
    }



    /**
     * Returns true if the current object was initialized
     *
     * @return boolean
     */
    final public function defined()
    {
        return is_object($this->m_dbObject) && $this->m_dbObject->exists();
    } // fn defined


    final public function translateProperty($p_property)
    {
        $p_property = strtolower($p_property);
        if (is_array($this->m_properties) && isset($this->m_properties[$p_property])) {
        	return $this->m_properties[$p_property];
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


    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
    	$errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        				. OF_OBJECT_STRING . ' ' . get_class($this->m_dbObject);
		CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }


    final public function trigger_invalid_value_error($p_property, $p_value, $p_smarty = null)
    {
    	$errorMessage = INVALID_VALUE_STRING . " $p_value "
        				. OF_PROPERTY_STRING . " $p_property "
        				. OF_OBJECT_STRING . ' ' . get_class($this->m_dbObject);
		CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }
}

?>