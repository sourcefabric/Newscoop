<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation\Exception;

/**
 * This exception is thrown when an attribute is missing but required.
 */
use Newscoop\Utils\Validation;

class XMLMissingAttribueException extends \Exception
{

	/** @var string */
	private $attributeName;

	public function __construct($attributeName)
	{
		Validation::notEmpty($attributeName, 'attributeName');
		$this->attributeName = $attributeName;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the attribute name that is corrupted.
	 * 
	 * @return string
	 * 		The attribute name, not null.
	 */
	function getAttributeName()
	{
		return $this->attributeName;
	}
}