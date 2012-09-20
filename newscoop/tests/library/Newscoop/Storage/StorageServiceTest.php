<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Storage;

/**
 */
class StorageServiceTest extends \TestCase
{
    /**
     * @var Newscoop\Storage\StorageService
     */
    protected $service;

    public function setUp()
    {
        $this->image = realpath(APPLICATION_PATH . '/../' . self::PICTURE_LANDSCAPE);
        $this->hash = sha1_file($this->image);
        $this->adapter = $this->getMock('Zend_Cloud_StorageService_Adapter');
        $this->service = new StorageService($this->adapter);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Storage\StorageService', $this->service);
    }

    public function testMoveImage()
    {
        $name = $this->getImageName();

        $this->adapter->expects($this->once())
            ->method('moveItem')
            ->with($this->equalTo(self::PICTURE_LANDSCAPE), $this->equalTo('images/' . $name));

        $this->adapter->expects($this->once())
            ->method('fetchItem')
            ->with($this->equalTo(self::PICTURE_LANDSCAPE))
            ->will($this->returnValue(file_get_contents($this->image)));

        $newName = $this->service->moveImage(self::PICTURE_LANDSCAPE);
        $this->assertEquals($name, $newName);
    }

    public function testMoveThumbnail()
    {
        $name = $this->getImageName(true);

        $this->adapter->expects($this->once())
            ->method('moveItem')
            ->with($this->equalTo(self::PICTURE_LANDSCAPE), $this->equalTo('images/thumbnails/' . $name));

        $this->adapter->expects($this->once())
            ->method('fetchItem')
            ->with($this->equalTo(self::PICTURE_LANDSCAPE))
            ->will($this->returnValue(file_get_contents($this->image)));

        $newName = $this->service->moveThumbnail(self::PICTURE_LANDSCAPE);
        $this->assertEquals($name, $newName);
    }

    /**
     * Get image name
     *
     * @param bool $isThumbnail
     * @return string
     */
    private function getImageName()
    {
        return sprintf(
            '%s/%s/%s.jpg',
            substr($this->hash, 0, 1),
            substr($this->hash, 1, 2),
            $this->hash
        );
    }
}
