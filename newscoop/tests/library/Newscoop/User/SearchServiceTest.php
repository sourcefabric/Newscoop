<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\User;

use Newscoop\Entity\User;

/**
 */
class SearchServiceTest extends \TestCase
{
    public function setUp()
    {
        $this->imageService = $this->getMockBuilder('Newscoop\Image\ImageService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new SearchService($this->imageService);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\User\SearchService', $this->service);
        $this->assertInstanceOf('Newscoop\Search\ServiceInterface', $this->service);
    }

    public function testGetDocumentId()
    {
        $user = new User();
        $this->assertEquals('user-0', $this->service->getDocumentId($user));
    }

    public function testGetDocument()
    {
        $user = new User();
        $user->setUsername('name');
        $user->setImage('someimage.jpg');
        $user->addAttribute('bio', 'abc');

        $this->imageService->expects($this->once())
            ->method('getUserImage')
            ->with($this->equalTo($user))
            ->will($this->returnValue('imagesrc'));

        $this->assertEquals(array(
            'id' => 'user-0',
            'type' => 'user',
            'user' => 'name',
            'bio' => 'abc',
            'image' => 'imagesrc',
            'published' => gmdate('Y-m-d\TH:i:s\Z'),
        ), $this->service->getDocument($user));
    }

    public function testIsIndexed()
    {
        $user = new User();
        $this->assertFalse($this->service->isIndexed($user));
        $user->setIndexed(new \DateTime());
        $this->assertTrue($this->service->isIndexed($user));
    }

    public function testIsIndexable()
    {
        $user = new User();
        $this->assertFalse($this->service->isIndexable($user));

        $user->setPublic(true);

        $this->assertFalse($this->service->isIndexable($user));

        $user->setActive();

        $this->assertTrue($this->service->isIndexable($user));

        $user->setPublic(false);

        $this->assertFalse($this->service->isIndexable($user));
    }
}
