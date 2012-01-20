<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

require_once __DIR__ . '/LocalImageTest.php';

/**
 */
class ArticleRenditionCollectionTest extends \TestCase
{
    public function testUsingSmallDefaultImageForRendition()
    {
        $rendition = new Rendition(800, 600, 'crop', 'test');
        $renditions = new ArticleRenditionCollection(1, array(), new LocalImage(LocalImageTest::PICTURE_LANDSCAPE));
        $this->assertFalse(isset($renditions[$rendition]));
        $this->assertNull($renditions[$rendition]);
    }
}
