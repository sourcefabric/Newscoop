<?php

/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\TemplateList\Meta;

/**
 * Slideshow exposition for templates
 */
class SlideshowsMeta extends MetaBase
{
    protected function getPropertiesMap()
    {
        return array(
            'id' => 'getId',
            'headline' => 'getHeadline',
            'description' => 'getDescription',
            'items' => 'getItems',
            'articles' => 'getArticles'
        );
    }

    public function getItems()
    {
        return $this->dataObject->getItems()->toArray();
    }

    public function getArticles()
    {
        return $this->dataObject->getArticles()->toArray();
    }
}
