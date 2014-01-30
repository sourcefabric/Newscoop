<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article Image
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ImageRepository")
 * @ORM\Table(name="ArticleImages")
 */
class ArticleImage implements ImageInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue()
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="NrArticle")
     * @var int
     */
    private $articleNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Image\LocalImage", fetch="EAGER")
     * @ORM\JoinColumn(name="IdImage", referencedColumnName="Id")
     * @var Newscoop\Image\Image
     */
    private $image;

    /**
     * @ORM\Column(type="integer", name="Number", nullable=True)
     * @var int
     */
    private $number;

    /**
     * @ORM\Column(type="boolean", name="is_default", nullable=True)
     * @var bool
     */
    private $isDefault;

    /**
     * @param int $articleNumber
     * @param Newscoop\Image\LocalImage $image
     * @param bool $isDefault
     */
    public function __construct($articleNumber, LocalImage $image, $isDefault = false)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->image = $image;
        $this->isDefault = (bool) $isDefault;
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
     * Get image
     *
     * @return Newscoop\Image\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get image id
     *
     * @return int
     */
    public function getId()
    {
        return $this->image->getId();
    }

    /**
     * Get image path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->image->getPath();
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->image->getWidth();
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->image->getHeight();
    }

    /**
     * Set is default image
     *
     * @param bool $isDefault
     * @return void
     */
    public function setIsDefault($isDefault = false)
    {
        $this->isDefault = (bool) $isDefault;
    }

    /**
     * Test if is default image for article
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }
}
