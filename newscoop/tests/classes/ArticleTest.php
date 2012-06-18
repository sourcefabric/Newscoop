<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class ArticleTest extends \TestCase
{
    const LANGUAGE_ID = 1;

    private $fields = array(
        'name' => true,
    );

    private $map = array(
        'ä' => 'ae',
        'Ä' => 'ae',
        'á' => 'a',
        'à' => 'a',
        'â' => 'a',
        'æ' => 'a',
        'é' => 'e',
        'é' => 'e',
        'è' => 'e',
        'è' => 'e',
        'ü' => 'ue',
        'Ü' => 'ue',
        'ö' => 'oe',
        'Ö' => 'oe',
        'ß' => 'ss',
        'ç' => 'c',
        'ê' => 'e',
        'ê' => 'e',
        'ì' => 'i',
        'ì' => 'i',
        'í' => 'i',
        'í' => 'i',
        'ô' => 'o',
        'ô' => 'o',
        'œ' => 'o',
        'ò' => 'o',
        'ò' => 'o',
        'ó' => 'o',
        'ó' => 'o',
        'ù' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'û' => 'u',
        'ú' => 'u',
        'ú' => 'u',
        'ÿ' => 'y',
        'Ÿ' => 'y',
        ';' => '',
        '_' => '',
        '=' => '',
        '»' => '',
        '«' => '',
        '==' => '',
    );

    public function testSeoUrlEnd()
    {
        foreach ($this->map as $title => $url) {
            $article = new Article();
            $article->setName($title);
            $this->assertEquals("$url.htm", $article->getSEOURLEnd($this->fields, self::LANGUAGE_ID));
            $this->assertEquals("$title.htm", $article->getLegacySEOURLEnd($this->fields, self::LANGUAGE_ID), 'legacy');
        }
    }
}
