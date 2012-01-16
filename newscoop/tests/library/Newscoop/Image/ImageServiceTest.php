<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class ImageServiceTest extends \TestCase
{
    const PICTURE = 'tests/fixtures/picture.jpg';

    /** @var Newscoop\Image\ImageService */
    protected $service;

    /** @var array */
    protected $config = array();

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        $this->config = array(
            'cache_url' => 'images/cache',
            'cache_path' => sys_get_temp_dir() . '/' . uniqid(),
        );

        $this->orm = $this->setUpOrm('Newscoop\Image\Image');

        $this->service = new ImageService($this->config, $this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\ImageService', $this->service);
    }

    public function testGetSrc()
    {
        $src = $this->service->getSrc(self::PICTURE, 300, 300);
        $this->assertEquals('300x300/center_center/' . rawurlencode(rawurlencode(self::PICTURE)), $src);
    }

    public function testGenerateImage()
    {
        $src = '300x300/center_center/' . rawurlencode(rawurlencode(self::PICTURE));

        $image = $this->generateImage($src);
        $info = $this->getInfo($image);

        $this->assertFileExists($this->config['cache_path'] . "/$src");
        $this->assertEquals(300, $info[0]);
        $this->assertEquals(300, $info[1]);
    }

    public function testFind()
    {
        $image = new Image('path');
        $this->orm->persist($image);
        $this->orm->flush($image);

        $this->assertContains('path', $this->service->find($image->getId())->getPath());
    }

    /**
     * Generates image
     *
     * @param string $url
     * @return string
     */
    private function generateImage($url)
    {
        ob_start();
        $this->service->generateFromSrc($url);
        return ob_get_clean();
    }

    /**
     * Get image info
     *
     * @param string $image
     * @return array
     */
    private function getInfo($image)
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($tmpfile, $image);
        $info = getimagesize($tmpfile);
        unlink($tmpfile);
        return $info;
    }
}
