<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

class UserServiceTest extends \RepositoryTestCase
{
    /** @var Newscoop\Services\UserService */
    protected $service;

    /** @var Zend_Auth */
    protected $auth;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\Entity\Repository\UserRepository */
    protected $repository;

    /** @var Newscoop\Entity\User */
    private $user;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\UserAttribute');

        $this->auth = $this->getMockBuilder('Zend_Auth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->em->getRepository('Newscoop\Entity\User');

        $this->service = new UserService($this->em, $this->auth);

        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setUsername('test');
        $this->user->setFirstName('Foo');
        $this->user->setLastName('Bar');
    }

    public function testUser()
    {
        $this->assertInstanceOf('Newscoop\Services\UserService', $this->service);
    }

    public function testGetCurrentUser()
    {
        $this->em->persist($this->user);
        $this->em->flush();

        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(1));

        $this->assertEquals($this->user, $this->service->getCurrentUser());
    }

    public function testGetCurrentUserNotAuthorized()
    {
        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $this->assertNull($this->service->getCurrentUser());
    }

    public function testFind()
    {
        $this->em->persist($this->user);
        $this->em->flush();

        $this->assertEquals($this->user, $this->service->find(1));
    }

    public function testSaveNew()
    {
        $userdata = array(
            'username' => 'foobar',
            'first_name' => 'foo',
            'last_name' => 'bar',
            'email' => 'foo@bar.com',
        );

        $user = $this->service->save($userdata);
        $this->assertInstanceOf('Newscoop\Entity\User', $user);
        $this->assertEquals(1, $user->getId());
    }

    public function testDeleteActive()
    {
        $this->user->setActive(true);
        $this->em->persist($this->user);
        $this->em->flush();

        $this->user->addAttribute('tic', 'toc');
        $this->em->persist($this->user);
        $this->em->flush();

        $this->assertTrue($this->user->isActive());

        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(3));

        sleep(2); // for testing difference in create/update time
        $this->service->delete($this->user);

        $this->assertFalse($this->user->isActive());
        $this->assertFalse($this->user->isPublic());
        $this->assertFalse($this->user->isAdmin());

        $this->assertEmpty($this->user->getEmail());
        $this->assertEmpty($this->user->getFirstName());
        $this->assertEmpty($this->user->getLastName());
        $this->assertEmpty($this->user->getAttribute('tic'));
        $this->assertEmpty($this->user->getAttributes());
        $this->assertGreaterThan($this->user->getCreated()->getTimestamp(), $this->user->getUpdated()->getTimestamp());
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

    public function testDeletePending()
    {
        $user = $this->service->createPending('test@example.com');

        $this->service->delete($user);

        $this->assertEmpty($this->service->find($user->getId()));
    }

    public function testGenerateUsername()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('Foo Bar');
        $this->em->persist($user);
        $this->em->flush();

        $this->assertEquals('Foos Bar', $this->service->generateUsername('Foos', 'Bar'));
        $this->assertEquals('Foo Bar1', $this->service->generateUsername('Foo', 'Bar'));
        $this->assertEquals('', $this->service->generateUsername(' ', ' '));
        $this->assertEquals('Foo', $this->service->generateUsername('Foo', ''));
        $this->assertEquals('Bar', $this->service->generateUsername('', 'Bar'));
        $this->assertEquals('', $this->service->generateUsername('!@#$%^&*()+-={}[]\\|;\':"§-?/.>,<', ''));
        $this->assertEquals('_', $this->service->generateUsername('_', ''));
        $this->assertEquals('Foo Bar Jr', $this->service->generateUsername('Foo  Bar ', ' Jr '));
    }

    public function testSetActive()
    {
        $this->assertFalse($this->user->isActive());

        $this->service->setActive($this->user);

        $this->assertTrue($this->user->isActive());
    }

    public function testSave()
    {
        $data = array(
            'email' => 'info@example.com',
        );

        $this->assertEquals($this->user, $this->service->save($data, $this->user));
        $this->assertGreaterThan(0, $this->user->getId());
        $this->assertEquals('info@example.com', $this->user->getEmail());
    }

    public function testCreatePending()
    {
        $user = $this->service->createPending('email@example.com');
        $this->assertInstanceOf('Newscoop\Entity\User', $user);
        $this->assertTrue($user->isPublic());
        $this->assertGreaterThan(0, $user->getId());

        $next = $this->service->createPending('email@example.com');
        $this->assertEquals($user->getId(), $next->getId());
    }

    public function testSavePending()
    {
        $user = $this->service->createPending('info@example.com');
        $this->service->savePending(array('username' => 'test'), $user);

        $this->assertTrue($user->isActive());
        $this->assertTrue($user->isPublic());
    }

    /**
     * @expectedException InvalidArgumentException username
     */
    public function testUsernameCaseSensitivity()
    {
        $this->service->save(array(
            'email' => 'one@example.com',
            'username' => 'Foo Bar',
        ));

        $this->service->save(array(
            'email' => 'two@example.com',
            'username' => 'foo bar',
        ));
    }

    public function testGetPublicUserCount()
    {
        $this->assertEquals(0, $this->service->getPublicUserCount());

        $this->user->setActive();
        $this->em->persist($this->user);
        $this->em->flush();

        $this->assertEquals(0, $this->service->getPublicUserCount());

        $this->user->setPublic();
        $this->em->flush();

        $this->assertEquals(1, $this->service->getPublicUserCount());
    }
}
