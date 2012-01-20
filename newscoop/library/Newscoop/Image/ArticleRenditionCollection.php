<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Article Rendition Collection
 *
 * Set of image renditions for article returning default image
 * if not overriden
 */
class ArticleRenditionCollection implements \ArrayAccess
{
    /** @var int */
    private $articleNumber;

    /** @var array */
    private $renditions = array();

    /** @var Newscoop\Image\ImageInterface */
    private $defaultImage;

    /**
     * @param int $articleNumber
     * @param array $articleRenditions
     * @param Newscoop\Image\ImageInterface $defaultImage
     */
    public function __construct($articleNumber, array $articleRenditions = array(), ImageInterface $defaultImage = null)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->setArticleRenditions($articleRenditions);
        $this->defaultImage = $defaultImage;
    }

    /**
     * Test if can provide image for rendition
     *
     * @param Newscoop\Image\Rendition $rendition
     * @return bool
     */
    public function offsetExists($rendition)
    {
        return isset($this->renditions[(string) $rendition]) || $this->defaultImage !== null;
    }

    /**
     * Get image for rendition
     *
     * @param Newscoop\Image\Rendition $rendition
     * @return Newscoop\Image\ArticleImageRendition
     */
    public function offsetGet($rendition)
    {
        return isset($this->renditions[(string) $rendition])
            ? $this->renditions[(string) $rendition]
            : ($this->defaultImage !== null ? $this->renditions[(string) $rendition] = new DefaultArticleRendition($this->articleNumber, $rendition, $this->defaultImage) : null);
    }

    /**
     * @throws BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException("Collection not editable");
    }

    /**
     * @throws BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        $this->offsetGet();
    }

    /**
     * Set article renditions
     *
     * @param array $renditions
     * @return void
     */
    private function setArticleRenditions($renditions)
    {
        foreach ($renditions as $rendition) {
            $this->renditions[$rendition->getName()] = $rendition;
        }
    }
}
