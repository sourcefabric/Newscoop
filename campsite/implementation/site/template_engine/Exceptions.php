<?php
/**
 * @package Campsite
 */

final class InvalidFunctionException extends Exception {
	private $m_className = null;

	private $m_methodName = null;


    public function __construct($p_className, $p_method)
    {
        parent::__construct("$p_method() method is not available for the $p_className class", 0);
        $this->m_className = $p_className;
        $this->m_methodName = $p_method;
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

?>