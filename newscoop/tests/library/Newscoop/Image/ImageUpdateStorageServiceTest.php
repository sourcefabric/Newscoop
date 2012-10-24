<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class ImageUpdateStorageServiceTest extends \TestCase
{
    /** @var Newscoop\Image\ImageUpdateStorageService */
    protected $service;

    public function setUp()
    {
        $this->em = $this->setUpOrm('Newscoop\Image\LocalImage');

        $this->storage = $this->getMockBuilder('Newscoop\Storage\StorageService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new UpdateStorageService($this->em, $this->storage);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\UpdateStorageService', $this->service);
    }

    public function testUpdateStorage()
    {
        $image = new LocalImage();
        $image->upload('old', 'old');
        $this->em->persist($image);
        $this->em->flush();

        $this->storage->expects($this->once())
            ->method('moveImage')
            ->with($this->equalTo('images/old'))
            ->will($this->returnValue('a/bc/abc.jpg'));

        $this->storage->expects($this->once())
            ->method('moveThumbnail')
            ->with($this->equalTo('images/thumbnails/old'))
            ->will($this->returnValue('a/bc/abc.jpg'));

        $this->assertFalse($image->hasUpdatedStorage());

        $this->service->updateStorage();

        $this->assertTrue($image->hasUpdatedStorage());
        $this->assertEquals('images/a/bc/abc.jpg', $image->getPath());
        $this->assertEquals('images/thumbnails/a/bc/abc.jpg', $image->getThumbnailPath());
    }

    public function testIsDeletableNoReference()
    {
        $this->assertTrue($this->service->isDeletable('image'));
    }

    public function testIsDeletableMultiReferences()
    {
        $image = new LocalImage();
        $image->updateStorage('image', 'thumb');
        $this->em->persist($image);

        $image = new LocalImage();
        $image->updateStorage('image', 'thumb');
        $this->em->persist($image);

        $this->em->flush();

        $this->assertFalse($this->service->isDeletable('image'));
    }
}
