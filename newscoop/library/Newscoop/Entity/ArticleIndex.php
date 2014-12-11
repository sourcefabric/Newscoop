<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * ArticleIndex entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="ArticleIndex")
 */
class ArticleIndex
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="\Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var \Newscoop\Entity\Publication
     */
    protected $publication;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    protected $language;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumn(name="NrArticle", referencedColumnName="Number")
     * @var \Newscoop\Entity\Article
     */
    protected $article;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\KeywordIndex")
     * @ORM\JoinColumn(name="IdKeyword", referencedColumnName="Id")
     * @var \Newscoop\Entity\KeywordIndex
     */
    protected $keyword;

    /**
     * @ORM\Id()
     * @ORM\Column(name="NrSection")
     * @var int
     */
    protected $sectionNumber;

    /**
     * @ORM\Id()
     * @ORM\Column(name="NrIssue")
     * @var int
     */
    protected $issueMumber;

    /**
     * Gets the value of article.
     *
     * @return \Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Sets the value of article.
     *
     * @param \Newscoop\Entity\Article $article the article
     *
     * @return self
     */
    protected function setArticle(\Newscoop\Entity\Article $article)
    {
        $this->article = $article;

        return $this;
    }
}

