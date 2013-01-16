<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;
use Newscoop\Entity\Topic;
use Newscoop\Entity\UserTopic;
use Newscoop\Entity\Language;
use Newscoop\Topic\SaveUserTopicsCommand;

class UserTopicServiceTest extends \RepositoryTestCase
{
    const USER_ID = 1;
    const LANGUAGE_ID = 1;

    /** @var Newscoop\Services\UserTopicService */
    protected $service;

    /** @var Newscoop\Entity\User */
    protected $user;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\Topic', 'Newscoop\Entity\UserTopic', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\Language');

        $this->service = new UserTopicService($this->em);

        $this->language = new Language();
        $this->em->persist($this->language);

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
        $topic = new Topic(1, $this->language, 'name');
        $this->em->persist($topic);

        $this->service->followTopic($this->user, $topic);

        $topics = $this->service->getTopics($this->user);
        $this->assertEquals(1, sizeof($topics));
        $this->assertEquals($topic, $topics[0]);
    }

    public function testUpdateTopics()
    {
        $this->em->persist($topic = new Topic(1, $this->language, '1'));
        $this->em->persist(new Topic(2, $this->language, '2'));
        $this->em->persist(new Topic(3, $this->language, '3'));
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

    public function testUpdateTopicsIfDeleted()
    {
        $topic = new Topic(1, $this->language, 'deleted');
        $this->em->persist($topic);
        $this->em->flush();

        $this->service->followTopic($this->user, $topic);

        $this->em->remove($topic);
        $this->em->persist(new Topic(2, $this->language, 'next'));
        $this->em->flush();
        $this->em->clear();

        $this->service->updateTopics($this->user, array(
            2 => "false",
        ));

        $topics = $this->service->getTopics($this->user);
        $this->assertEquals(0, count($topics));
    }

    public function testGetTopicsDeleted()
    {
        $topic = new Topic(1, $this->language, 'test');
        $this->em->persist($topic);
        $this->em->flush();

        $this->service->followTopic($this->user, $topic);

        $this->em->remove($topic);
        $this->em->flush();
        $this->em->clear();

        $this->assertEquals(0, count($this->service->getTopics($this->user)));
    }

    public function testSaveUserTopicsCommand()
    {
        $topic = new Topic(1, $this->language, 'test');
        $this->em->persist($topic);
        $this->em->flush();

        $command = new SaveUserTopicsCommand(array(
            'selected' => array(1),
            'userId' => self::USER_ID,
            'languageId' => self::LANGUAGE_ID,
        ));

        $this->service->saveUserTopics($command);

        $topics = $this->service->getTopics(self::USER_ID);
        $this->assertEquals(1, count($topics));
        $this->assertEquals('test', $topics[0]->getView()->name);
    }

    public function testSaveUserTopicsCommandReplace()
    {
        $this->em->persist(new Topic(1, $this->language, 'topic1'));
        $this->em->persist(new Topic(2, $this->language, 'topic2'));
        $this->em->flush();

        $command = new SaveUserTopicsCommand(array(
            'selected' => array(1),
            'userId' => self::USER_ID,
            'languageId' => self::LANGUAGE_ID,
        ));

        $this->service->saveUserTopics($command);

        $command = new SaveUserTopicsCommand(array(
            'selected' => array(2),
            'userId' => self::USER_ID,
            'languageId' => self::LANGUAGE_ID,
        ));

        $this->service->saveUserTopics($command);

        $topics = $this->service->getTopics(self::USER_ID);
        $this->assertEquals(1, count($topics));
        $this->assertEquals('topic2', $topics[0]->getView()->name);
    }

    public function testSaveUserTopicsCommandUpdate()
    {
        $this->em->persist(new Topic(1, $this->language, 'topic1'));
        $this->em->persist(new Topic(2, $this->language, 'topic2'));
        $this->em->flush();

        $command = new SaveUserTopicsCommand(array(
            'selected' => array(1),
            'userId' => self::USER_ID,
            'languageId' => self::LANGUAGE_ID,
        ));

        $this->service->saveUserTopics($command);

        $this->assertEquals(1, count($this->service->getTopics(self::USER_ID)));

        $command = new SaveUserTopicsCommand(array(
            'topics' => array(2),
            'selected' => array(2),
            'userId' => self::USER_ID,
            'languageId' => self::LANGUAGE_ID,
        ));

        $this->service->saveUserTopics($command);

        $this->assertEquals(2, count($this->service->getTopics(self::USER_ID)));
    }

    public function testSaveUserTopicsCommandUpdateTwice()
    {
        $this->em->persist(new Topic(1, $this->language, 'topic'));

        $command = new SaveUserTopicsCommand(array(
            'topics' => array(1),
            'selected' => array(1),
            'userId' => self::USER_ID,
            'languageId' => self::LANGUAGE_ID,
        ));

        $this->service->saveUserTopics($command);
        $this->service->saveUserTopics($command);

        $topics = $this->service->getTopics(self::USER_ID);
        $this->assertEquals(1, count($topics));
    }
}
