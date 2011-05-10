<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Utils;


/**
 * Utility class that provides methods for validating parameters.
 */
class Validation
{
	/* --------------------------------------------------------------- */

	/**
	 * Validates if the provided parameter is null.
	 * Will throw exception if this is the case.
	 *
	 * @param mixed $parameter
	 *		The parameter to check for nullity.
	 * @param string $name
	 *		The parameter name used for displaying the exception default 'unknown'.
	 */
	public static function notNull($parameter, $name='unknown')
	{
		if(is_null($parameter)){
			throw new \Exception("Please provide a value for the parameter '$name'.");
		}
	}

	/**
	 * Validates if the provided parameter is null or empty.
	 * Will throw exception if this is the case.
	 *
	 * @param mixed $parameter
	 *		The parameter to check for nullity or empty.
	 * @param string $name
	 *		The parameter name used for displaying the exception default 'unknown'.
	 * @throws InvalidArgumentException
	 */
	public static function notEmpty($parameter, $name='unknown')
	{
		if(is_null($parameter)){
			throw new \Exception("Please provide a value for the parameter '$name'.");
		} else if(is_string($parameter) && trim($parameter) == ''){
			throw new \Exception("Please provide a none empty value for the parameter '$name'.");
		}
	}

	/* --------------------------------------------------------------- */

	private function __construct() {}

}
