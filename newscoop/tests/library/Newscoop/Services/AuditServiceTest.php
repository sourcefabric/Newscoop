<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\EventDispatcher\Events\GenericEvent;

class AuditServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\Services\AuditService */
    protected $service;

    /** @var Newscoop\Services\UserService */
    protected $userService;

    /** @var Newscoop\Entity\Repository\AuditRepository */
    protected $repository;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Newscoop\Entity\Repository\AuditRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->userService = $this->getMockBuilder('Newscoop\Services\UserService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new AuditService($this->em, $this->userService);
    }

    public function testAudit()
    {
        $this->assertInstanceOf('Newscoop\Services\AuditService', $this->service);
    }

    public function testUpdate()
    {
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('Newscoop\Entity\AuditEvent'), $this->contains('test'));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\AuditEvent'))
            ->will($this->returnValue($this->repository));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $event = new GenericEvent($this);
        $event->setName('event.test');
        $this->service->update($event);
    }

    public function testFindAll()
    {
        $all = array(1, 2, 3);
        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($all));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\AuditEvent'))
            ->will($this->returnValue($this->repository));
        
        $this->assertEquals($all, $this->service->findAll());
    }

    public function testFindBy()
    {
        $result = array(1, 2, 4);
        $criteria = array('resource' => 'article');
        $orderBy = array('created');
        $limit = 5;
        $offset = 1;
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo($criteria), $this->equalTo($orderBy), $this->equalTo($limit), $this->equalTo($offset))
            ->will($this->returnValue($result));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\AuditEvent'))
            ->will($this->returnValue($this->repository));

        $this->assertEquals($result, $this->service->findBy($criteria, $orderBy, $limit, $offset));
    }
}
