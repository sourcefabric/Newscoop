<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 */
class UserTest extends \RepositoryTestCase
{
    /** @var Newscoop\Entity\Repository\UserRepository */
    protected $repository;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User');
        $this->repository = $this->em->getRepository('Newscoop\Entity\User');
    }

    public function testUser()
    {
        $this->assertInstanceOf('Newscoop\Entity\User', new User());
        $this->assertInstanceOf('Newscoop\Entity\Repository\UserRepository', $this->repository);
    }

    public function testSave()
    {
        $user = new User();
        $this->repository->save($user, array(
            'username' => 'foo.bar',
            'password' => 'secret',
            'email' => 'foo@bar.com',
            'first_name' => 'Foo',
            'last_name' => 'Bar',
            'status' => User::STATUS_INACTIVE,
        ));

        $this->em->flush();

        $users = $this->repository->findAll();
        $this->assertEquals(1, sizeof($users));

        $user = array_shift($users);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('foo.bar', $user->getUsername());
        $this->assertTrue($user->checkPassword('secret'));
        $this->assertEquals('foo@bar.com', $user->getEmail());
        $this->assertEquals('Foo', $user->getFirstName());
        $this->assertEquals('Bar', $user->getLastName());
        $this->assertEquals(User::STATUS_INACTIVE, $user->getStatus());
        $this->assertFalse($user->isActive());

        $this->assertLessThan(2, time() - $user->getCreated()->getTimestamp());
    }

    public function testSaveUsernameOnly()
    {
        $user = new User();
        $this->repository->save($user, array(
            'username' => 'foobar',
        ));

        $this->em->flush();

        $this->assertEquals(1, $user->getId());
        $this->assertEquals('foobar', $user->getUsername());
        $this->assertEmpty($user->getFirstName());
        $this->assertEmpty($user->getLastName());
        $this->assertEmpty($user->getEmail());
    }

    public function testSetPassword()
    {
        $user = new User();
        $user->setPassword('test');

        $property = new \ReflectionProperty($user, 'password');
        $property->setAccessible(TRUE);

        $this->assertTrue($user->checkPassword('test'));
        $this->assertFalse($user->checkPassword('test1'));

        $hash = $property->getValue($user);
        $user->setPassword('test');
        $this->assertNotEquals($hash, $property->getValue($user)); // expect different hash for same password

        $property->setValue($user, sha1('test'));
        $this->assertFalse($user->checkPassword('test1'));
        $this->assertEquals(sha1('test'), $property->getValue($user));

        $this->assertTrue($user->checkPassword('test'));
        $this->assertNotEquals(sha1('test'), $property->getValue($user)); // expect password update on check
        $this->assertNotEquals($hash, $property->getValue($user)); // different from old
        $this->assertTrue($user->checkPassword('test'));
        $this->assertFalse($user->checkPassword(sha1('test')));
    }

    public function testFindAll()
    {
        $this->assertEmpty($this->repository->findAll());
    }

    public function testGetGroups()
    {
        $user = new User();
        $this->assertEquals(0, count($user->getGroups()));
    }

    public function testSetGetRole()
    {
        $role = $this->getMock('Newscoop\Entity\Acl\Role');
        $role->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(3));

        $user = new User();
        $user->setRole($role);
        $this->assertEquals(3, $user->getRoleId());
    }
}
