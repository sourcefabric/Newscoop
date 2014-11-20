<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
final class MetaSlideshowItemList implements Iterator
{
    /**
     * @var Newscoop\Package\Package
     */
    private $slideshow;

    /**
     * @var ArrayIterator
     */
    private $items;

    /**
     * @param Newscoop\Package\Package $slideshow
     */
    public function __construct(\Newscoop\Package\Package $slideshow)
    {
        $this->slideshow = $slideshow;
    }

    /**
     * @return MetaSlideshowItem
     */
    public function current()
    {
        return new MetaSlideshowItem($this->items->current());
    }

    /**
     * @return void
     */
    public function rewind()
    {
        if ($this->items === null) {
            $this->items = new ArrayIterator($this->slideshow->getItems()->toArray());
        }

        return $this->items->rewind();
    }

    /**
     * @return void
     */
    public function next()
    {
        return $this->items->next();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->items->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->items->valid();
    }
}
