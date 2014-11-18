<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="package_article")
 */
class Article
{
    /**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Package", inversedBy="articles")
     * @ORM\JoinTable(name="package_article_package")
     * @var array
     */
    protected $packages;

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
