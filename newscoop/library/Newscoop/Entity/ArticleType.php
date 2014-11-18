<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Utils\Validation;

/**
 * Issue entity
 * @ORM\Entity
 * @ORM\Table(name="ArticleTypeMetadata")
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ArticleTypeRepository")
 */
class ArticleType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",unique=true,name="type_name")
     */
    protected $name;

    /**
     * @ORM\Id
     * @ORM\Column(type="string",unique=true,name="field_name")
     *
     * TODO MASSIVE ALERT HERE! WE HAVE THE STRING: NULL FOR NULL VALUES FROM LEGACY!!!...
     */
    protected $fieldName = 'NULL';

    /**
     * @ORM\Column(type="integer", name="fk_phrase_id", nullable=True)
     * @var int
     */
    protected $phraseId;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     * (at) OneToMany( targetEntity="Newscoop\Entity\ArticleTypeField", mappedBy="articleType" )
     * @todo hack
     */
    protected $articleTypeFields;

    /**
     * Provides the name of the article type.
     *
     * @return string   The name of the theme.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the article type
     *
     * @param string $name
     *      The name of the article type, must not be null or empty.
     *
     * @return ArticleType
     *      This object for chaining purposes.
     */
    public function setName($name)
    {
        Validation::notEmpty($name, 'name');
        $this->name = $name;
        return $this;
    }


    /**
     * Provides article type phraseId.
     *
     * @return integer
     */
    public function getPhraseId()
    {
        return $this->phraseId;
    }

    /**
     * Set article type phraseId.
     */
    public function setPhraseId($phraseId)
    {
        $this->phraseId = $phraseId;

        return $this;
    }

    /**
     * Getter for fieldName
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
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
    public function setArticleTypeFields(ArrayCollection $articleTypeFields)
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

