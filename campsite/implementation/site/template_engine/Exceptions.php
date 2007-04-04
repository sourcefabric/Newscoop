<?php
/**
 * @package Campsite
 */

final class InvalidFunctionException extends Exception {
	private $m_className = null;

	private $m_methodName = null;


    public function __construct($p_className, $p_methodName)
    {
        parent::__construct("$p_methodName() method is not available for the $p_className class", 0);
        $this->m_className = $p_className;
        $this->m_methodName = $p_methodName;
    } // fn __construct


    public function getClassName()
    {
    	return $this->m_className;
    }


    public function getMethodName()
    {
    	return $this->m_methodName;
    }

} // class InvalidFunctionException


final class InvalidPropertyHandlerException extends Exception {
	private $m_className = null;

	private $m_propertyName = null;


    public function __construct($p_className, $p_propertyName)
    {
        parent::__construct("No handler was assigned for property $p_propertyName in class $p_className", 0);
        $this->m_className = $p_className;
        $this->m_propertyName = $p_propertyName;
    } // fn __construct


    public function getClassName()
    {
    	return $this->m_className;
    }


    public function getPropertyName()
    {
    	return $this->m_propertyName;
    }

} // class InvalidPropertyHandlerException

?>