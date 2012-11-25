<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

/**
 */
class BlogServiceTest extends \PHPUnit_Framework_TestCase
{
    const GROUP_BLOGGER = 1;
    const GROUP_OTHER = 2;

    /** @var Newscoop\Services\BlogService */
    private $service;

    /** @var array */
    private $config = array(
        'role' => self::GROUP_BLOGGER,
        'publication' => 1,
        'issue' => 1,
        'type' => 'bloginfo',
    );

    public function setUp()
    {
        $this->service = new BlogService($this->config);
    }

    public function testBlogService()
    {
        $this->assertInstanceOf('Newscoop\Services\BlogService', $this->service);
    }

    public function testIsBloggerNoGroups()
    {
        $user = $this->getMock('Newscoop\Entity\User');
        $user->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue(array()));

        $this->assertFalse($this->service->isBlogger($user));
    }

    public function testIsBloggerNoBloggerGroup()
    {
        $user = $this->getMock('Newscoop\Entity\User');
        $group = $this->getMock('Newscoop\Entity\User\Group');

        $user->expects($this->any())
            ->method('getGroups')
            ->will($this->returnValue(array($group)));

        $group->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::GROUP_OTHER));

        $this->assertFalse($this->service->isBlogger($user));
    }

    public function testIsBlogger()
    {
        $user = $this->getMock('Newscoop\Entity\User');
        $group = $this->getMock('Newscoop\Entity\User\Group');

        $user->expects($this->any())
            ->method('getGroups')
            ->will($this->returnValue(array($group)));

        $group->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::GROUP_BLOGGER));

        $this->assertTrue($this->service->isBlogger($user));
    }

    public function testGetSection()
    {
        $this->markTestSkipped('Requires adodb mock.');
        $user = new User('uname');
        $this->assertNull($this->service->getSection($user));
    }
}
