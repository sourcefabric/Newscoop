<?php

/**
 * Holds metadata of an operator.
 */
class Operator
{
	/**
	 * The name of the operator
	 *
	 * @var string
	 */
	private $m_name;

	/**
	 * The symbol of the operator
	 *
	 * @var string
	 */
	private $m_symbol;

	/**
	 * constructor
	 *
	 * @param string $p_name
	 * @param string $p_symbol
	 */
	public function __construct($p_name, $p_symbol)
	{
		$this->m_name = $p_name;
		$this->m_symbol = $p_symbol;
	}

	/**
	 * Returns the operator name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->m_name;
	}

	/**
	 * Returns the operator symbol.
	 *
	 * @return string
	 */
	public function getSymbol()
	{
		return $this->m_symbol;
	}

	/**
	 * Static method; returns an operator object that defines the
	 * equality operation.
	 *
	 * @return object
	 */
	static function Equal()
	{
		return new Operator('is', '==');
	}

	/**
	 * Static method; returns an operator object that defines the
	 * operation 'equal or smaller'.
	 *
	 * @return object
	 */
	static function EqualSmaller()
	{
		return new Operator('equal_smaller', '<=');
	}

	/**
	 * Static method; returns an operator object that defines the
	 * operation 'equal or greater'.
	 *
	 * @return object
	 */
	static function EqualGreater()
	{
		return new Operator('equal_greater', '>=');
	}

	/**
	 * Static method; returns an operator object that defines the
	 * operation 'smaller'.
	 *
	 * @return object
	 */
	static function Smaller()
	{
		return new Operator('smaller', '<');
	}

	/**
	 * Static method; returns an operator object that defines the
	 * operation 'greater'.
	 *
	 * @return object
	 */
	static function Greater()
	{
		return new Operator('greater', '>');
	}

	/**
	 * Static method; returns an operator object that defines the
	 * operation 'not equal'.
	 *
	 * @return object
	 */
	static function NotEqual()
	{
		return new Operator('not', '!=');
	}
}

?>