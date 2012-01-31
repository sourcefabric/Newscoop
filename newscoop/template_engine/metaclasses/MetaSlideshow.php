<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
final class MetaSlideshow
{
    /**
     * @var string
     */
    public $headline;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var array
     */
    public $items;

    /**
     * @var Newscoop\Package\Package
     */
    private $slideshow;

    /**
     * @param Newscoop\Package\Package $package
     */
    public function __construct(\Newscoop\Package\Package $package)
    {
        $this->slideshow = $package;
        $this->headline = $this->slideshow->getHeadline();
        $this->slug = $this->slideshow->getSlug();
        $this->items = new MetaSlideshowItemList($this->slideshow);
    }
}
