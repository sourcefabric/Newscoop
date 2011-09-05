<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User,
    Newscoop\Entity\UserToken;

class UserTokenServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\UserService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new UserTokenService($this->em);
    }

    public function testService()
    {
        $service = new UserTokenService($this->em);
        $this->assertInstanceOf('Newscoop\Services\UserTokenService', $service);
    }

    public function testGenerateToken()
    {
        $user = new User();

        $token = $this->service->generateToken($user, 'test.action');
        $this->assertRegExp('/^[a-zA-Z0-9]{40}$/', $token);
    }

    public function testCheckTokenInvalid()
    {
        $user = new User();
        $this->expectsFind('qwerty', 'test', $user, null);
        $this->assertFalse($this->service->checkToken($user, 'qwerty', 'test'));
    }

    public function testCheckTokenValid()
    {
        $user = new User();
        $token = new UserToken($user, 'test', 'qwerty');
        $this->expectsFind('qwerty', 'test', $user, $token);
        $this->assertTrue($this->service->checkToken($user, 'qwerty', 'test'));
    }

    public function testCheckInvalidDate()
    {
        $user = new User();
        $token = new UserToken($user, 'test', 'qwerty');

        $property = new \ReflectionProperty($token, 'created');
        $property->setAccessible(true);
        $property->setValue($token, new \DateTime('-3 days'));

        $this->expectsFind('qwerty', 'test', $user, $token);
        $this->assertFalse($this->service->checkToken($user, 'qwerty', 'test'));
    }

    private function expectsFind($token, $action, $user, $return)
    {
        $this->em->expects($this->once())
            ->method('find')
            ->with($this->equalTo('Newscoop\Entity\UserToken'), $this->equalTo(array('user' => $user->getId(), 'token' => $token, 'action' => $action)))
            ->will($this->returnValue($return));
    }
}
