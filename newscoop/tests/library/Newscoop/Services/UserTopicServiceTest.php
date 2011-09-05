<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User,
    Newscoop\Entity\Topic,
    Newscoop\Entity\UserTopic;

class UserTopicServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\UserTopicService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new UserTopicService($this->em);
    }

    public function testService()
    {
        $service = new UserTopicService($this->em);
        $this->assertInstanceOf('Newscoop\Services\UserTopicService', $service);
    }

    public function testGetTopicsEmpty()
    {
        $user = new User();

        $this->em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('user' => $user->getId())))
            ->will($this->returnValue(array()));

        $this->assertEmpty($this->service->getTopics($user));
    }

    public function testFollow()
    {
        $user = new User();
        $topic = new Topic(1, 1, 'name');
        $rel = new UserTopic($user, $topic);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($rel));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $this->service->followTopic($user, $topic);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('user' => $user->getId())))
            ->will($this->returnValue(array($rel)));

        $topics = $this->service->getTopics($user);
        $this->assertEquals(1, sizeof($topics));
        $this->assertEquals($topic, $topics[0]);
    }
}
