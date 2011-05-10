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
     * @OneToMany(targetEntity="Newscoop\Entity\Section", mappedBy="issue")
     * @var array
     */
    private $sections;

    /**
     */
    public function __construct()
    {
        $this->sections = new ArrayCollection;
    }

    /**
     * Get language
     *
     * @return Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }
}

