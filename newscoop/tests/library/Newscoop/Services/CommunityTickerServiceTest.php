<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

class CommunityTickerServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $em;

    protected $repository;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Newscoop\Entity\Repository\Events\CommunityTickerEventRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new CommunityTickerService($this->em);
    }

    public function testAudit()
    {
        $this->assertInstanceOf('Newscoop\Services\CommunityTickerService', $this->service);
    }

    public function testUpdate()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\Events\CommunityTickerEvent'))
            ->will($this->returnValue($this->repository));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $this->repository->expects($this->once())
            ->method('save')
            ->with();

        $event = new \sfEvent($this, 'event.test', array(
            'user' => 1,
            'params' => array(
                'title' => 'Test',
            ),
        ));

        $this->service->update($event);
    }

    public function testFindAll()
    {
        $this->getRepository();

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array()), $this->equalTo(array('id' => 'desc')), $this->equalTo(10), $this->equalTo(0))
            ->will($this->returnValue('result'));

        $this->assertEquals('result', $this->service->findAll(10));
    }

    public function testFindByUser()
    {
        $this->getRepository();

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('user' => 1)), $this->equalTo(array('id' => 'desc')), $this->equalTo(10), $this->equalTo(0))
            ->will($this->returnValue('result'));

        $this->assertEquals('result', $this->service->findByUser(1, 10));
    }

    protected function getRepository()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\Events\CommunityTickerEvent'))
            ->will($this->returnValue($this->repository));
    }
}
