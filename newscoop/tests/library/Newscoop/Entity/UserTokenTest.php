<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 */
class UserTokenTest extends \RepositoryTestCase
{
    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\UserAttribute', 'Newscoop\Entity\UserToken', 'Newscoop\Entity\Acl\Role');
        $this->repository = $this->em->getRepository('Newscoop\Entity\UserToken');
    }

    public function testUserToken()
    {
        $this->assertInstanceOf('Newscoop\Entity\UserToken', new UserToken(new User(), 'token', 'action'));
    }

    public function testSave()
    {
        $user = new User('email');
        $this->em->persist($user);
        $this->em->flush();

        $userToken = new UserToken($user, 'test_action', 'test_token');
        $this->assertAttributeEquals('test_action', 'action', $userToken);
        $this->assertAttributeEquals('test_token', 'token', $userToken);
        $this->assertAttributeEquals($user, 'user', $userToken);

        $this->em->persist($userToken);
        $this->em->flush();
        $this->em->clear();

        $tokens = $this->repository->findAll();
        $this->assertEquals(1, sizeof($tokens));

        $token = $this->repository->find(array(
            'user' => $user->getId(),
            'action' => 'test_action',
            'token' => 'test_token',
        ));

        $this->assertNotNull($token);
    }
}
