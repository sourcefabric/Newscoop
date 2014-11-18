<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Entity\Language;

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
    protected $id;

    /**
     * @ORM\Column(type="integer", name="NrArticle")
     * @var int
     */
    protected $articleNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Image\LocalImage", fetch="EAGER")
     * @ORM\JoinColumn(name="IdImage", referencedColumnName="Id")
     * @var Newscoop\Image\Image
     */
    protected $image;

    /**
     * @ORM\Column(type="integer", name="Number", nullable=True)
     * @var int
     */
    protected $number;

    /**
     * @ORM\Column(type="boolean", name="is_default", nullable=True)
     * @var bool
     */
    protected $isDefault;

    /**
     * @ORM\OneToMany(targetEntity="ArticleImageCaption", mappedBy="articleImage", indexBy="languageId", cascade={"persist"})
     * @var Doctrine\Common\Collections\Collection
     */
    private $captions;

    /**
     * @param int $articleNumber
     * @param LocalImage $image
     * @param bool $isDefault
     */
    public function __construct($articleNumber, LocalImage $image, $isDefault = false, $number = 1)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->image = $image;
        $this->isDefault = (bool) $isDefault;
        $this->number = $number;
        $this->captions = new ArrayCollection();
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

    /**
     * Sets the value of number.
     *
     * @param int $number the number
     *
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Gets the value of number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
    * Set caption
    *
    * @param string $caption
    * @param Language $language
    * @return void
    */
    public function setCaption($caption, Language $language)
    {
        if (!isset($this->captions[$language->getId()])) {
            $this->captions[$language->getId()] = new ArticleImageCaption($this, $language);
        }

        $this->captions[$language->getId()]->setCaption($caption);
    }

    /**
    * Get caption
    *
    * @return string
    */
    public function getCaption($languageId)
    {
        return isset($this->captions[$languageId]) ? $this->captions[$languageId]->getCaption() : null;
    }
}
