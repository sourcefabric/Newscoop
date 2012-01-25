<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Newscoop\Image\LocalImage;

/**
 * @Entity
 * @Table(name="package_item")
 */
class Item
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Newscoop\Package\Package")
     * @var Newscoop\Package\Package
     */
    private $package;

    /**
     * @ManyToOne(targetEntity="Newscoop\Image\LocalImage")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Image\LocalImage
     */
    private $image;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $offset;

    /**
     * @param Newscoop\Package\Package $package
     * @param mixed $item
     */
    public function __construct(Package $package, $item)
    {
        $this->image = $item;
        $this->package = $package;
        $this->offset = (int) count($this->package->getItems());
        $this->package->getItems()->set($this->offset, $this);
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
     * Get image
     *
     * @return Newscoop\Image\LocalImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set offset
     *
     * @param int $offset
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * Get offset
     *
     * @param int $offset
     * @return void
     */
    public function getOffset()
    {
        return $this->offset;
    }
}
