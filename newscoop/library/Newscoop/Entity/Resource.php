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
 * Provides the contections of database entries with file system or external resources.
 * The resources can be from the database or from the file system so whenever checking resources for equality use the path property.
 *
 * @Entity
 * @Table(name="resource")
 */
class Resource extends Entity
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /* --------------------------------------------------------------- */

    /**
     * @Column(name="name", nullable=FALSE)
     * @var string
     */
    private $name;
    /**
     * @Column(name="path", unique=TRUE, nullable=FALSE)
     * @var string
     */
    private $path;

    /* --------------------------------------------------------------- */

    /**
     * Provides the name of the theme resource, must be a user frendly name used for displaying it on the UI.
     *
     * @return string
     * 		The name of the theme resource.
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the theme resource, must be a user frendly name used for displaying it on the UI.
     *
     * @param string $name
     * 		The name of the theme resource, must not be null or empty.
     *
     * @return Newscoop\Entity\Resource
     * 		This object for chaining purposes.
     */
    function setName($name)
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
     * 		The path of the resource.
     */
    function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path of the resource.
     *
     * @param string $path
     * 		The path of the resource.
     *
     * @return Newscoop\Entity\Resource
     * 		This object for chaining purposes.
     */
    function setPath($path)
    {
        Validation::notEmpty($path, 'path');
		$this->path = str_replace('\\', '/', $path);
        return $this;
    }

    /**
     * @param string $path Should be id..
     */
    public function __construct($path = null)
    {
        if (!is_null($path))
            $this->setPath($path);
    }

    /* --------------------------------------------------------------- */

    /**
     * Checks if the provided resource is equal with this resource.
     * The equality is done using the resource path.
     *
     * @param Resource $other
     * 		The resource to compare with.
     *
     * @return bool
     * 		True if the resources are considered eqaul, false otherwise.
     */
    function isSame(Resource $other)
    {
        if ($other !== NULL) {
            return $this->path === $other->path;
        }
        return FALSE;
    }

    /* ----------------- LEGACY -------------------------------------- */

    /**
     * Check if the resource exists
     *
     * @return bool
     *          True always for now
     */
    public function exists()
    {
        return true;
    }

    /**
     * Checks if the provided resource is equal with this resource.
     * The equality is done using the resource path.
     *
     * @param Resource $other
     * 		The resource to compare with.
     *
     * @return bool
     * 		True if the resources are considered eqaul, false otherwise.
     */
    public function sameAs(Resource $other)
    {
        return $this->isSame($other);
    }

    public function getProperty()
    {
        return;
    }
}