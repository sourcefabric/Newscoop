<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Publication entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\PublicationRepository")
 * @Table(name="Publications")
 */
class Publication
{
    /**
     * @Id @generatedValue
     * @Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @Column(name="Name")
     * @var string
     */
    private $name;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\Issue", mappedBy="publication")
     * @var array
     */
    private $issues;

    /**
     */
    public function __construct()
    {
        $this->issues = new ArrayCollection;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        $languages = array();
        foreach ($this->issues as $issue) {
            $languages[] = $issue->getLanguage();
        }

        return $languages;
    }
}

