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
            'articles' => 'getArticles',
            'slug' => 'getSlug',
            'count' => 'getItemsCount'
        );
    }

    public function getItems()
    {
        return $this->dataObject->getItems()->toArray();
    }

    public function getItemsCount()
    {
        return count($this->dataObject->getItems()->toArray());
    }

    public function getArticles()
    {
        return $this->dataObject->getArticles()->toArray();
    }

    public function getSlug()
    {
        return $this->slugify($this->dataObject->getHeadline());
    }

    /**
     * Modifies a string to remove all non ASCII characters and spaces.
     */
    public function slugify($text)
    {
        $charMap = array(
            // Latin symbols
            '©' => '(c)',
            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',
        );
        // Make custom replacements
        $text = str_replace(array_keys($charMap), $charMap, $text);
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
