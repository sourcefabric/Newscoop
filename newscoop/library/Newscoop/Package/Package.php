<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

/**
 * @Entity
 * @Table(name="package")
 */
class Package
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", nullable=True)
     * @var int
     */
    private $articleNumber;

    /**
     * @OneToMany(targetEntity="Newscoop\Package\Item", mappedBy="package")
     * @OrderBy({"offset"="ASC"})
     * @return Doctrine\Common\Collections\Collection
     */
    private $items;

    /**
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('#%d', $this->id);
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
     * Set article number
     *
     * @param int $articleNumber
     * @return void
     */
    public function setArticleNumber($articleNumber)
    {
        $this->articleNumber = (int) $articleNumber;
    }

    /**
     * Get article number
     *
     * @return int
     */
    public function getArticleNumber()
    {
        return $this->articleNumber;
    }

    /**
     * Get items
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
}
