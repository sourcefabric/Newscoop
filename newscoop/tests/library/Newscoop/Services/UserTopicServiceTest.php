<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User,
    Newscoop\Entity\Topic,
    Newscoop\Entity\UserTopic;

class UserTopicServiceTest extends \RepositoryTestCase
{
    /** @var Newscoop\Services\UserTopicService */
    protected $service;

    /** @var Newscoop\Entity\User */
    protected $user;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\Topic', 'Newscoop\Entity\UserTopic', 'Newscoop\Entity\Acl\Role');

        $this->service = new UserTopicService($this->em);
        $this->user = new User('name');
        $this->em->persist($this->user);
        $this->em->flush();
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\UserTopicService', $this->service);
    }

    public function testGetTopicsEmpty()
    {
        $this->assertEmpty($this->service->getTopics($this->user));
    }

    public function testFollow()
    {
        $topic = new Topic(1, 1, 'name');
        $this->em->persist($topic);

        $this->service->followTopic($this->user, $topic);

        $topics = $this->service->getTopics($this->user);
        $this->assertEquals(1, sizeof($topics));
        $this->assertEquals($topic, $topics[0]);
    }

    public function testUpdateTopics()
    {
        $this->em->persist($topic = new Topic(1, 1, '1'));
        $this->em->persist(new Topic(2, 1, '2'));
        $this->em->persist(new Topic(3, 1, '3'));
        $this->em->flush();

        $this->service->followTopic($this->user, $topic);

        $this->service->updateTopics($this->user, array(
            '1' => 'false',
            '2' => 'false',
            '3' => 'true',
        ));

        $topics = $this->service->getTopics($this->user);
        $this->assertEquals(1, count($topics));
        $this->assertEquals('3', current($topics)->getName());
    }
}
