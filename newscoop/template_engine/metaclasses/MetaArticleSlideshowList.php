<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
final class MetaArticleSlideshowList implements Iterator
{
    /**
     * @var int
     */
    private $articleNumber;

    /**
     * @var array
     */
    private $slideshows;

    /**
     * @param int $articleNumber
     */
    public function __construct($articleNumber)
    {
        $this->articleNumber = (int) $articleNumber;
    }

    /**
     * @return MetaSlideshow
     */
    public function current()
    {
        return new MetaSlideshow($this->slideshows->current());
    }

    /**
     * @return void
     */
    public function rewind()
    {
        if ($this->slideshows === null) {
            $this->slideshows = new ArrayIterator(Zend_Registry::get('container')->getService('package')->findByArticle($this->articleNumber));
        }

        $this->slideshows->rewind();
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->slideshows->next();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->slideshows->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->slideshows->valid();
    }
}
