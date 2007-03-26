<?php

/**
 * @package Campsite
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/Exceptions.php');


/**
 * @package Campsite
 */
class MetaDbObject {
    //
    protected $m_dbObject = null;


    public function __get($p_property)
    {
        if (!$this->defined()) {
            return false;
        }
        return $this->m_dbObject->getProperty($p_property);
    } // fn __get


    final public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    final public function defined()
    {
        return is_object($this->m_dbObject) && $this->m_dbObject->exists();
    } // fn defined

}

?>
