<?php

use Newscoop\Entity\ArticleDatetime,
    ArrayIterator,
    IteratorAggregate;

class MetaArticleDatetime implements IteratorAggregate
{
    private $datetimes;

    private $iterator;

    public function __construct($dates)
    {
        $this->datetimes = (array) $dates;
        $this->iterator = new ArrayIterator($this->datetimes);
    }

    public function getIterator()
    {
        return (is_null($this->iterator) ? ($this->iterator = new ArrayIterator($this->datetimes)) : $this->iterator);
    }

    public function __get($name)
    {
        if (!(($currentItem = $this->iterator->current()) instanceof ArticleDatetime)) {
            return null;
        }
        $getMethod = 'get'.preg_replace("`(?<=[a-z])(_([a-z]))`e","ucfirst(strtoupper('\\2'))",trim($name));
        if (!is_callable(array($currentItem, $getMethod))) {
            return null;
        }
        return $currentItem->$getMethod();
    }
}