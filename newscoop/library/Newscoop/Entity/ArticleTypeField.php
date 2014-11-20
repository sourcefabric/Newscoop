<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Issue entity
 * @ORM\Entity
 * @ORM\Table(name="ArticleTypeMetadata")
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ArticleTypeFieldRepository")
 *
 * @todo check for performance issues
 */
class ArticleTypeField
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",name="field_name")
     */
    protected $name;

    /**
     * @var Newscoop\Entity\ArticleType
     * (at) ManyToOne( targetEntity="Newscoop\Entity\ArticleType" )
     * (at) JoinColumn( name="type_id", referencedColumnName="id", nullable=FALSE )
     * @todo hack
     */
    protected $articleType;

    /**
     * @ORM\Column(type="string",name="type_name")
     * @todo hack used for getting the parent article type from same table, which we will set later on into self::$type.
     */
    protected $typeHack;

    /**
     * @ORM\Column(type="integer", name="max_size", nullable=True)
     */
    protected $length;

    /**
     * @ORM\Column(type="string",name="field_type", nullable=True)
     */
    protected $type;

    /**
     * @ORM\Column(type="integer", name="field_weight", nullable=True)
     * @var int
     */
    protected $fieldWeight;

    /**
     * @ORM\Column(type="integer", name="is_hidden", nullable=True)
     * @var int
     */
    protected $isHidden;

    /**
     * @ORM\Column(type="integer", name="comments_enabled", nullable=True)
     * @var int
     */
    protected $commentsEnabled;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Translation")
     * @ORM\JoinColumn(name="fk_phrase_id", referencedColumnName="phrase_id", nullable=true, onDelete="SET NULL")
     * @var Newscoop\Entity\Translation
     */
    protected $phrase;

    /**
     * @ORM\Column(type="integer", name="fk_phrase_id", nullable=True)
     * @var int
     */
    protected $phraseId;

    /**
     * @ORM\Column(name="field_type_param", nullable=True)
     * @var string
     */
    protected $fieldTypeParam;

    /**
     * @ORM\Column(type="integer", name="is_content_field", nullable=True)
     * @var int
     */
    protected $isContentField;

    /**
     * @ORM\Column(type="integer", name="show_in_editor", nullable=False)
     * @var int
     */
    protected $showInEditor;

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
        $this->articleType = $type;

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

    /**
     * Getter for fieldWeight
     *
     * @return int
     */
    public function getFieldWeight()
    {
        return $this->fieldWeight;
    }

    /**
     * Setter for fieldWeight
     *
     * @param int $fieldWeight Value to set
     *
     * @return self
     */
    public function setFieldWeight($fieldWeight)
    {
        $this->fieldWeight = $fieldWeight;

        return $this;
    }

    /**
     * Getter for isHidden
     *
     * @return int
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Setter for isHidden
     *
     * @param int $isHidden Value to set
     *
     * @return self
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Getter for commentsEnabled
     *
     * @return int
     */
    public function getCommentsEnabled()
    {
        return $this->commentsEnabled;
    }

    /**
     * Setter for commentsEnabled
     *
     * @param int $commentsEnabled Value to set
     *
     * @return self
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->commentsEnabled = $commentsEnabled;

        return $this;
    }

    /**
     * Getter for phraseId
     *
     * @return int
     */
    public function getPhraseId()
    {
        return $this->phraseId;
    }

    /**
     * Getter for fieldTypeParam
     *
     * @return string
     */
    public function getFieldTypeParam()
    {
        return $this->fieldTypeParam;
    }

    /**
     * Setter for fieldTypeParam
     *
     * @param string $fieldTypeParam Value to set
     *
     * @return self
     */
    public function setFieldTypeParam($fieldTypeParam)
    {
        $this->fieldTypeParam = $fieldTypeParam;

        return $this;
    }

    /**
     * Getter for isContentField
     *
     * @return int
     */
    public function getIsContentField()
    {
        return $this->isContentField;
    }

    /**
     * Setter for isContentField
     *
     * @param int $isContentField Value to set
     *
     * @return self
     */
    public function setIsContentField($isContentField)
    {
        $this->isContentField = $isContentField;

        return $this;
    }

    /**
     * Getter for showInEditor
     *
     * @return int
     */
    public function getShowInEditor()
    {
        return $this->showInEditor;
    }

    /**
     * Setter for showInEditor
     *
     * @param int $showInEditor Value to set
     *
     * @return self
     */
    public function setShowInEditor($showInEditor)
    {
        $this->showInEditor = $showInEditor;

        return $this;
    }

    /**
     * Gets the value of phrase.
     *
     * @return Newscoop\Entity\Translation
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * Sets the value of phrase.
     *
     * @param Translation $phrase the phrase
     *
     * @return self
     */
    protected function setPhrase(Translation $phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }
}
