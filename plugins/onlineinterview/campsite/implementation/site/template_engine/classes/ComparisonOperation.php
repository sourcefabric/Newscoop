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
		return $this->m_rightOperand;
	}
}


?>
