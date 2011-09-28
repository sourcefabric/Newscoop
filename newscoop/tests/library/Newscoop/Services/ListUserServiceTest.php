<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

class ListUserServiceTest extends \RepositoryTestCase
{
    /** Newscoop\Services\ListUserService */
    protected $service;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\UserAttribute', 'Newscoop\Entity\Acl\Role');
        $this->service = new ListUserService($this->em);
    }

    public function testUser()
    {
        $this->assertInstanceOf('Newscoop\Services\ListUserService', $this->service);
    }

    public function testOrderByRank()
    {
        $user = new User('email');
        $this->em->persist($user);
        $this->em->flush();
        $user->addAttribute('points', 1);

        $user = new User('email');
        $this->em->persist($user);
        $this->em->flush();
        $user->addAttribute('points', 2);

        $this->em->flush();
        $this->em->clear();

        $this->assertEquals(2, $this->service->countBy());
    }

    public function testGetRandomList()
    {
        $this->addUser('1');
        $this->addUser('2');
        $this->addUser('3', 0, 0);
        $this->addUser('4', 0, 1);
        $this->addUser('5', 1, 0);
        $this->addUser('6');

        $list1 = array_map(function($user) {
            return $user->getId();
        }, $this->service->getRandomList());

        $this->assertEquals(3, count($list1));

        $list2 = array_map(function($user) {
            return $user->getId();
        }, $this->service->getRandomList());

        $this->assertEquals(3, count($list2));

        $this->assertNotEquals($list1, $list2);
    }

    private function addUser($name, $status = 1, $isPublic = 1)
    {
        $user = new User($name);
        $user->setStatus($status);
        $user->setPublic($isPublic);
        $this->em->persist($user);
        $this->em->flush();
    }
}
