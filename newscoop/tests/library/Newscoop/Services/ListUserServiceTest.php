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
    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\UserAttribute', 'Newscoop\Entity\Acl\Role');
    }

    public function testUser()
    {
        $service = new ListUserService($this->em);
        $this->assertInstanceOf('Newscoop\Services\ListUserService', $service);
    }

    public function testOrderByRank()
    {
        $user = new User();
        $user->setUsername('user1');
        $this->em->persist($user);
        $this->em->flush();
        $user->addAttribute('points', 1);


        $user = new User();
        $user->setUsername('user2');
        $this->em->persist($user);
        $this->em->flush();
        $user->addAttribute('points', 2);

        $this->em->flush();
        $this->em->clear();

        $service = new ListUserService($this->em);
        $this->assertEquals(2, $service->countBy());
    }
}
