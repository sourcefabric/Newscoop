<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Auth;

use Newscoop\Entity\User;

/**
 */
class DoctrineAuthServiceTest extends \PHPUnit_Framework_TestCase
{
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
    }

    public function testService()
    {
        $service = new DoctrineAuthService($this->em);
        $this->assertInstanceOf('Newscoop\Services\Auth\DoctrineAuthService', $service);
        $this->assertInstanceOf('Zend_Auth_Adapter_Interface', $service);
    }

    public function testFluenInterface()
    {
        $service = new DoctrineAuthService($this->em);
        $this->assertEquals($service, $service->setUsername('foo'));
        $this->assertEquals($service, $service->setPassword('bar'));
        $this->assertEquals($service, $service->setAdmin(true));
    }

    public function testAuthenticateNotFound()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('username' => 'john')))
            ->will($this->returnValue(NULL));

        $service = new DoctrineAuthService($this->em);
        $service->setUsername('john');
        $service->setPassword('secret');

        $result = $service->authenticate();
        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(\Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
    }

    public function testAuthenticateInactive()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));

        $user = $this->getMock('Newscoop\Entity\User');

        $user->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(FALSE));

        $user->expects($this->never())
            ->method('checkPassword');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('username' => 'john')))
            ->will($this->returnValue($user));

        $service = new DoctrineAuthService($this->em);
        $service->setUsername('john');
        $service->setPassword('secret');
        $result = $service->authenticate();

        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(\Zend_Auth_Result::FAILURE_UNCATEGORIZED, $result->getCode());
    }

    public function testAuthenticateInvalid()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));

        $user = $this->getMock('Newscoop\Entity\User');

        $user->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(TRUE));

        $user->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(FALSE));

        $user->expects($this->once())
            ->method('checkPassword')
            ->with($this->equalTo('secret'))
            ->will($this->returnValue(FALSE));

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('username' => 'john')))
            ->will($this->returnValue($user));

        $service = new DoctrineAuthService($this->em);
        $service->setUsername('john');
        $service->setPassword('secret');

        $result = $service->authenticate();
        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(\Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
    }

    public function testAuthenticateValid()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));

        $user = $this->getMock('Newscoop\Entity\User');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('username' => 'john')))
            ->will($this->returnValue($user));

        $user->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(TRUE));

        $user->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(FALSE));

        $user->expects($this->once())
            ->method('checkPassword')
            ->with($this->equalTo('secret'))
            ->will($this->returnValue(TRUE));

        $user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(3));

        $service = new DoctrineAuthService($this->em);
        $service->setUsername('john')->setPassword('secret');

        $result = $service->authenticate();
        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(\Zend_Auth_Result::SUCCESS, $result->getCode());
        $this->assertEquals(3, $result->getIdentity());
    }

    public function testAuthenticateValidAdmin()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));

        $user = $this->getMock('Newscoop\Entity\User');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('username' => 'john')))
            ->will($this->returnValue($user));

        $user->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(TRUE));

        $user->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(TRUE));

        $user->expects($this->once())
            ->method('checkPassword')
            ->with($this->equalTo('secret'))
            ->will($this->returnValue(TRUE));

        $user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(3));

        $service = new DoctrineAuthService($this->em);
        $service->setUsername('john')->setPassword('secret')->setAdmin(TRUE);

        $result = $service->authenticate();
        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(\Zend_Auth_Result::SUCCESS, $result->getCode());
        $this->assertEquals(3, $result->getIdentity());
    }

    public function testAuthenticateInvalidAdmin()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));

        $user = $this->getMock('Newscoop\Entity\User');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('username' => 'john')))
            ->will($this->returnValue($user));

        $user->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(TRUE));

        $user->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(FALSE));

        $service = new DoctrineAuthService($this->em);
        $service->setUsername('john')->setPassword('secret')->setAdmin(TRUE);

        $result = $service->authenticate();
        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(\Zend_Auth_Result::FAILURE_UNCATEGORIZED, $result->getCode());
    }
}
