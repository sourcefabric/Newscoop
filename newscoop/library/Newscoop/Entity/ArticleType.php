<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Utils\Validation;

/**
 * Issue entity
 * @ORM\Entity
 * @ORM\Table(name="ArticleTypeMetadata")
 */
class ArticleType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",unique=true,name="type_name")
     */
    private $name;

    /**
     * @ORM\Id
     * @ORM\Column(type="string",unique=true,name="field_name")
     *
     * TODO MASSIVE ALERT HERE! WE HAVE THE STRING: NULL FOR NULL VALUES FROM LEGACY!!!...
     */
    private $fieldName = 'NULL';

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     * (at) OneToMany( targetEntity="Newscoop\Entity\ArticleTypeField", mappedBy="articleType" )
     * @todo hack
     */
    private $articleTypeFields;

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
     * Getter for articleTypeFields
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getArticleTypeFields()
    {
        return $this->articleTypeFields;
    }

    /**
     * Setter for articleTypeFields
     *
     * @param Doctrine\Common\Collections\ArrayCollection $articleTypeFields
     *        Value to set
     *
     * @return self
     */
    public function setArticleTypeFields(
        \Doctrine\Common\Collections\ArrayCollection $articleTypeFields
    )
    {
        $this->articleTypeFields = $articleTypeFields;

        return $this;
    }


	/**
	 * Used for update and stuff
     *
	 * @return string
	 */
	public function __toString()
	{
	    return $this->getName();
	}
}

