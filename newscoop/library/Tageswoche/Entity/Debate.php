<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="plugin_debate")
 */
class Debate
{
    /**
     * @Id @Column(type="integer", name="debate_nr")
     * @var int
     */
    private $id;

    /**
     * @ManyToMany(targetEntity="Newscoop\Entity\Article")
     * @JoinTable(name="plugin_debate_article",
     *      joinColumns={
     *          @JoinColumn(name="fk_debate_nr", referencedColumnName="debate_nr")
     *      },
     *      inverseJoinColumns={
     *          @JoinColumn(name="fk_article_nr", referencedColumnName="Number"),
     *          @JoinColumn(name="fk_article_language_id", referencedColumnName="IdLanguage")
     *      })
     * @var Doctrine\Common\Collections\Collection
     */
    private $articles;

    /**
     * @OneToMany(targetEntity="Tageswoche\Entity\Vote", mappedBy="debate")
     * @var Doctrine\Common\Collections\Collection
     */
    private $votes;

    /**
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get votes
     *
     * @return array
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
