<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Issue entity
 * @Entity
 * @Table(name="Issues")
 */
class Issue
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @Id
     * @Column(type="integer", name="Number")
     * @var int
     */
    private $number;

    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * Get language
     *
     * @return Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }
}

