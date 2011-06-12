<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Utils\Validation;
use Newscoop\Service\IErrorHandler;

/**
 * Provides the services implementation for the Outputs.
 */
class ErrorHandlerOnLog implements IErrorHandler
{

	const PLACE_HOLDER = '$'; 
	const REGEX_PLACE_HOLDER = '([\$]{1}[1-9]+)';
	
	/* --------------------------------------------------------------- */

	function warning($key)
	{
		Validation::notEmpty($key, 'key');
		syslog(LOG_WARNING, $this->compile($key, array_slice(func_get_args(), 1)));
	}
	
	function error($key)
	{
		Validation::notEmpty($key, 'key');
		syslog(LOG_ERR, $this->compile($key, array_slice(func_get_args(), 1)));
	}

	/* --------------------------------------------------------------- */
	
	protected function compile($key, array $params)
	{
		$matches = array();
		preg_match_all(self::REGEX_PLACE_HOLDER, $key, $matches);
		$count = count($matches[0]);
		
		if($count != count($params)){
			throw new \Exception("Unmatched parameters, expected '$count' based on the placeholders an got '.count($params).'.");
		}
		
		$txt = $key;
		$k = 1;
		foreach ($params as $param){			
			$txt = str_replace(self::PLACE_HOLDER.$k++, $param, $txt);
		}
		return $txt;
	}
}