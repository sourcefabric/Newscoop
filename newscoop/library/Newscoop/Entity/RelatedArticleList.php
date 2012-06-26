<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection as Collection;

/**
 * Related article list
 *
 * @Entity
 * @Table(name="context_boxes")
 */
class RelatedArticleList
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", name="fk_article_no")
     * @var int
     */
    private $number;

    /**
     * @ManyToMany(targetEntity="Newscoop\Entity\Article")
     * @JoinTable(name="context_articles",
     *      joinColumns={@JoinColumn(name="fk_context_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="fk_article_no", referencedColumnName="Number")}
     *      )
     * @var Doctrine\Common\Collections\Collection
     */
    private $articles;

    /**
     */
    public function __construct()
    {
        $this->articles = new Collection();
    }

    /**
     * Get articles
     *
     * @return array
     */
    public function getArticles()
    {
        return $this->articles->toArray();
    }
}
