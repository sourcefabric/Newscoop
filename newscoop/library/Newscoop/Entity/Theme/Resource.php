<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Theme;

use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;
use Newscoop\Entity\Theme;

/**
 * Provides the container of the theme data.
 */
class Resource extends Entity
{

	/** @var string  */
	private $name;

	/** @var string  */
	private $path;

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

	/* --------------------------------------------------------------- */

	/**
	 * Provides the path of the resource.
	 *
	 * @return string
	 *		The path of the resource.
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the path of the resource.
	 *
	 * @param string $path
	 *		The path of the resource.
	 *
	 * @return Newscoop\Entity\Theme\Resource
	 *		This object for chaining purposes.
	 */
	public function setPath($path)
	{
		Validation::notEmpty($path, 'path');
		$this->path = $path;
		return $this;
	}
}