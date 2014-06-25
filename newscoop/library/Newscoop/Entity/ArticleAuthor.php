<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article Author Association class.
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ArticleAuthorRepository")
 * @ORM\Table(name="ArticleAuthors")
 */
class ArticleAuthor
{
    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="fk_article_number", referencedColumnName="Number"),
     *      @ORM\JoinColumn(name="fk_language_id", referencedColumnName="IdLanguage")
     *  })
     */
    protected $article;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="fk_article_number")
     * @var int
     */
    protected $articleNumber;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="fk_language_id")
     * @var int
     */
    protected $languageId;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Author")
     * @ORM\JoinColumn(name="fk_author_id", referencedColumnName="id")
     * @var Newscoop\Entity\Author
     */
    protected $author;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\AuthorType")
     * @ORM\JoinColumn(name="fk_type_id", referencedColumnName="id")
     * @var Newscoop\Entity\AuthorType
     */
    protected $type;

    /**
     * @ORM\Column(type="integer", nullable=true, name="`order`")
     * @var int
     */
    protected $order;

    /**
     * @param int $articleNumber
     * @param int $languageId
     * @param int $authorId
     * @param int $typeId
     * @param int $order
     */
    public function __construct($articleNumber = null, $languageId = null, $authorId = null, $typeId = null, $order = null)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->languageId = (int) $languageId;
        $this->authorId = (int) $authorId;
        $this->typeId = (int) $typeId;
        $this->order = (int) $order;
    }

    /**
     * Get Article object
     * @return Newscoop\Entity\Article
     */
    public function getArticle() {
        return $this->article;
    }

    /**
     * Set Article
     * @param \Newscoop\Entity\Article $article
     */
    public function setArticle(\Newscoop\Entity\Article $article) {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article number
     * @return int
     */
    public function getArticleNumber() {
        return $this->articleNumber;
    }

    /**
     * Set article number
     * @param string $articleNumber
     */
    public function setArticleNumber($articleNumber) {
        $this->articleNumber = $articleNumber;

        return $this;
    }

    /**
     * Get language id
     * @return int
     */
    public function getLanguageId() {
        return $this->languageId;
    }

    /**
     * Set language id
     * @param int $languageId
     */
    public function setLanguageId($languageId) {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * Get Author
     * @return Newscoop\Entity\Author
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param Newscoop\Entity\Author $author
     */
    public function setAuthor(Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get AuthorType
     * @return Newscoop\Entity\AuthorType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set AuthorType
     * @param \Newscoop\Entity\AuthorType $type
     */
    public function setType(\Newscoop\Entity\AuthorType $type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get order
     * @return int
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set order
     * @param int $order
     */
    public function setOrder($order) {
        $this->order = $order;

        return $this;
    }
}
