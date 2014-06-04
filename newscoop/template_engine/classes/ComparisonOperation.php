<?php

require_once('Operator.php');


/**
 * Defines a comparison operation; holds data for the left and right
 * operands and for the operator.
 */
class ComparisonOperation
{
	/**
	 * left operand
	 *
	 * @var mix
	 */
	private $m_leftOperand;

	/**
	 * operator
	 *
	 * @var object of type Operator
	 */
	private $m_operator;

	/**
	 * right operand
	 *
	 * @var mix
	 */
	private $m_rightOperand;

	/**
	 * constructor
	 *
	 * @param mix $p_leftOperand
	 * @param object of type Operator $p_operator
	 * @param mix $p_rightOperand
	 */
	public function __construct($p_leftOperand, $p_operator, $p_rightOperand)
	{
		$this->m_leftOperand = $p_leftOperand;
		$this->m_operator = $p_operator;
		$this->m_rightOperand = $p_rightOperand;
	}

	/**
	 * Returns the left operand
	 *
	 * @return mix
	 */
	public function getLeftOperand()
	{
		return $this->m_leftOperand;
	}

	/**
	 * Returns the operator
	 *
	 * @return object of type Operator
	 */
	public function getOperator()
	{
		return $this->m_operator;
	}

	/**
	 * Returns the right operand
	 *
	 * @return mix
	 */
	public function getRightOperand()
	{
	    // some values have to be computed
	    switch (strtolower($this->m_rightOperand)) {
	        
	       case 'now()': 
	           $this->m_rightOperand = strftime('%Y-%m-%d %H:%M:%S');
	       break;
	       
	       case 'curdate()':
	           $this->m_rightOperand = strftime('%Y-%m-%d');    
	       break;
	       
	       case 'curtime()':
	           $this->m_rightOperand = strftime('%H:%M:%S');
	       break;
	       
	       case 'current()':
	           // this value indicates that the left operand have to compared with the value from current context
	           // e.g. language_number is current()
	           
	           $Context = CampTemplate::singleton()->context();
	           $object = strtolower($this->m_leftOperand);
	           
	           switch ($object) {
	               
	               case 'language':
	               case 'publication':
	               case 'issue':
	               case 'section':
	               case 'article': 
	                   $this->m_rightOperand = $Context->$object->number;
	               break; 
	               
	               case 'publication':   
	                   $this->m_rightOperand = $Context->$object->identifier;
	               break;                     
	           }
	       break; 
	    }
	    
		return $this->m_rightOperand;
	}

    public function __toString()
    {
        return $this->getLeftOperand() .'_'. $this->getOperator()->getName() .'_'. $this->getRightOperand();
    }
}


?>