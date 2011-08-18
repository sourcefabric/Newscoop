<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\UserService */
    protected $service;

    /** @var Zend_Auth */
    protected $auth;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\Entity\Repository\UserRepository */
    protected $repository;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth = $this->getMockBuilder('Zend_Auth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Newscoop\Entity\Repository\UserRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new UserService($this->em, $this->auth);
    }

    public function testUser()
    {
        $service = new UserService($this->em, $this->auth);
        $this->assertInstanceOf('Newscoop\Services\UserService', $service);
    }

    public function testGetCurrentUser()
    {
        $user = $this->getMock('Newscoop\Entity\User');

        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(1));

        $this->expectGetRepository();

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($user));

        $this->assertEquals($user, $this->service->getCurrentUser());
        $this->assertEquals($user, $this->service->getCurrentUser()); // test if getting user only once
    }

    public function testGetCurrentUserNotAuthorized()
    {
        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $this->assertNull($this->service->getCurrentUser());
    }

    public function testFindAll()
    {
        $this->expectGetRepository();

        $this->repository->expects($this->once())
            ->method('findAll')
            ->with()
            ->will($this->returnValue(array(1, 2)));

        $this->assertEquals(array(1, 2), $this->service->findAll());
    }

    public function testFind()
    {
        $user = new User();
        $this->expectGetRepository();
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($user));

        $this->assertEquals($user, $this->service->find(1));
    }

    public function testCreate()
    {
        $this->expectGetRepository();

        $userdata = array(
            'username' => 'foobar',
            'first_name' => 'foo',
            'last_name' => 'bar',
            'email' => 'foo@bar.com',
        );

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('Newscoop\Entity\User'), $this->equalTo($userdata));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $this->assertInstanceOf('Newscoop\Entity\User', $this->service->create($userdata));
    }

    public function testDelete()
    {
        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(3));

        $user = new User();
        $this->em->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($user));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $this->service->delete($user);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeleteHimself()
    {
        $user = new User();
        $property = new \ReflectionProperty($user, 'id');
        $property->setAccessible(TRUE);
        $property->setValue($user, 1);

        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(1));

        $this->service->delete($user);
    }

    protected function expectGetRepository()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));
    }
}
