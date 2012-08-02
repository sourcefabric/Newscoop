<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Entity\Language;

/**
 * Article Image
 * @Entity
 * @Table(name="ArticleImages")
 */
class ArticleImage implements ImageInterface
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", name="NrArticle")
     * @var int
     */
    private $articleNumber;

    /**
     * @ManyToOne(targetEntity="Newscoop\Image\LocalImage", fetch="EAGER")
     * @JoinColumn(name="IdImage", referencedColumnName="Id")
     * @var Newscoop\Image\Image
     */
    private $image;

    /**
     * @Column(type="integer", name="Number", nullable=True)
     * @var int
     */
    private $number;

    /**
     * @Column(type="boolean", name="is_default", nullable=True)
     * @var bool
     */
    private $isDefault;

    /**
     * @OneToMany(targetEntity="ArticleImageCaption", mappedBy="articleImage", indexBy="languageId", cascade={"persist"})
     * @var Doctrine\Common\Collections\Collection
     */
    private $captions;

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
     * Set caption
     *
     * @param string $caption
     * @param Newscoop\Entity\Language $language
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

    /**
     * Set number
     *
     * @param int $number
     * @return void
     */
    public function setNumber($number)
    {
        $this->number = (int) $this->number;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get image id
     *
     * @return int
     */
    public function getImageId()
    {
        return $this->image->getId();
    }
}
