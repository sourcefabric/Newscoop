<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Collections\ArrayCollection;

/**
 * Issue entity
 * @Entity
 * @Table(name="ArticleTypeMetadata")
 *
 * @todo check for performance issues
 */
class ArticleTypeField
{
    /**
     * @Id
     * @Column(type="string",name="field_name")
     */
    private $name;

    /**
     * @var Newscoop\Entity\ArticleType
     * (at) ManyToOne( targetEntity="Newscoop\Entity\ArticleType" )
     * (at) JoinColumn( name="type_id", referencedColumnName="id", nullable=FALSE )
     * @todo hack
     */
    private $articleType;

    /**
     * @Column(type="string",name="type_name")
     * @todo hack used for getting the parent article type from same table, which we will set later on into self::$type.
     */
    private $typeHack;

    /**
     * @Column(type="integer", name="max_size", nullable=True)
     */
    private $length;

    /**
     * @Column(type="string",name="field_type", nullable=True)
     */
    private $type;

    /**
     * @Column(type="integer", name="field_weight", nullable=True)
     * @var int
     */
    private $fieldWeight;

    /**
     * @Column(type="integer", name="is_hidden", nullable=True)
     * @var int
     */
    private $isHidden;

    /**
     * @Column(type="integer", name="comments_enabled", nullable=True)
     * @var int
     */
    private $commentsEnabled;

    /**
     * @Column(type="integer", name="fk_phrase_id", nullable=True)
     * @var int
     */
    private $phraseId;

    /**
     * @Column(name="field_type_param", nullable=True)
     * @var string
     */
    private $fieldTypeParam;

    /**
     * @Column(type="integer", name="is_content_field", nullable=True)
     * @var int
     */
    private $isContentField;

    /**
     * Get the article type
     * @return \Newscoop\Entity\ArticleType
     */
    public function getArticleType()
    {
        return $this->articleType;
    }

    /**
     * @return \Newscoop\Entity\ArticleTypeField
     */
    public function setArticleType(ArticleType $type)
    {
        $this->articleType =
            //$this->typeHack =
            $type;
        return $this;
    }

    /**
     * @return \Newscoop\Entity\ArticleTypeField
     */
    public function setArticleTypeHack(ArticleType $type)
    {
        $this->typeHack = $type;
        return $this;
    }

    /**
     * @return \Newscoop\Entity\ArticleTypeField
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return \Newscoop\Entity\ArticleTypeField
     */
    public function setLength($val)
    {
        $this->length = $val;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \Newscoop\Entity\ArticleTypeField
     */
    public function setType($val)
    {
        $this->type = $val;
        return $this;
    }
}

