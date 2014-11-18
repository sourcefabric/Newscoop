<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;

/**
 * Provides the output based on the client agent.
 * 
 * @ORM\Entity
 * @ORM\Table(name="output")
 */
class Output extends AbstractEntity
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * @ORM\Column(name="name", unique=TRUE, nullable=FALSE)
	 * @var string
	 */
	protected $name;

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
	 * @return Output
	 *		This object for chaining purposes.
	 */
	public function setName($name)
	{
		Validation::notEmpty($name, 'name');
		$this->name = $name;
		return $this;
	}

}
