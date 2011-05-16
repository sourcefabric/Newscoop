<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\Service;

/**
 * Provides error handling.
 */
interface IErrorHandler
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * Handle the warning. The key of the warning needs to be mapped as constant
	 * see previous implementations.
	 *
	 * @param string $key
	 * 		The key of the waning, which uniquelly identifies it, not null.
	 */
	function warning($key);

	/**
	 * Handle the error. The key of the error needs to be mapped as constant
	 * see previous implementations.
	 *
	 * @param string $key
	 * 		The key of the error, which uniquelly identifies it, not null.
	 */
	function error($key);

}