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
    protected $articleNumber;

    /** @var array */
    protected $renditions = array();

    /** @var Newscoop\Image\ImageInterface */
    protected $defaultImage;

    /**
     * @param int $articleNumber
     * @param array $articleRenditions
     * @param ImageInterface $defaultImage
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
     * @param boolean $strict Check only key existing
     * @return bool
     */
    public function offsetExists($rendition, $strict = false)
    {
        if ($strict) {
            return array_key_exists((string) $rendition, $this->renditions);
        }

        return array_key_exists((string) $rendition, $this->renditions) || ($this->defaultImage !== null && $rendition->fits($this->defaultImage));
    }

    /**
     * Get image for rendition
     *
     * @param Newscoop\Image\Rendition $rendition
     * @return Newscoop\Image\ArticleImageRendition
     */
    public function offsetGet($rendition)
    {
        return array_key_exists((string) $rendition, $this->renditions)
            ? $this->renditions[(string) $rendition]
            : ($this->defaultImage !== null && $rendition->fits($this->defaultImage) ? $this->renditions[(string) $rendition] = new DefaultArticleRendition($this->articleNumber, $rendition, $this->defaultImage) : null);
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
        throw new \BadMethodCallException("Collection not editable");
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
