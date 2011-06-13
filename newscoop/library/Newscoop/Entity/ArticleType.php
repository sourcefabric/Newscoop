<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Utils\Validation;

/**
 * Issue entity
 * @Entity
 * @Table(name="ArticleTypeMetadata")
 */
class ArticleType
{
    /**
     * @Id
     * @Column(type="string",unique=true,name="type_name")
     */
    private $name;

    /**
     * @Id
     * @Column(type="string",unique=true,name="field_name")
     *
     * TODO MASSIVE ALERT HERE! WE HAVE THE STRING: NULL FOR NULL VALUES FROM LEGACY!!!...
     */
    private $fieldName = 'NULL';

	/**
	 * Provides the name of the article type, must be a user frendly name used for displaying it on the UI.
	 *
	 * @return string
	 *		The name of the theme.
	 */
    public function getName()
    {
        return $this->name;
    }

	/**
	 * Set the name of the article type, must be a user frendly name used for displaying it on the UI.
	 *
	 * @param string $name
	 *		The name of the article type, must not be null or empty.
	 *
	 * @return Newscoop\Entity\ArticleType
	 *		This object for chaining purposes.
	 */
	public function setName($name)
	{
		Validation::notEmpty($name, 'name');
		$this->name = $name;
		return $this;
	}

	/**
	 * Used for update and stuff
	 * @return string
	 */
	public function __toString()
	{
	    return $this->getName();
	}
}

