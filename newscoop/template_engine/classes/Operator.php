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
	 * The type of the operands
	 *
	 * @var string
	 */
	private $m_type;

	/**
	 * The symbols of the operator for different output formats (SQL, PHP etc.)
	 *
	 * @var array
	 */
	private static $s_symbols = array('sql' => array('is' => '=',
                                                     'smaller_equal' => '<=',
                                                     'greater_equal' => '>=',
                                                     'smaller' => '<',
                                                     'greater' => '>',
                                                     'not' => '<>',
                                                     'like' => 'like',
													 'match'=> 'match',
                                                     'in' => 'IN'
                                               ),
                                      'php' => array('is' => '==',
                                                     'smaller_equal' => '<=',
                                                     'greater_equal' => '>=',
                                                     'smaller' => '<',
                                                     'greater' => '>',
                                                     'not' => '!='
                                               )
	                            );

	/**
	 * The list of operators corresponding to the operand types
	 *
	 * @var array
	 */
	private static $s_typeOperators = array('integer'=>array('is',
                                                             'smaller_equal',
                                                             'greater_equal',
                                                             'smaller',
                                                             'greater',
                                                             'not',
                                                             'in'
                                                       ),
                                            'string'=>array('is',
	                                                        'smaller_equal',
	                                                        'greater_equal',
	                                                        'smaller',
	                                                        'greater',
	                                                        'not',
	                                                        'like',
                                                       		'match',
                                                            'in'
	                                                  ),
                                            'boolean'=>array('is',
	                                                      'not'
	                                                ),
                                            'date'=>array('is',
	                                                      'smaller_equal',
	                                                      'greater_equal',
	                                                      'smaller',
	                                                      'greater',
	                                                      'not'
	                                                ),
                                            'datetime'=>array('is',
	                                                          'smaller_equal',
	                                                          'greater_equal',
	                                                          'smaller',
	                                                          'greater',
	                                                          'not'
	                                                    ),
                                            'time'=>array('is',
	                                                      'smaller_equal',
	                                                      'greater_equal',
	                                                      'smaller',
	                                                      'greater',
	                                                      'not'
	                                                ),
                                            'timestamp'=>array('is',
	                                                           'smaller_equal',
	                                                           'greater_equal',
	                                                           'smaller',
	                                                           'greater',
	                                                           'not'
	                                                     ),
                                            'switch'=>array('is',
	                                                        'not'
	                                                  ),
                                            'topic'=>array('is',
	                                                       'not'
	                                                 )
	                                  );

	/**
	 * The default symbol type
	 *
	 * @var string
	 */
	private static $s_defaultSymbolType = 'sql';

	/**
	 * constructor
	 *
	 * @param string $p_name
	 * @param string $p_type
	 */
	public function __construct($p_name, $p_type = 'string')
	{
	    $p_name = strtolower($p_name);
	    $p_type = strtolower($p_type);
	    if (!isset($p_type) || !array_key_exists($p_type, Operator::$s_typeOperators)) {
	        $p_type = 'string';
	    }
	    if (array_search($p_name, Operator::$s_typeOperators[$p_type]) === false) {
	        throw new InvalidOperatorException($p_name, $p_type);
	    }
		$this->m_name = $p_name;
		$this->m_type = $p_type;
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
	 * Returns the operands type.
	 *
	 * @return string
	 */
	public function getType()
	{
	    return $this->m_type;
	}

	/**
     * Returns the operator symbol.
     *
     * @param string $p_format
     *    The format used to get the symbol.
     *
     * @return string
     */
	public function getSymbol($p_format = null)
	{
        if (is_null($p_format)) {
            $p_format = Operator::$s_defaultSymbolType;
        }

        if (!array_key_exists($p_format, Operator::$s_symbols)) {
            return null;
        }

        return Operator::$s_symbols[$p_format][$this->m_name];
    }

	/**
	 * Returns an array of available operators for the given type
	 *
	 * @return array
	 */
	public static function GetOperators($p_type = 'string')
	{
	    if (!isset($p_type) || !array_key_exists($p_type, Operator::$s_typeOperators)) {
	        $p_type = 'string';
	    }

	    return Operator::$s_typeOperators[$p_type];
	}
}

?>
