<?php
/**
 * @package Campsite
 */

final class InvalidPropertyException extends Exception {
	private $m_className = null;

	private $m_property = null;

    /**
     * @param string $p_className
     */
    public function __construct($p_className, $p_property)
    {
        parent::__construct("$p_property property is not available for the $p_className class", 0);
        $this->m_className = $p_className;
        $this->m_property = $p_property;
    } // fn __construct


    public function getClassName()
    {
    	return $this->m_className;
    }


    public function getProperty()
    {
    	return $this->m_property;
    }

} // class InvalidPropertyException

?>