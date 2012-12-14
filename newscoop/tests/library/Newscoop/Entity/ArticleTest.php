<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 */
class ArticleTest extends \TestCase
{
    const NUMBER = 123;
    const LANGUAGE = 'en';
    const TITLE = 'Lorem Ipsum';
    const LEAD = 'Lorem';
    const BODY = 'Ipsum';

    public function setUp()
    {
        $this->language = new Language();
        $this->language->setCode(self::LANGUAGE);
    }

    public function testGetView()
    {
        $article = new Article(self::NUMBER, $this->language);
        $article->author(self::TITLE, array());

        $view = $article->getView();
        $this->assertInstanceOf('Newscoop\View\ArticleView', $view);

        $this->assertEquals(self::NUMBER, $view->number, 'number');
        $this->assertEquals(self::LANGUAGE, $view->language);
        $this->assertEquals(self::TITLE, $view->title);
        $this->assertEquals(array(), $view->fields);
    }
}
