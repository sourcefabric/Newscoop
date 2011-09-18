<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Auth;

use Newscoop\Entity\User,
    Newscoop\Entity\UserIdentity;

/**
 */
class SocialAuthServiceTest extends \RepositoryTestCase
{
    /** @var Newscoop\Services\Auth\SocialAuthService */
    protected $service;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\UserIdentity', 'Newscoop\Entity\Acl\Role');
        $this->service = new SocialAuthService($this->em);
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\Auth\SocialAuthService', $this->service);
        $this->assertInstanceOf('Zend_Auth_Adapter_Interface', $this->service);
    }

    public function testFluenInterface()
    {
        $this->assertEquals($this->service, $this->service->setProvider('foo'));
        $this->assertEquals($this->service, $this->service->setProviderUserId('bar'));
    }

    public function testAuthenticateNotFound()
    {
        $result = $this->service->setProvider('foo')->setProviderUserId('bar')->authenticate();
        $this->assertEquals(\Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
    }

    public function testAuthenticateExisting()
    {
        $user = new User('username');
        $this->em->persist($user);
        $this->em->flush();

        $this->service->addIdentity($user, 'foo', 'bar');
        $result = $this->service->setProvider('foo')->setProviderUserId('bar')->authenticate();
        $this->assertEquals(\Zend_Auth_Result::SUCCESS, $result->getCode());
    }
}
