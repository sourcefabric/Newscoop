<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;
use Newscoop\Entity\Theme;

/**
 * Provides the output based on the client agent.
 * 
 * @Entity
 * @Table(name="output")
 */
class Output extends Entity
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * @Column(name="name", unique=TRUE, nullable=FALSE)
	 * @var string
	 */
	private $name;

	/* --------------------------------------------------------------- */

	/**
	 * Provides the name of the theme resource, must be a user frendly name used for displaying it on the UI.
	 *
	 * @return string
	 *		The name of the theme resource.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the name of the theme resource, must be a user frendly name used for displaying it on the UI.
	 *
	 * @param string $name
	 *		The name of the theme resource, must not be null or empty.
	 *
	 * @return Newscoop\Entity\Theme\Resource
	 *		This object for chaining purposes.
	 */
	public function setName($name)
	{
		Validation::notEmpty($name, 'name');
		$this->name = $name;
		return $this;
	}

}