<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Entity;

use Newscoop\Entity\Article;

/**
 * @Entity
 * @Table(name="Xmobile_issue")
 */
class MobileIssueArticle
{
    /**
     * @OneToOne(targetEntity="Newscoop\Entity\Article")
     * @JoinColumn(name="NrArticle", referencedColumnName="Number")
     */
    private $article;

    /**
     * @Id
     * @Column(type="integer", name="NrArticle")
     * @var int
     */
    private $articleNumber;

    /**
     * @Column(type="string", name="Fissuedate")
     * @var string
     */
    private $date;

    /**
     * Get article
     *
     * @return Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
}
