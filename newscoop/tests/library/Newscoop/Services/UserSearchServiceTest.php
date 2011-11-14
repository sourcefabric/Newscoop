<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

class UserSearchServiceTest extends \RepositoryTestCase
{
    /** @var \Newscoop\Services\UserSearchService */
    protected $service;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role');

        $this->service = new UserSearchService($this->em);

        $user = new User();
        $user->setEmail('foo@bar.name');
        $user->setUsername('FooBar');
        $this->em->persist($user);

        $user = new User();
        $user->setEmail('john@doe.name');
        $user->setUsername('john');
        $this->em->persist($user);

        $this->em->flush();

    }

    public function testSearchUserService()
    {
        $this->assertInstanceOf('Newscoop\Services\UserSearchService', $this->service);
    }

    public function testFindEmpty()
    {
        $this->assertEquals(0, count($this->service->find('tic')));
    }

    public function testFindByEmail()
    {
        $this->assertEquals(1, count($this->service->find('bar.n')));
        $this->assertEquals(2, count($this->service->find('name')));
        $this->assertEquals(2, count($this->service->find('NAME')));
    }

    public function testFindByUsername()
    {
        $this->assertEquals(1, count($this->service->find('oBar')));
        $this->assertEquals(1, count($this->service->find('JOHN')));
    }

    public function testFindWithLimit()
    {
        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setEmail(uniqid("email_{$i}", true));
            $user->setUsername(uniqid("username_{$i}", true));
            $this->em->persist($user);
        }

        $this->em->flush();

        $this->assertEquals(25, count($this->service->find('email_')));
    }
}
