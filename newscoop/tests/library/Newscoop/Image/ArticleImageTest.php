<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class ArticleImageTest extends \TestCase
{
    const ARTICLE_NUMBER = 123;

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\ArticleImage', new ArticleImage(self::ARTICLE_NUMBER, new LocalImage(self::PICTURE_LANDSCAPE)));
    }

    public function testImageInterface()
    {
        $image = new ArticleImage(self::ARTICLE_NUMBER, new LocalImage(self::PICTURE_LANDSCAPE));

        $this->assertEquals(self::PICTURE_LANDSCAPE, $image->getPath());
        $this->assertEquals(500, $image->getWidth());
        $this->assertEquals(333, $image->getHeight());
        $this->assertFalse($image->isDefault());
    }
}
