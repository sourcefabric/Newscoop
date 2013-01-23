<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * Webcode entity
 *
 * @Entity
 * @Table(name="webcode")
 */
class Webcode
{
    /**
     * @Id
     * @Column(type="string", length="80")
     * @var string
     */
    private $webcode;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @JoinColumns({
     *      @JoinColumn(name="article_number", referencedColumnName="Number"),
     *      @JoinColumn(name="language_id", referencedColumnName="IdLanguage")
     *  })
     */
    private $article;

    /**
     * @param string $webcode
     * @param Newscoop\Entity\Article $article
     */
    public function __construct($webcode, $article)
    {
        $this->webcode = (string) $webcode;
        $this->article = $article;
    }

    /**
     * Get article
     *
     * @return Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->webcode;
    }
}
