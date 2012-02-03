<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

/**
 * @Entity
 * @Table(name="package_article")
 */
class Article
{
    /**
     * @Id @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ManyToMany(targetEntity="Package")
     * @JoinTable(name="package_article_package")
     * @var array
     */
    private $packages;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = (int) $id;
        $this->packages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get packages
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPackages()
    {
        return $this->packages;
    }
}
