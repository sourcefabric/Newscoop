<?php
/**
 * @package Campsite
 */

final class InvalidFunctionException extends Exception {
    private $m_className = null;

    private $m_methodName = null;


    /**
     * @param string $p_className
     * @param string $p_methodName
     */
    public function __construct($p_className, $p_methodName)
    {
        parent::__construct("$p_methodName() method is not available for the $p_className class", 0);
        $this->m_className = $p_className;
        $this->m_methodName = $p_methodName;
    } // fn __construct


    public function getClassName()
    {
        return $this->m_className;
    } // fn getClassName


    public function getMethodName()
    {
        return $this->m_methodName;
    } // fn getMethodName

} // class InvalidFunctionException


final class InvalidPropertyHandlerException extends Exception {
    private $m_className = null;

    private $m_propertyName = null;


    /**
     * @param string $p_className
     */
    public function __construct($p_className, $p_propertyName)
    {
        parent::__construct("No handler was assigned for property $p_propertyName in class $p_className", 0);
        $this->m_className = $p_className;
        $this->m_propertyName = $p_propertyName;
    } // fn __construct


    public function getClassName()
    {
        return $this->m_className;
    } // fn getClassName


    public function getPropertyName()
    {
        return $this->m_propertyName;
    } // fn getPropertyName

} // class InvalidPropertyHandlerException


final class InvalidObjectException extends Exception {
    private $m_className = null;


    /**
     * @param string $p_className
     */
    public function __construct($p_className)
    {
        parent::__construct("the $p_className() object is not a valid resource", 0);
        $this->m_className = $p_className;
    } // fn __construct


    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->m_className;
    } // fn getClassName

}// class InvalidObjectException


final class InvalidOperatorException extends Exception {
    private $m_operatorName = null;

    private $m_typeName = null;


    /**
     * @param string $p_operatorName
     * @param string $p_typeName
     */
    public function __construct($p_operatorName, $p_typeName)
    {
        parent::__construct("The name '$p_operatorName' is not a valid operator for the type $p_typeName.", 0);
        $this->m_operatorName = $p_operatorName;
        $this->m_typeName = $p_typeName;
    } // fn __construct


    public function getOperatorName()
    {
        return $this->m_operatorName;
    } // fn getOperatorName


    public function getTypeName()
    {
        return $this->m_typeName;
    } // fn getTypeName

}// class InvalidOperatorException


final class InvalidValueException extends Exception {
    private $m_value = null;
    private $m_type = null;


    /**
     * @param string $p_type
     */
    public function __construct($p_value, $p_type)
    {
        parent::__construct("the value $p_value is not a valid $p_type", 0);
        $this->m_value = $p_value;
        $this->m_type = $p_type;
    } // fn __construct


    public function getValue()
    {
        return $this->m_value;
    } // fn getValue


    public function getType()
    {
        return $this->m_type;
    } // fn getType

}// class InvalidValueException

?>