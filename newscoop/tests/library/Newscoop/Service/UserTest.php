<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Service\User */
    protected $service;

    /** @var Zend_Auth */
    protected $auth;

    /** @var Newscoop\Entity\Repository\UserRepository */
    protected $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('Newscoop\Entity\Repository\UserRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth = $this->getMockBuilder('Zend_Auth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new User($this->repository, $this->auth);
    }

    public function testUser()
    {
        $service = new User($this->repository, $this->auth);
        $this->assertInstanceOf('Newscoop\Service\User', $service);
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

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo('Newscoop\Entity\User'), $this->equalTo(1))
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
}
