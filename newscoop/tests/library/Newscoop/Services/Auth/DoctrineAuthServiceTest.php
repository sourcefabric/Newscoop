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
class DoctrineAuthServiceTest extends \RepositoryTestCase
{
    const EMAIL = 'john@example.com';
    const USERNAME = 'john';
    const PASSWORD = 'secret';

    /** @var Newscoop\Services\Auth\DoctrineAuthService */
    private $service;

    /** @var Newscoop\Entity\User */
    private $user;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role');
        $this->service = new DoctrineAuthService($this->em);

        $this->user = new User();
        $this->user->setEmail(self::EMAIL);
        $this->user->setUsername(self::USERNAME);
        $this->user->setPassword(self::PASSWORD);
        $this->em->persist($this->user);
        $this->em->flush();
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\Auth\DoctrineAuthService', $this->service);
        $this->assertInstanceOf('Zend_Auth_Adapter_Interface', $this->service);
    }

    public function testFluenInterface()
    {
        $this->assertEquals($this->service, $this->service->setUsername('foo'));
        $this->assertEquals($this->service, $this->service->setPassword('bar'));
        $this->assertEquals($this->service, $this->service->setAdmin(true));
    }

    public function testAuthenticateNotFound()
    {
        $this->service->setEmail(self::EMAIL . 'salt')->setPassword(self::PASSWORD);

        $result = $this->service->authenticate();

        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(\Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
    }

    public function testAuthenticateInactive()
    {
        $this->service->setEmail(self::EMAIL)->setPassword(self::PASSWORD);

        $result = $this->service->authenticate();

        $this->assertEquals(\Zend_Auth_Result::FAILURE_UNCATEGORIZED, $result->getCode());
    }

    public function testAuthenticateWithUsernameOnFrontend()
    {
        $this->user->setActive();
        $this->em->persist($this->user);
        $this->em->flush();

        $this->service->setUsername(self::USERNAME)->setPassword(self::PASSWORD);

        $result = $this->service->authenticate();

        $this->assertEquals(\Zend_Auth_Result::SUCCESS, $result->getCode());
    }

    public function testAuthenticateInvalid()
    {
        $this->service->setEmail(self::EMAIL)->setPassword(sha1(self::PASSWORD));

        $this->user->setActive();
        $this->em->persist($this->user);
        $this->em->flush();

        $result = $this->service->authenticate();
        $this->assertEquals(\Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
    }

    public function testAuthenticateValid()
    {
        $this->user->setActive();
        $this->em->persist($this->user);
        $this->em->flush();

        $this->service->setEmail(self::EMAIL)->setPassword(self::PASSWORD);

        $result = $this->service->authenticate();
        $this->assertEquals(\Zend_Auth_Result::SUCCESS, $result->getCode());
        $this->assertEquals($this->user->getId(), $result->getIdentity());
    }

    public function testAuthenticateValidAdmin()
    {
        $this->user->setActive();
        $this->user->setAdmin(TRUE);
        $this->em->persist($this->user);
        $this->em->flush();

        $this->service->setUsername(self::USERNAME)->setPassword(self::PASSWORD)->setAdmin(TRUE);

        $result = $this->service->authenticate();

        $this->assertEquals(\Zend_Auth_Result::SUCCESS, $result->getCode());
    }

    public function testAuthenticateInvalidAdmin()
    {
        $this->service->setUsername('john')->setPassword('secret')->setAdmin(TRUE);

        $result = $this->service->authenticate();

        $this->assertEquals(\Zend_Auth_Result::FAILURE_UNCATEGORIZED, $result->getCode());
    }
}
