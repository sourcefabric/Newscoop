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
    protected $issueNumber;

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

    /**
     * Gets the value of publication.
     *
     * @return \Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Sets the value of publication.
     *
     * @param \Newscoop\Entity\Publication $publication the publication
     *
     * @return self
     */
    protected function setPublication(\Newscoop\Entity\Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Gets the value of sectionNumber.
     *
     * @return int
     */
    public function getSectionNumber()
    {
        return $this->sectionNumber;
    }

    /**
     * Sets the value of sectionNumber.
     *
     * @param int $sectionNumber the section number
     *
     * @return self
     */
    protected function setSectionNumber($sectionNumber)
    {
        $this->sectionNumber = $sectionNumber;

        return $this;
    }

    /**
     * Gets the value of issueNumber.
     *
     * @return int
     */
    public function getIssueNumber()
    {
        return $this->issueNumber;
    }

    /**
     * Sets the value of issueNumber.
     *
     * @param int $issueNumber the issue number
     *
     * @return self
     */
    protected function setIssueNumber($issueNumber)
    {
        $this->issueNumber = $issueNumber;

        return $this;
    }
}

