<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Newscoop\Image\Rendition;

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
     * @Column * @var string
     */
    private $headline;

    /**
     * @Column(type="text", nullable=True)
     * @var string
     */
    private $description;

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
     * @ManyToOne(targetEntity="Newscoop\Image\Rendition")
     * @JoinColumn(referencedColumnName="name")
     * @var Newscoop\Image\Rendition
     */
    private $rendition;

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
     * Set headline
     *
     * @param string $headline
     * @return void
     */
    public function setHeadline($headline)
    {
        $this->headline = (string) $headline;
    }

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
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

    /**
     * Set rendition
     *
     * @param Newscoop\Image\Rendition $rendition
     * @return void
     */
    public function setRendition(Rendition $rendition)
    {
        $this->rendition = $rendition;
    }

    /**
     * Get rendition
     *
     * @return Newscoop\Image\Rendition
     */
    public function getRendition()
    {
        return $this->rendition;
    }
}
