<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 */
class UserTopicTest extends \TestCase
{
    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;

    public function setUp()
    {
        $this->em = $this->setUpOrm('Newscoop\Entity\User', 'Newscoop\Entity\UserTopic', 'Newscoop\Entity\Topic', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\Language');
        $this->repository = $this->em->getRepository('Newscoop\Entity\UserTopic');
        $this->language = new Language();
    }

    public function testUserToken()
    {
        $user = new User('uname');
        $topic = new Topic(1, $this->language, 'name');
        $userTopic = new UserTopic($user, $topic);
        $this->assertInstanceOf('Newscoop\Entity\UserTopic', $userTopic);
    }

    /**
     * @expects Exception
     */
    public function testSave()
    {
        $user = new User('uname');
        $this->em->persist($user);
        $this->em->persist($this->language);
        $this->em->flush();

        $topic = new Topic(1, $this->language, 'name');
        $this->em->persist($topic);
        $this->em->flush();

        $userTopic = new UserTopic($user, $topic);
        $this->em->persist($userTopic);
        $this->em->flush();
        $this->em->clear();

        $userTopics = $this->repository->findBy(array(
            'user' => $user->getId(),
        ));

        $this->assertEquals(1, sizeof($userTopics));
        $this->assertEquals($topic->getName(), $userTopics[0]->getTopic()->getName());
    }
}
