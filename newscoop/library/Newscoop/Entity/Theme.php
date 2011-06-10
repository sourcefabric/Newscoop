<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\ThemeRepository")
 * @Table(name="Theme")
 */
class Theme extends Entity
{

	/** @var string  */
	private $path;

	/** @var string  */
	private $name;

	/** @var string  */
	private $designer;

	/** @var string  */
	private $version;

	/** @var string  */
	private $minorNewscoopVersion;

	/** @var string  */
	private $description;

	/* --------------------------------------------------------------- */

	/**
	 * Provides the path of the theme.
	 *
	 * @return string
	 *		The path of the theme.
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the path of the theme.
	 *
	 * @param string $path
	 *		The path of the theme, must not be null or empty.
	 *
	 * @return Newscoop\Entity\Theme
	 *		This object for chaining purposes.
	 */
	public function setPath($path)
	{
		Validation::notEmpty($path, 'path');
		$this->path = str_replace('\\', '/', $path);
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the name of the theme, must be a user frendly name used for displaying it on the UI.
	 *
	 * @return string
	 *		The name of the theme.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the name of the theme, must be a user frendly name used for displaying it on the UI.
	 *
	 * @param string $name
	 *		The name of the theme, must not be null or empty.
	 *
	 * @return Newscoop\Entity\Theme
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
	 * Provides the designer name of the theme.
	 *
	 * @return string
	 *		The designer name of the theme.
	 */
	public function getDesigner()
	{
		return $this->designer;
	}

	/**
	 * Set the designer name of the theme.
	 *
	 * @param string $designer
	 *		The designer name of the theme, must not be null or empty.
	 *
	 * @return Newscoop\Entity\Theme
	 *		This object for chaining purposes.
	 */
	public function setDesigner($designer)
	{
		Validation::notEmpty($designer, 'designer');
		$this->designer = $designer;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the version of the theme, this has to be a whell formated version name like '1.3'.
	 *
	 * @return string
	 *		The version of the theme.
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Set the version of the theme, this has to be a whell formated version name like '1.3'.
	 *
	 * @param string $version
	 *		TThe version of the theme, must not be null or empty.
	 *
	 * @return Newscoop\Entity\Theme
	 *		This object for chaining purposes.
	 */
	public function setVersion($version)
	{
		Validation::notEmpty($version, 'version');
		$this->version = $version;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the minimum newscoop version for this theme, this has to be a whell formated version name like '3.6'.
	 *
	 * @return string
	 *		The minimum newscoop version of the theme.
	 */
	public function getMinorNewscoopVersion()
	{
		return $this->minorNewscoopVersion;
	}

	/**
	 * Set the minimum newscoop version for this theme, this has to be a whell formated version name like '3.6'.
	 *
	 * @param string $minorNewscoopVersion
	 *		The minimum newscoop version of the theme, must not be null or empty.
	 *
	 * @return Newscoop\Entity\Theme
	 *		This object for chaining purposes.
	 */
	public function setMinorNewscoopVersion($minorNewscoopVersion)
	{
		Validation::notEmpty($minorNewscoopVersion, 'minorNewscoopVersion');
		$this->minorNewscoopVersion = $minorNewscoopVersion;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the desciption of the theme.
	 *
	 * @return string
	 *		The description of the theme.
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set the desciption of the theme.
	 *
	 * @param string $description
	 *		The description of the theme, must not be null or empty.
	 *
	 * @return Newscoop\Entity\Theme
	 *		This object for chaining purposes.
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/* --------------------------------------------------------------- */

	public function isInstalled()
	{
	    return true;
	}
	
    public function getInstalledVersion()
	{
	    return 1;
	}
	
	public function toObject()
	{
	    return (object) array
	    ( 
	    	"id"          => (int) $this->getId(), 
	        "name"        => (string) $this->getName(), 
	        "description" => (string) $this->getDescription(), 
	        "designer"    => (string) $this->getDesigner(), 
	        "path"        => (string) $this->getPath(),
	        "version"	  => (string) $this->getVersion(),
	        "minorNewscoopVersion" => (string) $this->getMinorNewscoopVersion(),
	        "installedVersion" => (string) $this->getInstalledVersion()
	    );
	}
}
