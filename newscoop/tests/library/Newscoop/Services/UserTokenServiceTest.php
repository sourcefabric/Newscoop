<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User,
    Newscoop\Entity\UserToken;

class UserTokenServiceTest extends \RepositoryTestCase
{
    /** @var Newscoop\Services\UserTokenService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\Entity\User */
    protected $user;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\UserToken', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\User');
        $this->service = new UserTokenService($this->em);

        $this->user = new User('test');
        $this->user->setEmail('petr@localhost');
        $this->em->persist($this->user);
        $this->em->flush();
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\UserTokenService', $this->service);
    }

    public function testGenerateToken()
    {
        $token = $this->service->generateToken($this->user, 'test.action');
        $this->assertRegExp('/^[a-zA-Z0-9]{40}$/', $token);
        $this->assertTrue($this->service->checkToken($this->user, $token, 'test.action'));
    }

    public function testCheckTokenNotExisting()
    {
        $this->assertFalse($this->service->checkToken($this->user, 'qwerty', 'test'));
    }

    public function testCheckTokenValid()
    {
        $token = new UserToken($this->user, 'test', 'qwerty');
        $this->em->persist($token);
        $this->em->flush();

        $this->assertTrue($this->service->checkToken($this->user, 'qwerty', 'test'));
    }

    public function testCheckInvalidDate()
    {
        $token = new UserToken($this->user, 'test', 'qwerty');
        $this->em->persist($token);
        $this->em->flush();

        $property = new \ReflectionProperty($token, 'created');
        $property->setAccessible(true);
        $property->setValue($token, new \DateTime('-3 days'));

        $this->assertFalse($this->service->checkToken($this->user, 'qwerty', 'test'));
    }

    public function testInvalidateTokens()
    {
        $token = $this->service->generateToken($this->user, 'action');
        $this->service->invalidateTokens($this->user, 'action');
        $this->assertFalse($this->service->checkToken($this->user, $token, 'action'));
    }
}
