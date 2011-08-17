<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

class AuditServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\AuditService */
    protected $service;

    /** @var Newscoop\Services\UserService */
    protected $userService;

    /** @var Newscoop\Entity\Repository\AuditRepository */
    protected $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('Newscoop\Entity\Repository\AuditRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->userService = $this->getMockBuilder('Newscoop\Services\UserService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new AuditService($this->repository, $this->userService);
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

        $event = new \sfEvent($this, 'event.test');
        $this->service->update($event);
    }

    public function testFindAll()
    {
        $all = array(1, 2, 3);
        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($all));
        
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

        $this->assertEquals($result, $this->service->findBy($criteria, $orderBy, $limit, $offset));
    }
}
