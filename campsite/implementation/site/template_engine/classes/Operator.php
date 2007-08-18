<?php
/**
 * @package Campsite
 */

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
     * @param string $p_type
     *    The operators set to be used to get the symbol.
     *
     * @return string
     */
	public function getSymbol($p_type = null)
	{
        if (!empty($p_type) && $p_type != 'php') {
            $operators = self::LoadOperators();
            return $operators[$p_type][$this->getName()];
        }

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

    /**
     * Loads the operators set.
     * PHP comparison operators is the default set. It is implemented
     * in every method that defines base operators in this class, that is
     * why PHP operators are not listed in $operatorsSet.
     *
     * New operators sets have to be appended here.
     *
     * @return array $operatorsSet
     *    The array containing the operators set, following this format:
     *        array(
     *            'set name' => array(
     *                'operator name' => 'operator symbol'
     *                ...
     */
    private static function LoadOperators()
    {
        $operatorsSet = array(
            'sql' => array(
                'is' => '=',
                'equal_smaller' => '<=',
                'equal_greater' => '>=',
                'smaller' => '<',
                'greater' => '>',
                'not' => '<>'
                )
            );

        return $operatorsSet;
    }

}

?>