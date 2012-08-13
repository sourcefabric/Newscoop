<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User,
    Newscoop\Entity\User\Group,
    Newscoop\Entity\Author;

class ListUserServiceTest extends \RepositoryTestCase
{
    /** Newscoop\Services\ListUserService */
    protected $service;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\UserAttribute', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\User\Group', 'Newscoop\Entity\Author');
        $this->service = new ListUserService(array('blog' => array(
            'role' => 1,
        )), $this->em);
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
        $this->addUser('7');

        $list1 = array_map(function($user) {
            return $user->getId();
        }, $this->service->getRandomList());

        $this->assertEquals(4, count($list1));
    }

    public function testGetEditors()
    {
        $blogRole = new Group();
        $blogRole->setName('blogger');

        $author1 = new Author('tic1', 'toc');
        $author2 = new Author('tic2', 'toc');

        $this->em->persist($blogRole);
        $this->em->persist($author1);
        $this->em->persist($author2);
        $this->em->flush();

        $user = new User();
        $user->setUsername('user')
            ->setEmail('user@example.com')
            ->setActive(true);

        $admin = new User();
        $admin->setUsername('admin')
            ->setEmail('admin@example.com')
            ->setActive(true)
            ->setAdmin(true);

        $editor = new User();
        $editor->setUsername('editor')
            ->setEmail('editor@example.com')
            ->setActive(true)
            ->setAdmin(true)
            ->setAuthor($author1);

        $blogger = new User();
        $blogger->setUsername('blogger')
            ->setEmail('blogger@example.com')
            ->setActive(true)
            ->setAdmin(true)
            ->setAuthor($author2)
            ->addUserType($blogRole);

        $this->em->persist($user);
        $this->em->persist($admin);
        $this->em->persist($editor);
        $this->em->persist($blogger);
        $this->em->flush();

        $service = new ListUserService(array('blog' => array(
            'role' => $blogRole->getId(),
        )), $this->em);

        $editors = $service->findEditors();
        $this->assertEquals(1, count($editors));
        $this->assertEquals($editor->getId(), $editors[0]->getId());
        $this->assertEquals(1, $service->getEditorsCount());
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
